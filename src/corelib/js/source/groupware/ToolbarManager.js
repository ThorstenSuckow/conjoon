Ext.namespace('de.intrabuild.groupware');

de.intrabuild.groupware.ToolbarManager = function(){
    
    var contId     = "DOM:de.intrabuild.groupware.Toolbar.controls";
    var toolbars   = [];
    var actVisible = null;
    
    return {

		get : function(id)
		{
            if (toolbars[id]) {
                return toolbars[id];
            }
            
            return null;
		},

        register : function(id, element)
        {
            element.addClass('de-intrabuild-groupware-Toolbar');
            toolbars[id] = element;
        },
        
        disable : function(id, disable)
        {
            if (toolbars[id]) {
                toolbars[id].setDisabled(disable);
            }
        },        
        
        hide : function(id)
        {
            if (toolbars[id]) {
                toolbars[id].hide();
            }
            if (id == actVisible) {
                actVisible = null;
            }
        },
        
        destroy : function(id)
        {
            if (toolbars[id]) {
                toolbars[id].hide();
                toolbars[id].destroy();
            }
            
            delete toolbars[id];
            
            if (id == actVisible) {
                actVisible = null;
            }
        },
        
        show : function(id)
        {
            if (!toolbars[id] || actVisible == id) {
                return;
            }
           
            this.hide(actVisible);
            
            if (!toolbars[id].rendered) {
                toolbars[id].render(contId);
            } else {
                toolbars[id].show();
            }
            
            actVisible = id;
        }
        
    };
}();