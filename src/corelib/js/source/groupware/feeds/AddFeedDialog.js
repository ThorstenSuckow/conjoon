/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author$
 * $Id$
 * $Date$ 
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$ 
 */

Ext.namespace('de.intrabuild.groupware.feeds');

de.intrabuild.groupware.feeds.AddFeedDialog = function(config) {
    
    Ext.apply(this, config);

    /**
     * The panel for displaying error messages if subscribing to a feed
     * fails in any way (malformed url, network issues etc.)
     * @param {Ext.Panel}
     */
    this.errorPanel = new Ext.Panel({
        columnWidth : .82, 
        height      : 130, 
        autoScroll  : true,
        html        : ""
    });
    
    /**
     * The trigger field for submitting the feed's url.
     * @param {Ext.form.TriggerField}
     */
    this.urlTrigger = new Ext.form.TriggerField({
        fieldLabel   : 'Feed-Url',
        vtype        : 'url',
        //allowBlank   : false,
        triggerClass : 'de-intrabuild-go-trigger',
        anchor       : '100%'  
    }); 
    
    /**
     * Textfield for submitting a custom name for identifying the feed later on.
     */
    this.feedNameTextField = new Ext.form.TextField({
        fieldLabel   : 'Feed-Name',
        itemCls      : 'de-intrabuild-margin-b-10',
        allowBlank   : false,
        width        : 202,
        validator    : function(v){
                           var alphanum = /^[a-zA-Z0-9_()\/ :.]+$/;
                           return alphanum.test(v);
                       }
    });
    
    /**
     * Combobox to store the duration of how long to store the entries before
     * wiped in the DB.
     */
    this.keepEntriesComboBox = new Ext.form.ComboBox({
        tpl           : '<tpl for="."><div class="x-combo-list-item">{text:htmlEncode}</div></tpl>',
        fieldLabel    : 'Speichere Eintr&auml;ge',
        listClass     : 'de-intrabuild-smalleditor',
        itemCls       : 'de-intrabuild-margin-b-15',
        displayField  : 'text',
        valueField    : 'id',
        mode          : 'local',
        width         : 185,
        listWidth     : 201,
        editable      : false,
        triggerAction : 'all',
        store         : new Ext.data.SimpleStore({
            data   : [
                [2419200, "für 2 Wochen"],
                [1209600, "für eine Woche"],
                [432000, "für 5 Tage"],
                [172800, "für 2 Tage"],
                [86400, "für einen Tag"],
                [43200, "für 12 Stunden"],
                [21600, "für 6 Stunden"],
                [7200, "für 2 Stunden"],
                [3600, "für eine Stunde"]
            ],
            fields : ['id', 'text']
        })
    });
    
    
    /**
     * @ext-bug beta 1 not setting listWidth renders not correct
     */
    /**
     * Combobox to store update/refresh behavior.
     */
    this.updateComboBox = new Ext.form.ComboBox({
        tpl           : '<tpl for="."><div class="x-combo-list-item">{text:htmlEncode}</div></tpl>',
        fieldLabel    : 'Aktualisiere',
        listClass     : 'de-intrabuild-smalleditor',
        itemCls       : 'de-intrabuild-margin-b-10',
        displayField  : 'text',
        valueField    : 'id',
        mode          : 'local',
        width         : 185,
        listWidth     : 201,
        editable      : false,
        triggerAction : 'all',
        store         : new Ext.data.SimpleStore({
            data   : [
                [172800, "alle 2 Tage"],
                [86400, "jeden Tag"],
                [43200, "alle 12 Stunden"],
                [21600, "alle 6 Stunden"],
                [7200, "alle 2 Stunden"],
                [3600, "jede Stunde"],
                [1800, "alle 30 Minuten"],
                [900, "alle 15 Minuten"]
            ],
            fields : ['id', 'text']
        }) 
    });    
    
    
    /**
     * @ext-bug beta 1 using hideLabel renders causes jumpy checkbox 
     */    
    /**
     * Checkbox telling wether to close the dialog after a feed was successfully
     * added. If checked, the dialog won't close and stay in input mode.
     */
    this.keepAddModeCheckbox = new Ext.form.Checkbox({
        fieldLabel : '&#160',
        labelSeparator : '',
        boxLabel   : 'nach Speichern Eingabe fortsetzen',
        ctCls      : 'de-intrabuild-smalleditor',
        width      : 'auto',
        autoWidth  : true
        //style      : 'margin-left:105px'
    });
    
    /**
     * @ext-bug something breaks a reference to button.el if using the button and
     *          its return value
     */
    /**
     * The button for submitting the dialogs form.
     */
    this.okButton = new Ext.Button({
        text     : 'Hinzuf&uuml;gen',
        disabled : true,
        tooltip  : 'Speichert und f&uuml;gt die Angaben zum Feed-Reader hinzu',
		handler  : this.onOk,
		scope    : this
    });       
    
    /**
     * The button for resetting the dialogs form and starting a new input
     * procedure.
     */
    this.resetButton = new Ext.Button({
		text    : 'Zur&uuml;cksetzen',
		handler : this.onReset,
		scope   : this,
		tooltip : 'Bricht die Eingabe ab und setzt die Felder zur&uuml;ck',
		handler : this.onReset,
		scope   : this
	});

    /**
     * Cancels and closes the dialog.
     */
    this.cancelButton = new Ext.Button({
		text    : 'Abbrechen',
		tooltip : 'Bricht die Eingabe ab und schlie&szlig;t den Dialog',
		handler : this.onCancel,
		scope   : this
	});        
    
    /**
     * The card layout either displaying additional form fields or the panel
     * showing error messages.
     * @param {Ext.Panel}
     */    
    this.card = new Ext.Panel({
       region     : 'south',  
        height   : 140,
        border         : false,
        deferredRender : true,
        bodyStyle      : 'background-color:#F6F6F6;padding:0px 10px 5px 10px',
        layout         : 'card',
        activeItem    : 0,
        defaults      : {
            border    : false,
            bodyStyle : 'background:none'
        },
        items : [{
            // empty card
            html : ""    
          },{
            // error card
            id        : 'DOM:de.intrabuild.groupware.feeds.AddFeedDialog.errorPanel',
            layout    : 'column',
            defaults  : {
                bodyStyle : 'background:none;padding-top:10px;',
                border    : false
            },
            items     : [{
                columnWidth : .18,
                html        : '<div class="de-intrabuild-groupware-feeds-AddFeedDialog-errorCard-imagePanel">&nbsp;</div>'
              },
                this.errorPanel
            ]
          },{
              // form card
            id    : 'DOM:de.intrabuild.groupware.feeds.AddFeedDialog.additionalFormPanel',
            items : new Ext.FormPanel({
                labelAlign : 'left',
                border     : false,
                baseCls    : 'x-small-editor',
                cls        : 'de-intrabuild-groupware-feeds-AddFeedDialog-formCard',
                defaults   : {
                    labelStyle : 'font-size:11px'
                },
                items : [
                    this.feedNameTextField,
                    this.updateComboBox,
                    this.keepEntriesComboBox,
                    this.keepAddModeCheckbox   
                ]
            })                  
        }]
    });    
    
    this.items = [{
        region    : 'center',  
        height    : 120, 
        border    : false,
        items     : [
            new de.intrabuild.groupware.util.FormIntro({
				style      : 'padding:10px 10px 0px 10px;',
				label      : "Feed-Adresse",
				imageClass : 'de-intrabuild-groupware-feeds-AddFeedDialog-introImage',
				text       : "Geben Sie die URL (beginnend mit \"http://\") des Feeds an, den Sie importieren m&ouml;chten, und klicken Sie danach auf den Button rechts vom Eingabefeld."        
            }),  
            new Ext.FormPanel({
                labelAlign : 'right',
                border     : false,
                hideLabels : true,
                cls        : 'x-small-editor',
                bodyStyle  : 'background:none;padding:0 10px 0 68px',
                items      : [
                    this.urlTrigger 
                ]
            }),
			new Ext.BoxComponent({
				style  : 'margin:0px 10px 0 10px',
				autoEl : {
					tag : 'div',
					cls : 'de-intrabuild-groupware-util-FormIntro-sepx',
					html : '&#160;'
			    }
			}) 
         ]
      },
        this.card  
    ];
    
    de.intrabuild.groupware.feeds.AddFeedDialog.superclass.constructor.call(this,  {
        iconCls   : 'de-intrabuild-groupware-feeds-Icon', 
        title     : 'Feed hinzuf&uuml;gen',
        bodyStyle : 'background-color:#F6F6F6',
        modal     : true,
        height    : 325,
        width     : 400,
        defaults  : {
            bodyStyle : 'background:none;'    
        },
        resizable : false,
        /**
         * @ext-bug beta1 not setting the layout mode to border does not render 
         *                the form intro right, i.e. text not displayed when the
         *                dialog reopens or when it is showed over an already modal
         *                window
         */
        layout    : 'border',
		buttons : [
            this.okButton,
			this.resetButton,
			this.cancelButton 
		]
    });

   /* this.animContainer = Ext.DomHelper.append(this.el, {
                        id:'DOM:de.intrabuild.groupware.feeds.FeedPreview.container',
                        style:"height:120px;background-color:red"
                     }, true);*/
    
    // install listeners
    this.urlTrigger.onTriggerClick = this.onTriggerClick.createDelegate(this);

    
    // finish other components
    this.keepEntriesComboBox.setValue(2419200);
    this.updateComboBox.setValue(172800);
    this.feedNameTextField.on('valid',   this.onValid, this);
    this.feedNameTextField.on('invalid', this.onInvalid, this);

    this.on('beforeclose', this.onBeforeClose, this);
};

