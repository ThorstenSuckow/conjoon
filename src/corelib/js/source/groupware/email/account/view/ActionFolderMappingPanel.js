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

Ext.namespace('com.conjoon.groupware.email.account.view');

com.conjoon.groupware.email.account.view.ActionFolderMappingPanel = Ext.extend(Ext.FormPanel, {

    /**
     * @type {com.conjoon.groupware.email.AccountRecord} record
     */
    record : null,

    inboxPathTextField : null,
    sentPathTextField : null,
    draftPathTextField : null,
    trashPathTextField : null,
    junkPathTextField : null,

    /**
     *
     */
    initComponent : function()
    {
        var me = this;

        me.title = com.conjoon.Gettext.gettext("Mappings");

        var inboxRow = this.getFolderMappingRow({
            fieldLabel : com.conjoon.Gettext.gettext("\"Inbox\" Folder"),
            handler    : function() {this.openFolderDialog('inbox');}
        });

        var sentRow = this.getFolderMappingRow({
            fieldLabel : com.conjoon.Gettext.gettext("\"Sent\" Folder"),
            handler    : function() {this.openFolderDialog('sent');}
        });

        var trashRow = this.getFolderMappingRow({
            fieldLabel : com.conjoon.Gettext.gettext("\"Trash\" Folder"),
            handler    : function() {this.openFolderDialog('trash');}
        });

        var junkRow = this.getFolderMappingRow({
            fieldLabel : com.conjoon.Gettext.gettext("\"Junk\" Folder"),
            handler    : function() {this.openFolderDialog('junk');}
        });

        var draftRow = this.getFolderMappingRow({
            fieldLabel : com.conjoon.Gettext.gettext("\"Draft\" Folder"),
            handler    : function() {this.openFolderDialog('draft');}
        });


        me.inboxPathTextField = inboxRow.textField;
        me.sentPathTextField  = sentRow.textField;
        me.draftPathTextField = draftRow.textField;
        me.trashPathTextField = trashRow.textField;
        me.junkPathTextField  = junkRow.textField;

        me.items = [
            new com.conjoon.groupware.util.FormIntro({
                style     : 'margin:10px 0 25px 0;',
                labelText : com.conjoon.Gettext.gettext("Map folders to actions"),
                text      : com.conjoon.Gettext.gettext("This card allows for mapping remote folders to certain actions.<br />\"Actions\" represent different kind of workflows, such as moving an email message to the trash bin, or saving a \"sent\" message to a specific folder. Please specifiy the target folders for such actions using the form below.")
            }),
            inboxRow.container,
            sentRow.container,
            trashRow.container,
            junkRow.container,
            draftRow.container
        ];

        com.conjoon.groupware.email.account.view.ActionFolderMappingPanel.superclass.initComponent.call(this);
    },

    /**
     *
     * @param {com.conjoon.groupware.email.AccountRecord} record
     */
    setAccountRecord : function(record)
    {
        if (record.get('protocol') != 'IMAP') {
            throw("Invalid protocol for account record: \""
                + record.get('protocol') + "\""
            );
        }

        this.record = record;

        this.fillFormFields(record);
    },

    /**
     * @param {com.conjoon.groupware.email.AccountRecord} record
     */
    fillFormFields : function(record)
    {
        console.log("fillFormFields", record);
    },

    /**
     *
     * @param {String} type
     */
    openFolderDialog : function(type)
    {
        var w = this.getFolderDialog(type);

        w.on('selectpath', this.onSelectPath, this, {single : true});

        w.show();
    },

    /**
     * Listener for the FolderMappingDialog's selectpat event.
     *
     * @param {com.conjoon.groupware.email.account.view.FolderMappingDialog} dialog
     * @param {Array} path
     * @param {String} type
     */
    onSelectPath :function(dialog, path, type)
    {
        var me = this, pathText = path.join(' > '), textField;

        switch (type) {
            case 'inbox':
                textField = me.inboxPathTextField;
                break;
            case 'sent':
                textField = me.sentPathTextField;
                break;
            case 'draft':
                textField = me.draftPathTextField;
                break;
            case 'trash':
                textField = me.trashPathTextField;
                break;
            case 'junk':
                textField = me.junkPathTextField;
                break;
        }

        textField.setValue(pathText);
    },

// -------- helper

    /**
     * @return {Ext.Container}
     */
    getFolderMappingRow : function(config)
    {
        var textField =  new Ext.form.TextField({
            itemCls    : 'com-conjoon-float-left',
            fieldLabel : config.fieldLabel,
            width      : 220,
            labelStyle : 'font-size:11px',
            readOnly   : true,
            emptyText  : com.conjoon.Gettext.gettext("[Please choose a folder]")
        });

        return {
            container : new Ext.Container({
                layout  : 'form',
                labelWidth: 100,
                items   : [
                    textField,
                    new Ext.Button({
                        cls     : 'com-conjoon-float-left com-conjoon-margin-l-15',
                        text    : com.conjoon.Gettext.gettext("Choose..."),
                        width   : 75,
                        scope   : this,
                        handler : config.handler
                    }),
                    new com.conjoon.groupware.util.Clear({style : 'height:10px'})
                ]
            }),
            textField : textField
        };

    },


    /**
     * @return {Ext.Window}
     */
    getFolderDialog : function(type)
    {
        var title = "";

        switch (type) {
            case 'inbox':
                title = com.conjoon.Gettext.gettext("Choose remote \"Inbox\" Folder");
                break;
            case 'sent':
                title = com.conjoon.Gettext.gettext("Choose remote \"Sent\" Folder");
                break;
            case 'trash':
                title = com.conjoon.Gettext.gettext("Choose remote \"Trash\" Folder");
                break;
            case 'junk':
                title = com.conjoon.Gettext.gettext("Choose remote \"Junk\" Folder");
                break;
            case 'draft':
                title = com.conjoon.Gettext.gettext("Choose remote \"Draft\" Folder");
                break;
            default:
                throw("Unknown type \"" + type + "\"");
        }

        return new com.conjoon.groupware.email.account.view.FolderMappingDialog({
            record : this.record,
            title  : title,
            type   : type
        });
    }

});