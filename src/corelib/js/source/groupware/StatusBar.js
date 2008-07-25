Ext.namespace('de.intrabuild.groupware');

de.intrabuild.groupware.StatusBar = function(){
    
    var _messageBroadcaster = Ext.ux.util.MessageBus;
    
    var _statusBar = null;
    
    var _progressBar = null;
    
    var _activeRequestCount = 0;
    
    var _connectionInfo = null;
    
    var _onBeforeRequest = function()
    {
        if (_activeRequestCount == 0) {
            _progressBar.show();
            _progressBar.wait({interval : 250}); 
            //_progressBar.updateText(_activeRequestCount); 
        }
        _activeRequestCount++;      
    };
    
    var _onRequestException = function()
    {
        _onRequestComplete();
    };
    
    var _onRequestAbort = function()
    {
        _onRequestComplete();
    };
    
    var _onRequestComplete = function()
    {
        _activeRequestCount = Math.max(0, --_activeRequestCount);
        //_progressBar.updateText(_activeRequestCount); 
        
        if (_activeRequestCount <= 0) {
            _progressBar.reset(true);
        }
    };      
    
    var _transceive = function(subject, message)
    {
        switch (subject) {
            case 'de.intrabuild.groupware.email.Letterman.beforeload':
                _statusBar.setStatus({
                    text : de.intrabuild.Gettext.gettext("Checking for new emails...")
                });
            break; 
            
            case 'de.intrabuild.groupware.email.Letterman.load':
			    var total = message.total;
				var text  = de.intrabuild.Gettext.gettext("No new emails");
				
				if (total > 0) {
					text = String.format(
                        de.intrabuild.Gettext.ngettext("One new email", "{0} new emails", total),
						total
					);
				}
                _statusBar.setStatus({
                    text  : text,
                    clear : true
                });
            break;
            
            case 'de.intrabuild.groupware.email.Letterman.loadexception':
                _statusBar.setStatus({
                    text  : de.intrabuild.Gettext.gettext("Error while trying to receive new emails"),
                    clear : true
                });
            break;   
            
        }
        
    };
    
    var _subscribe = function()
    {
        _messageBroadcaster.subscribe('de.intrabuild.groupware.email.Letterman.beforeload', _transceive);    
        _messageBroadcaster.subscribe('de.intrabuild.groupware.email.Letterman.load', _transceive);    
        _messageBroadcaster.subscribe('de.intrabuild.groupware.email.Letterman.loadexception', _transceive);    
    };
    
    return {
      
        getStatusBar : function()
        {
            if (_statusBar === null) {
                
                var pconf = {
                    cls   : 'de-intrabuild-groupware-ProgressBar',
                    width : 80
                };
                if (Ext.isIE) {
                    pconf.style = 'margin-top:1px;';
                }
                
                _progressBar = new Ext.ProgressBar(pconf);
                
                var t = document.createElement('div');
                t.innerHTML = '&#160;';
                t.className = "de-intrabuild-groupware-statusbar-ConnectionInfo";
                _connectionInfo = new Ext.Toolbar.Item(t);
                
                _statusBar = new Ext.StatusBar({
                    region:'south',
                    height : 21,
                    defaultText : de.intrabuild.Gettext.gettext("Ready"),
                    border : false,
                    id : 'de.intrabuild.groupware.StatusBar',
                    cls: 'de-intrabuild-groupware-StatusBar',
                    statusAlign: 'left',
                    margins:'3 0 0 0',
                    items : [
                        new Ext.Toolbar.Separator(),
                        new Ext.Toolbar.Spacer(),
                        _progressBar,
                        new Ext.Toolbar.Spacer(),
                        new Ext.Toolbar.Separator(),
                        new Ext.Toolbar.Spacer(),
                        _connectionInfo,
                        new Ext.Toolbar.Spacer()
                    ]
                }); 
                
                _statusBar.afterRender = _statusBar.afterRender.createSequence(
                    function() {
                        _connectionInfo.disable();
                    }
                );
            
                var eao = Ext.Ajax;
                eao.on('beforerequest',    _onBeforeRequest);
                eao.on('requestcomplete',  _onRequestComplete);
                eao.on('requestexception', _onRequestException);
                _messageBroadcaster.subscribe(
                    'ext.lib.ajax.abort',
                    _onRequestAbort
                );
                
                _subscribe();
            }          
            
            
            
            return _statusBar;  
        }
        
        
    };    
    
}();