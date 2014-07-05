/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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

Ext.namespace('com.conjoon.groupware.email.account.view');

com.conjoon.groupware.email.account.view.ActionFolderMappingPanel = Ext.extend(Ext.FormPanel, {

    /**
     * @type {com.conjoon.groupware.email.AccountRecord} record
     */
    record : null,

    settings : null,

    inboxPathTextField : null,
    sentPathTextField : null,
    draftPathTextField : null,
    trashPathTextField : null,
    junkPathTextField : null,
    outboxPathTextField : null,

    /**
     *
     */
    initComponent : function()
    {
        var me = this;

        me.addEvents(
            /**
             * Helper event for sending field changed state events.
             */
            'fieldchange'
        );

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

        var outboxRow = this.getFolderMappingRow({
            fieldLabel : com.conjoon.Gettext.gettext("\"Outbox\" Folder"),
            handler    : function() {this.openFolderDialog('outbox');}
        });


        me.inboxPathTextField  = inboxRow.textField;
        me.sentPathTextField   = sentRow.textField;
        me.draftPathTextField  = draftRow.textField;
        me.trashPathTextField  = trashRow.textField;
        me.junkPathTextField   = junkRow.textField;
        me.outboxPathTextField = outboxRow.textField;

        me.items = [
            new com.conjoon.groupware.util.FormIntro({
                style     : 'margin:10px 0 20px 0;',
                labelText : com.conjoon.Gettext.gettext("Map folders to actions"),
                text      : com.conjoon.Gettext.gettext("This card allows for mapping remote folders to certain actions.<br />\"Actions\" represent different kind of workflows, such as moving an email message to the trash bin, or saving a \"sent\" message to a specific folder. Please specifiy the target folders for such actions using the form below.")
            }),
            inboxRow.container,
            sentRow.container,
            trashRow.container,
            junkRow.container,
            draftRow.container,
            outboxRow.container
        ];

        com.conjoon.groupware.email.account.view.ActionFolderMappingPanel.superclass.initComponent.call(this);
    },

    /**
     *
     * @param {com.conjoon.groupware.email.AccountRecord} record
     */
    setAccountRecord : function(record)
    {
        var me = this;

        me.settings = {
            inbox  : {path : "", parts : []},
            sent   : {path : "", parts : []},
            trash  : {path : "", parts : []},
            junk   : {path : "", parts : []},
            draft  : {path : "", parts : []},
            outbox : {path : "", parts : []}
        };

        if (record.get('protocol') != 'IMAP') {
            throw("Invalid protocol for account record: \""
                + record.get('protocol') + "\""
            );
        }

        this.record = record;

        me.applySettingsFromRecord(me.settings, record);
    },

    /**
     * @param {com.conjoon.groupware.email.AccountRecord} record
     */
    applySettingsFromRecord : function(target, record)
    {
        var me = this,
            folderMappings = record.get('folderMappings'),
            mapping, path,
            inbox  = "",
            outbox = "",
            sent   = "",
            draft  = "",
            trash  = "",
            junk   = "";

        for (var i = 0, len = folderMappings.length; i < len; i++) {
            mapping = folderMappings[i];

            target[(mapping.type).toLowerCase()] = {
                path  : '/' + mapping.path.join('/'),
                parts : mapping.path
            };

            path = [];
            for (var a = 0, lena = mapping.path.length; a < lena; a++) {
                path.push(mapping.path[a]);
            }

            path.shift();
            path.shift();

            switch ((mapping.type).toLowerCase()) {
                case 'inbox':
                    inbox = path.join(' > ');
                    break;
                case 'sent':
                    sent = path.join(' > ');
                    break;
                case 'draft':
                    draft = path.join(' > ');
                    break;
                case 'trash':
                    trash = path.join(' > ');
                    break;
                case 'junk':
                    junk = path.join(' > ');
                    break;
                case 'outbox':
                    outbox = path.join(' > ');
                    break;
            }
        }

        me.inboxPathTextField.setValue(inbox);
        me.sentPathTextField.setValue(sent);
        me.draftPathTextField.setValue(draft);
        me.trashPathTextField.setValue(trash);
        me.junkPathTextField.setValue(junk);
        me.outboxPathTextField.setValue(outbox);

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

    saveToRecord : function(record)
    {
        var me = this,
            settings = me.settings,
            mapping,
            found = {inbox : false, sent : false, draft : false,
                    junk: false, trash : false, outbox : false
            },
            type, changed = false;

        for (var i = 0, len = record.get('folderMappings').length; i < len; i++) {
            mapping = record.get('folderMappings')[i];

            type = (mapping.type).toLowerCase();

            if (type == 'inbox') {
                if (mapping.path.join('/') != settings.inbox.parts.join('/')) {
                    mapping.path = settings.inbox.parts;
                    changed = true;
                }
                found.inbox = true;
                continue;
            }
            if (type == 'sent') {
                if (mapping.path.join('/') != settings.sent.parts.join('/')) {
                    mapping.path = settings.sent.parts;
                    changed = true;
                }
                found.sent = true;
                continue;
            }
            if (type == 'draft') {
                if (mapping.path.join('/') != settings.draft.parts.join('/')) {
                    mapping.path = settings.draft.parts;
                    changed = true;
                }
                found.draft = true;
                continue;
            }
            if (type == 'junk') {
                if (mapping.path.join('/') != settings.junk.parts.join('/')) {
                    mapping.path = settings.junk.parts;
                    changed = true;
                }
                found.junk = true;
                continue;
            }
            if (type == 'trash') {
                if (mapping.path.join('/') != settings.trash.parts.join('/')) {
                    mapping.path = settings.trash.parts;
                    changed = true;
                }
                found.trash = true;
                continue;
            }
            if (type == 'outbox') {
                if (mapping.path.join('/') != settings.outbox.parts.join('/')) {
                    mapping.path = settings.outbox.parts;
                    changed = true;
                }
                found.outbox = true;
                continue;
            }
        }

        if (!found.inbox && settings.inbox.parts.length > 0) {
            record.get('folderMappings').push({
                path : settings.inbox.parts,
                type : 'INBOX'
            });
            changed = true;
        }
        if (!found.sent && settings.sent.parts.length > 0) {
            record.get('folderMappings').push({
                path : settings.sent.parts,
                type : 'SENT'
            });
            changed = true;
        }
        if (!found.draft && settings.draft.parts.length > 0) {
            record.get('folderMappings').push({
                path : settings.draft.parts,
                type : 'DRAFT'
            });
            changed = true;
        }
        if (!found.junk && settings.junk.parts.length > 0) {
            record.get('folderMappings').push({
                path : settings.junk.parts,
                type : 'JUNK'
            });
            changed = true;
        }
        if (!found.trash && settings.trash.parts.length > 0) {
            record.get('folderMappings').push({
                path : settings.trash.parts,
                type : 'TRASH'
            });
            changed = true;
        }
        if (!found.outbox && settings.outbox.parts.length > 0) {
            record.get('folderMappings').push({
                path : settings.outbox.parts,
                type : 'OUTBOX'
            });
            changed = true;
        }

        // force record to go into dirty state
        if (changed) {
            var tmp = record.get('folderMappings');
            record.set('folderMappings', "");
            record.set('folderMappings', tmp);
        }
    },

    /**
     * Listener for the FolderMappingDialog's selectpat event.
     *
     * @param {com.conjoon.groupware.email.account.view.FolderMappingDialog} dialog
     * @param {Object} pathInfo
     * @param {String} type
     */
    onSelectPath :function(dialog, pathInfo, type)
    {
        var me = this, textField, path = [];

        this.fireEvent('fieldchange', this);

        this.settings[type] = {
            parts : (pathInfo && pathInfo.parts ? pathInfo.parts : []),
            path  : (pathInfo && pathInfo.path  ? pathInfo.path : "")
        };

        if (pathInfo && pathInfo.parts) {
            for (var i = 0, len = pathInfo.parts.length; i < len; i++) {
                path.push(pathInfo.parts[i]);
            }

            path.shift();
            path.shift();

        }

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
            case 'outbox':
                textField = me.outboxPathTextField;
                break;
        }

        textField.setValue(path.join(' > '));
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
                    new com.conjoon.groupware.util.Clear({style : 'height:7px'})
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
        var me = this, title = "";

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
            case 'outbox':
                title = com.conjoon.Gettext.gettext("Choose remote \"Outbox\" Folder");
                break;
            default:
                throw("Unknown type \"" + type + "\"");
        }

        return new com.conjoon.groupware.email.account.view.FolderMappingDialog({
            record   : this.record,
            title    : title,
            type     : type,
            pathInfo : me.settings[type]
        });
    }

});