Ext.extend(de.intrabuild.groupware.feeds.AddFeedDialog, Ext.Window, {

    EMPTY_PANEL : 0,
    ERROR_PANEL : 1,
    FORM_PANEL  : 2,
    
    ERROR_NO_URL       : 0,
    ERROR_NO_VALID_URL : 1,
    ERROR_FEED         : 2,
    ERROR_SERVER       : 3,
    
    /**
     * The request ID as being returned from any made Ajax request.
     * @param {Number}
     */
    requestId : null,
    
    /**
     * Load Mask being used for indicating server requests.
     */
    loadMask : null,
    
    /**
     * Stores the number of feeds that have been added during the dialog's
     * session.
     */
    feedCount : 0,
    
    /**
     * Configuration for the loadMask
     */
    loadMaskConfig : {
        msg : '&Uuml;berpr&uuml;fe Adresse...'    
    },

    /**
     * Configuration for the loadMask on checking for a valid feedname
     */
    loadMaskConfigFeedName : {
        msg : '&Uuml;berpr&uuml;fe Feed-Name...'    
    },    
    
    /**
     * Configuration for the loadMask while saving the new feed
     */
    loadMaskConfigSaveFeed : {
        msg : 'Speichere Daten...'    
    },        
    
    /**
     * Listener for the feed name textfield.
     */
    onInvalid : function()
    {
        this.okButton.setDisabled(true);
    },    
    
    /**
     * Listener for the feed name textfield.
     */
    onValid : function()
    {
        this.okButton.setDisabled(false);
    },
    
    /**
     * Callback for the "Add"-Button.
     */
    onOk : function()
    {
        var value = this.feedNameTextField.getValue().trim();
        
        // last check if the value in the field was valid
        if (value == "") {
            this.feedNameTextField.reset();
            this.feedNameTextField.markInvalid();
            this.okButton.setDisabled(true);
            return;
        }
        
        var store = de.intrabuild.util.Registry.get('de.intrabuild.groupware.feeds.AccountStore')
        var recs = store.getRange();
        var tmpValue = value.toLowerCase();
        var index = -1;
        for (var i = 0, len = recs.length; i < len; i++) {
        	if (recs[i].get('name').toLowerCase() == tmpValue) {
        		index = i;
        		break;
        	}
        }
		
        if (index != -1) {
            var msg = Ext.MessageBox;
            msg.show({
                title : "Doppelter Feed-Name",
                msg : "Der von Ihnen angegebene Feed-Name \""+Ext.util.Format.htmlEncode(this.feedNameTextField.getValue())+"\" ist bereits vorhanden. Bitte w&auml;hlen Sie einen anderen.",
                buttons: msg.OK,
                icon: msg.INFO,
                animateTarget : this.feedNameTextField.el.dom.id,
                cls :'de-intrabuild-msgbox-info',
                width:400
            });
            return;
        }
       
        // if we have made it to here, save the new feed
        this.saveFeed();     
    },
    
    _disableControls : function(disable)
	{
        this.okButton.setDisabled(disable);  
		this.resetButton.setDisabled(disable);
		this.cancelButton.setDisabled(disable);
		
        if (disable) {
            this.tools['close'].mask();           
        } else {
            this.tools['close'].unmask();
        }		
	},
	
    /**
     * Listener for a beforeclose operation.
     * Will return false if there is currently an ajax-request being made, otherwise
     * true.
     *
     * @return {Boolean} true to allow close operation, otherwise false
     */ 
    onBeforeClose : function()
    {
        if (this.requestId !== null) {
            return false;   
        } else {
            return true;
        }
        
    },  	
   
    /**
     * Saves the newly added feed.
     */
    saveFeed : function()
    {
        this.loadMask.msg = this.loadMaskConfigSaveFeed.msg;
        
        this.loadMask.show(); 
        
        this._disableControls(true);
        
        this.requestId = Ext.Ajax.request({
            url    : '/groupware/feeds/add.feed/format/json',
            params : {
                name           : this.feedNameTextField.getValue().trim(), 
                uri            : this.urlTrigger.getValue().trim(),
                updateInterval : this.updateComboBox.getValue(),
                deleteInterval : this.keepEntriesComboBox.getValue()
            },
            success        : this.onFeedSaveSuccess, 
            failure        : this.onFeedFailure,
            scope          : this,
            disableCaching : true
        });   
        
        
        
    },
    
    /**
     * Called when saveFeed finishs without a failure.
     * Fires the 'add' event if adding the feed was successfull.
     */ 
    onFeedSaveSuccess : function(response, parameters)
    {
        var json = de.intrabuild.util.Json;
        var msg  = Ext.MessageBox;
        
        this.loadMask.hide();  
        
        var source = response.responseText;
        
        this._disableControls(false);
        
        if (json.isError(source)) {
            this.onFeedFailure(response, parameters);
            return;
        }   
        
        var convertTo   = de.intrabuild.util.Record.convertTo; 
        
        var responseValues = json.getResponseValues(source);
        var account = responseValues['account'];
        var items = responseValues['items'];
        var rec = convertTo(de.intrabuild.groupware.feeds.AccountRecord, account, account.id);
        de.intrabuild.util.Registry.get('de.intrabuild.groupware.feeds.AccountStore').add(rec);
        
        var store = de.intrabuild.util.Registry.get('de.intrabuild.groupware.feeds.FeedStore');
        var recs = [];
        var item = null;
        var recordClass = de.intrabuild.groupware.feeds.ItemRecord;
        for (var i = 0, len = items.length; i < len; i++) {
            item = items[i];
            recs.push(convertTo(recordClass, item, item.id));
        }
        store.add(recs);
       
	    this.reset();
		
        if (this.keepAddModeCheckbox.getValue() !== true) {
            this.close();
        }
    },
    
    /**
     * Callback after requesting feedname check or saving feed data
     * failed due to networking problems or else.
     *
     * @param {XmlHttpRequest} The response from the server
     * @param {object} The original submitted parameters
     */
    onFeedFailure : function(response, parameters)
    {
        this.loadMask.hide(); 
        
        de.intrabuild.groupware.ResponseInspector.handleFailure(response);    
        
        this._disableControls(false);    
    },
    
    /**
     * Callback for the "Reset"-Button.
     */
    onReset : function()
    {
        this.reset();
    },
    
    /**
     * Callback for the "Cancel"-Button.
     */
    onCancel : function()
    {
        this.close();
    },
    
    /**
     * Listener for the urlTriggerField. Actions will only be started if the
     * value equals to a valid url.
     * If the value of the trigger field equals to a valid url, the body of the
     * body-panel will be masked with a loading indicator and <tt>checkValidFedd</tt>
     * will be called, requesting the server to check if any feed is available 
     * from the server.
     * 
     */
    onTriggerClick : function()
    {
        if (this.urlTrigger.disabled) {
            return;
        }
        
        var value = Ext.util.Format.stripTags(this.urlTrigger.getValue().trim());
        
        if (value == "") {
            this.displayError(this.ERROR_NO_URL, value);
            return;
        } else if (!this.urlTrigger.isValid()) {    
            this.displayError(this.ERROR_NO_VALID_URL, value);
            return;
        }
        
        this.checkValidFeed(value);
    },
    
    /**
     * Checks if the passed url represents a valid feed location. The window 
     * will be masked with a loading indicator while the request is being 
     * processed by the server.
     * If any successfull response is being retrieved from the server, the
     * method <tt>onSuccess</tt> is called. This method then will check the response
     * and tell if the url was valid. 
     * If the server does not respond or any error occures in the scope of
     * communicating towards the server, the method <tt>onFailure</tt> will
     * be called.
     *
     * @param {String}
     */
    checkValidFeed : function(url)
    {
        if (this.loadMask == null) {
            this.loadMask = new Ext.LoadMask(this.body, this.loadMaskConfig);
        } else {
            // may have been reset by onOk (msg changed)
            this.loadMask.msg = this.loadMaskConfig.msg;
        }
        
		this.tools['close'].mask();
		this.cancelButton.setDisabled(true);
		
        this.card.layout.setActiveItem(this.EMPTY_PANEL);
        this.loadMask.show();
        
        this.requestId = Ext.Ajax.request({
            url            : '/groupware/feeds/is.feed.address.valid/format/json',
            params         : {uri : url},
            success        : this.onSuccess, 
            failure        : this.onFailure,
            scope          : this,
            disableCaching : true
        });
        
    },
    
    /**
     * Called when the Ajax request started in <tt>checkValidFeed</tt> returns
     * a successfull response. A successfull response does not mean yet, that
     * the passed url was a valid feed address, but the server processed the 
     * request.
     *
     * @param {XMLHttpRequest} The response
     * @param {Object} The passed parameters to the request
     */
    onSuccess : function(response, orgParameters)
    {
        this.loadMask.hide();    
        this.tools['close'].unmask();
        this.cancelButton.setDisabled(false);
		
        // status, i.e. 200
        // statusText, i.e OK
        // responseText, ie JSON OR no JSON (then something ugly happend.
        
        var json = de.intrabuild.util.Json;
        var msg  = Ext.MessageBox;
        
        var responseValues = json.getResponseValues(response.responseText);
        
        if (responseValues['success'] !== true) {
            this.displayError(this.ERROR_FEED, orgParameters.params.uri);   
            return;                      
        }
        
        // no error, feed was valid
        this.urlTrigger.setDisabled(true);
        
        this.card.layout.setActiveItem(this.FORM_PANEL);
        
    },

    /**
     * Called if communicating with the server failed in any way (network 
     * issues etc).
     *
     * @param {XMLHttpRequest} The response
     * @param {Object} The passed parameters to the request
     */
    onFailure : function(response, orgParameters)
    {
        this.loadMask.hide();  
        this.tools['close'].unmask();
        this.cancelButton.setDisabled(false);
		
		de.intrabuild.groupware.ResponseInspector.handleFailure(response);
    },    
    
    /**
     * Displays the error panel and shows the message associated with the
     * error.
     */
    displayError : function(code, url, addMessage)
    {
        var message = "";
        var url = Ext.util.Format.htmlEncode(url);
        
        switch (code) {
            case this.ERROR_NO_URL:
                message = "Fehler:<br />Bitte geben Sie eine Feed-Adresse an.";        
            break;
            
            case this.ERROR_NO_VALID_URL:
                message = "Fehler:<br />Die von Ihnen angegebene URL "+
                          "\""+url+"\" "+
                          "ist keine g&uuml;ltige Feed-Adresse.";        
            break;
            
            case this.ERROR_FEED:
                message = "Fehler:<br />An der angegebenen Adresse "+url+" konnte kein "+
                          "Feed ermittelt werden."
            break;
            
            case this.ERROR_SERVER:
                message = "Fehler:<br />Es ist ein allgemeiner Fehler aufgetreten. Der "+
                          "Response lieferte folgende Fehlerinformationen:<br />"+
                          addMessage;
            break;            
        }
        
        this.errorPanel.body.update(message);
        this.card.layout.setActiveItem(this.ERROR_PANEL);
    },
    
    /**
     * Resets all fields to null values, hides error/form panel, aborts the
     * checkValidFeed server request (if any) and hides the loading mask (if any).
     *
     * @return {Boolean} <tt>true</tt> to let the window close procedure continue, 
     *                   if used as callback
     */
    reset : function()
    {
        if (this.loadMask) {
            this.loadMask.hide();
        }
        
        if (this.requestId !== null) {
            Ext.Ajax.abort(this.requestId);
        }

        this.feedNameTextField.reset();
        this.keepEntriesComboBox.reset();
        this.updateComboBox.reset();
        
        this.urlTrigger.setDisabled(false);
        this.okButton.setDisabled(true);
		this.tools['close'].unmask();
        this.cancelButton.setDisabled(false);
        
        this.card.layout.setActiveItem(this.EMPTY_PANEL);
        this.urlTrigger.reset();
        
        this.requestId = null;
    }
    
});