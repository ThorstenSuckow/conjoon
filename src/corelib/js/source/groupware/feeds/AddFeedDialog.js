/**
 * conjoon
 * (c) 2007-2015 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */

Ext.namespace('com.conjoon.groupware.feeds');

com.conjoon.groupware.feeds.AddFeedDialog = Ext.extend(Ext.Window, {

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
    loadMaskConfig : null,

    /**
     * Configuration for the loadMask on checking for a valid feedname
     */
    loadMaskConfigFeedName : null,

    /**
     * Configuration for the loadMask while saving the new feed
     */
    loadMaskConfigSaveFeed : null,

    /**
     * The panel for displaying error messages if subscribing to a feed
     * fails in any way (malformed url, network issues etc.)
     * @param {Ext.Panel}
     */
    errorPanel : null,

    /**
     * The trigger field for submitting the feed's url.
     * @param {Ext.form.TriggerField}
     */
    urlTrigger : null,

    /**
     * Textfield for submitting a custom name for identifying the feed later on.
     */
    feedNameTextField : null,

    /**
     * Combobox for choosing the request timeout in seconds.
     *
     * @type Ext.form.ComboBox
     */
    requestTimeoutComboBox : null,

    /**
     * Combobox to store the duration of how long to store the entries before
     * wiped in the DB.
     */
    keepEntriesComboBox : null,

    /**
     * Combobox to store update/refresh behavior.
     */
    updateComboBox : null,

    /**
     * Checkbox telling wether to close the dialog after a feed was successfully
     * added. If checked, the dialog won't close and stay in input mode.
     */
    keepAddModeCheckbox : null,


    /**
     * The button for submitting the dialogs form.
     */
    okButton : null,

    /**
     * The button for resetting the dialogs form and starting a new input
     * procedure.
     */
    resetButton : null,

    /**
     * Cancels and closes the dialog.
     */
    cancelButton : null,

    /**
     * The card layout either displaying additional form fields or the panel
     * showing error messages.
     * @param {Ext.Panel}
     */
    card : null,

    initComponent : function()
    {
        this.loadMaskConfig = {
            msg : com.conjoon.Gettext.gettext("Validating feed url...")
        };

        this.loadMaskConfigFeedName = {
            msg : com.conjoon.Gettext.gettext("Validating feed name...")
        };

        this.loadMaskConfigSaveFeed = {
            msg : com.conjoon.Gettext.gettext("Saving...")
        };

        this.errorPanel = new Ext.Panel({
            columnWidth : .82,
            height      : 130,
            autoScroll  : true,
            html        : ""
        });

        this.urlTrigger = new Ext.form.TriggerField({
            fieldLabel   : com.conjoon.Gettext.gettext("Feed url"),
            vtype        : 'url',
            //allowBlank   : false,
            triggerClass : 'com-conjoon-go-trigger',
            anchor       : '100%'
        });

        this.feedNameTextField = new Ext.form.TextField({
            fieldLabel   : com.conjoon.Gettext.gettext("Feed name"),
            itemCls      : 'com-conjoon-margin-b-10',
            allowBlank   : false,
            anchor       : '95%',
            validator    : function(v){
                               var alphanum = /^[a-zA-Z0-9_\-()\/ :.]+$/;
                               return alphanum.test(v);
                           }
        });

        this.requestTimeoutComboBox = new Ext.form.ComboBox({
            tpl            : '<tpl for="."><div class="x-combo-list-item">{text:htmlEncode}</div></tpl>',
            fieldLabel     : com.conjoon.Gettext.gettext("Request timeout"),
            listClass      : 'com-conjoon-smalleditor',
            displayField   : 'text',
            itemCls        : 'com-conjoon-margin-b-10',
            valueField     : 'id',
            mode           : 'local',
            value          : 10,
            anchor         : '95%',
            editable       : false,
            triggerAction  : 'all',
            store          : new Ext.data.SimpleStore({
                data   : [
                    [10, com.conjoon.Gettext.gettext("10 seconds")],
                    [20, com.conjoon.Gettext.gettext("20 seconds")],
                    [30, com.conjoon.Gettext.gettext("30 seconds")]
                ],
                fields : ['id', 'text']
            })
        });

        this.keepEntriesComboBox = new Ext.form.ComboBox({
            tpl            : '<tpl for="."><div class="x-combo-list-item">{text:htmlEncode}</div></tpl>',
            fieldLabel     : com.conjoon.Gettext.gettext("Save entries"),
            listClass      : 'com-conjoon-smalleditor',
            itemCls        : 'com-conjoon-margin-b-10',
            displayField   : 'text',
            valueField     : 'id',
            mode           : 'local',
            anchor         : '95%',
            value          : 86400,
            editable       : false,
            triggerAction  : 'all',
            store          : new Ext.data.SimpleStore({
                data   : [
                    [3600,    com.conjoon.Gettext.gettext("for one hour")],
                    [7200,    com.conjoon.Gettext.gettext("for 2 hours")],
                    [21600,   com.conjoon.Gettext.gettext("for 6 hours")],
                    [43200,   com.conjoon.Gettext.gettext("for 12 hours")],
                    [86400,   com.conjoon.Gettext.gettext("for one day")],
                    [172800,  com.conjoon.Gettext.gettext("for 2 days")],
                    [432000,  com.conjoon.Gettext.gettext("for 5 days")],
                    [1209600, com.conjoon.Gettext.gettext("for one week")],
                    [2419200, com.conjoon.Gettext.gettext("for 2 weeks")]
                ],
                fields : ['id', 'text']
            })
        });


        /**
         * @ext-bug beta 1 not setting listWidth renders not correct
         */
        this.updateComboBox = new Ext.form.ComboBox({
            tpl            : '<tpl for="."><div class="x-combo-list-item">{text:htmlEncode}</div></tpl>',
            fieldLabel     : com.conjoon.Gettext.gettext("Refresh"),
            listClass      : 'com-conjoon-smalleditor',
            itemCls        : 'com-conjoon-margin-b-10',
            displayField   : 'text',
            valueField     : 'id',
            mode           : 'local',
            anchor         : '95%',
            editable       : false,
            triggerAction  : 'all',
            store          : new Ext.data.SimpleStore({
                data   : [
                    [900,    com.conjoon.Gettext.gettext("every 15 minutes")],
                    [1800,   com.conjoon.Gettext.gettext("every 30 minutes")],
                    [3600,   com.conjoon.Gettext.gettext("every hour")],
                    [7200,   com.conjoon.Gettext.gettext("every 2 hours")],
                    [21600,  com.conjoon.Gettext.gettext("every 6 hours")],
                    [43200,  com.conjoon.Gettext.gettext("every 12 hours")],
                    [86400,  com.conjoon.Gettext.gettext("every day")],
                    [172800, com.conjoon.Gettext.gettext("every 2 days")]
                ],
                fields : ['id', 'text']
            })
        });


        /**
         * @ext-bug beta 1 using hideLabel renders causes jumpy checkbox
         */
        this.keepAddModeCheckbox = new Ext.form.Checkbox({
            fieldLabel : '&#160',
            labelSeparator : '',
            boxLabel   : com.conjoon.Gettext.gettext("add another feed after saving"),
            ctCls      : 'com-conjoon-smalleditor',
            anchor     : '95%',
            autoWidth  : true
            //style      : 'margin-left:105px'
        });

        /**
         * @ext-bug something breaks a reference to button.el if using the button and
         *          its return value
         */
        this.okButton = new Ext.Button({
            text     : com.conjoon.Gettext.gettext("Add"),
            disabled : true,
            tooltip  : com.conjoon.Gettext.gettext("Saves and adds the configuration to the feed reader"),
            handler  : this.onOk,
            scope    : this,
            minWidth : 75
        });

        this.resetButton = new Ext.Button({
            text    : com.conjoon.Gettext.gettext("Reset"),
            handler : this.onReset,
            scope   : this,
            tooltip : com.conjoon.Gettext.gettext("Cancels and resets all fields"),
            handler : this.onReset,
            scope   : this,
            minWidth : 75
        });

        this.cancelButton = new Ext.Button({
            text    : com.conjoon.Gettext.gettext("Cancel"),
            tooltip : com.conjoon.Gettext.gettext("Cancels and closes this dialog"),
            handler : this.onCancel,
            scope   : this,
            minWidth : 75
        });

        this.card = new Ext.Panel({
           region     : 'south',
            height   : 170,
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
                id        : 'DOM:com.conjoon.groupware.feeds.AddFeedDialog.errorPanel',
                layout    : 'column',
                defaults  : {
                    bodyStyle : 'background:none;padding-top:10px;',
                    border    : false
                },
                items     : [{
                    columnWidth : .18,
                    html        : '<div class="com-conjoon-groupware-feeds-AddFeedDialog-errorCard-imagePanel">&nbsp;</div>'
                  },
                    this.errorPanel
                ]
              },{
                hideMode : 'visibility',
                  // form card
                id    : 'DOM:com.conjoon.groupware.feeds.AddFeedDialog.additionalFormPanel',
                items : new Ext.FormPanel({
                    labelAlign : 'left',
                    border     : false,
                    baseCls    : 'x-small-editor',
                    cls        : 'com-conjoon-groupware-feeds-AddFeedDialog-formCard',
                    defaults   : {
                        labelStyle : 'font-size:11px'
                    },
                    items : [
                        this.feedNameTextField,
                        this.updateComboBox,
                        this.keepEntriesComboBox,
                        this.requestTimeoutComboBox,
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
                new com.conjoon.groupware.util.FormIntro({
                    style      : 'padding:10px 10px 0px 10px;',
                    labelText  : com.conjoon.Gettext.gettext("Feed address"),
                    imageClass : 'com-conjoon-groupware-feeds-AddFeedDialog-introImage',
                    text       : com.conjoon.Gettext.gettext("Enter the url of the feed you want to import, starting with \"http://\". Press the button next to the input field when you are finished.")
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
                        cls : 'com-conjoon-groupware-util-FormIntro-sepx',
                        html : '&#160;'
                    }
                })
             ]
          },
            this.card
        ];

        Ext.apply(this,  {
            iconCls   : 'com-conjoon-groupware-feeds-Icon',
            title     : com.conjoon.Gettext.gettext("Add feed"),
            bodyStyle : 'background-color:#F6F6F6',
            modal     : true,
            height    : 355,
            width     : 450,
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

        this.urlTrigger.onTriggerClick = this.onTriggerClick.createDelegate(this);

        // finish other components
        this.keepEntriesComboBox.setValue(2419200);
        this.updateComboBox.setValue(172800);
        this.requestTimeoutComboBox.setValue(10);

        com.conjoon.groupware.feeds.AddFeedDialog.superclass.initComponent.call(this);
    },

    initEvents : function()
    {
        com.conjoon.groupware.feeds.AddFeedDialog.superclass.initEvents.call(this);

        this.mon(this.feedNameTextField, 'valid',   this.onValid, this);
        this.mon(this.feedNameTextField, 'invalid', this.onInvalid, this);

        this.on('beforeclose', this.onBeforeClose, this);
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

        var store = com.conjoon.groupware.feeds.AccountStore.getInstance();
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
                title : com.conjoon.Gettext.gettext("Feed name aready existing"),
                msg : String.format(com.conjoon.Gettext.gettext("The feed name \"{0}\" does already exist. Please chose another one. If you have previously removed a feed with the same name, please apply the changes first."), Ext.util.Format.htmlEncode(this.feedNameTextField.getValue())),
                buttons: msg.OK,
                icon: msg.INFO,
                animateTarget : this.feedNameTextField.el.dom.id,
                cls :'com-conjoon-msgbox-info',
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
            url    : './groupware/feeds.account/add.feed/format/json',
            params : {
                name           : this.feedNameTextField.getValue().trim(),
                uri            : this.urlTrigger.getValue().trim(),
                updateInterval : this.updateComboBox.getValue(),
                deleteInterval : this.keepEntriesComboBox.getValue(),
                requestTimeout : this.requestTimeoutComboBox.getValue()
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
        var json = com.conjoon.util.Json;
        var msg  = Ext.MessageBox;

        this.loadMask.hide();

        var source = response.responseText;

        this._disableControls(false);

        if (json.isError(source)) {
            this.onFeedFailure(response, parameters);
            return;
        }

        var convertTo   = com.conjoon.util.Record.convertTo;

        var responseValues = json.getResponseValues(source);
        var account = responseValues['account'];
        var items = responseValues['items'];
        var rec = convertTo(com.conjoon.groupware.feeds.AccountRecord, account, account.id);
        com.conjoon.groupware.feeds.AccountStore.getInstance().add(rec);

        var store = com.conjoon.groupware.feeds.FeedStore.getInstance();
        var item = null;
        var recordClass = com.conjoon.groupware.feeds.ItemRecord;
        for (var i = 0, len = items.length; i < len; i++) {
            item = items[i];
            store.addSorted(convertTo(recordClass, item, item.id));
        }

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
        this.requestId = null;

        this.loadMask.hide();

        com.conjoon.groupware.ResponseInspector.handleFailure(response);

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
            url            : './groupware/feeds.account/is.feed.address.valid/format/json',
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

        var json = com.conjoon.util.Json;
        var msg  = Ext.MessageBox;

        var responseValues = json.getResponseValues(response.responseText);

        this.requestId = null;

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
        this.requestId = null;
        com.conjoon.groupware.ResponseInspector.handleFailure(response);
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
                message = com.conjoon.Gettext.gettext("Error:<br />Please enter a feed url.");
            break;

            case this.ERROR_NO_VALID_URL:
                message = String.format(
                    com.conjoon.Gettext.gettext("Error:<br />The url \"{0}\" does not seem to be valid feed url."),
                    url
                );
            break;

            case this.ERROR_FEED:
                message = String.format(
                    com.conjoon.Gettext.gettext("Error:<br />The url \"{0}\" does not seem to contain a feed resource."),
                    url
                );
            break;

            case this.ERROR_SERVER:
                message = String.format(
                    com.conjoon.Gettext.gettext("Error:<br />An unexpected error occured. The response was:<br />{0}"),
                    addMessage
                );
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
        this.keepEntriesComboBox.setValue(86400);
        this.updateComboBox.setValue(3600);
        this.requestTimeoutComboBox.setValue(10);

        this.urlTrigger.setDisabled(false);
        this.okButton.setDisabled(true);
        this.tools['close'].unmask();
        this.cancelButton.setDisabled(false);

        this.card.layout.setActiveItem(this.EMPTY_PANEL);
        this.urlTrigger.reset();

        this.requestId = null;
    }

});
