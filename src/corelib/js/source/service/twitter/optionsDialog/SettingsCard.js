/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
 * licensing@conjoon.org
 *
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */

Ext.namespace('com.conjoon.service.twitter.optionsDialog');

/**
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 *
 * @class com.conjoon.service.twitter.optionsDialog.SettingsCard
 * @extends com.conjoon.cudgets.SettingsCard
 */
com.conjoon.service.twitter.optionsDialog.SettingsCard = Ext.extend(com.conjoon.cudgets.settings.Card, {

    /**
     * @cfg {com.conjoon.cudgets.SettingsContainer} settingsContainer The
     * settingsContainer that controls this card.
     */
    settingsContainer : null,

    /**
     * @type {Ext.form.TextField} nameField Textfield for the twitter's
     * account name.
     */
    nameField : null,

    /**
     * @type {Ext.form.Combo} updateIntervalCombo  Combobox to store update/refresh
     * behavior.
     */
    updateIntervalCombo : null,

    /**
     * @type {Ext.form.Combo} requestTimeoutCombo  Combobox to store the account's
     * request timeout for any action taken.
     */
    requestTimeoutCombo : null,

    /**
     * @type {String} recordId The id of the record which is currently displayed.
     */
    recordId : null,

    initComponent : function()
    {
        this.errorMessages = {
            name     : com.conjoon.Gettext.gettext("Please provide a valid name for this account. The name must be unique and must not already exist."),
            password : com.conjoon.Gettext.gettext("Please provide a password for this account.")
        };

        this.nameField = new Ext.form.TextField({
            fieldLabel      : com.conjoon.Gettext.gettext("Twitter user"),
            allowBlank      : false,
            validator       : this.isNameValid.createDelegate(this),
            itemCls         : 'com-conjoon-margin-b-15',
            enableKeyEvents : true,
            name            : 'name',
            disabled        : true
        });

        this.updateIntervalCombo = new Ext.form.ComboBox({
            tpl           : '<tpl for="."><div class="x-combo-list-item">{text:htmlEncode}</div></tpl>',
            fieldLabel    : com.conjoon.Gettext.gettext("Refresh interval"),
            listClass     : 'com-conjoon-smalleditor',
            itemCls       : 'com-conjoon-margin-b-10',
            displayField  : 'text',
            valueField    : 'id',
            mode          : 'local',
            editable      : false,
            triggerAction : 'all',
            name          : 'updateInterval',
            store         : new Ext.data.SimpleStore({
                data   : [
                    [60000,  com.conjoon.Gettext.gettext("1 minute")],
                    [120000, com.conjoon.Gettext.gettext("2 minutes")],
                    [300000, com.conjoon.Gettext.gettext("5 minutes")]
                ],
                fields : ['id', 'text']
            })
        });

        this.requestTimeoutCombo = new Ext.form.ComboBox({
            tpl           : '<tpl for="."><div class="x-combo-list-item">{text:htmlEncode}</div></tpl>',
            fieldLabel    : com.conjoon.Gettext.gettext("Request timeout"),
            listClass     : 'com-conjoon-smalleditor',
            displayField  : 'text',
            valueField    : 'id',
            mode          : 'local',
            editable      : false,
            triggerAction : 'all',
            name          : 'requestTimeout',
            store         : new Ext.data.SimpleStore({
                data   : [
                    [10000,  com.conjoon.Gettext.gettext("10 seconds")],
                    [20000, com.conjoon.Gettext.gettext("20 seconds")],
                    [30000, com.conjoon.Gettext.gettext("30 seconds")]
                ],
                fields : ['id', 'text']
            })
        });


        Ext.apply(this, {
            baseCls    : 'x-small-editor',
            defaults   : {
                labelStyle : 'font-size:11px',
                anchor     : '100%'
            },
            bodyStyle  : 'margin:10px 0 20px 0px;padding:10px;background:none',
            title : com.conjoon.Gettext.gettext("Account settings"),
            items : [
                this.nameField,
                new Ext.form.FieldSet({
                    defaults : {
                        labelStyle : 'font-size:11px',
                        anchor     : '100%'
                    },
                    title      : com.conjoon.Gettext.gettext("Connection options"),
                    labelAlign : 'top',
                    items      : [
                        this.updateIntervalCombo//,
                        //this.requestTimeoutCombo,
                    ]
                })
            ]
        });

        com.conjoon.service.twitter.optionsDialog.SettingsCard.superclass.initComponent.call(this);
    },

    setRecord : function(record)
    {
        this.recordId = record.id;
        com.conjoon.service.twitter.optionsDialog.SettingsCard.superclass.setRecord.call(this, record);
    },

    /**
     * Validates the current value of the feed name text field.
     *
     * A valid feedname must not equal to an empty string and may only
     * contain literals, numbers and "_".
     *
     */
    isNameValid : function(value)
    {
        value = value.toLowerCase().trim();

        var collection = this.settingsContainer.getAllEntries();

        var record = null;

        for (var i = 0, max_i = collection.length; i < max_i; i++) {
            record = collection[i];
            if (this.recordId == record.id) {
                continue;
            }
            if (record.get('name').toLowerCase() == value) {
                return false;
            }
        }

        var reg = /^[a-zA-Z0-9_]+$/;
        return value != "" && reg.test(value);
    }


});