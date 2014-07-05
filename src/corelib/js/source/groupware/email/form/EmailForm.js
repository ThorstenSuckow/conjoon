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

Ext.namespace('com.conjoon.groupware.email.form');

/**
 *
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.groupware.email.form.EmailForm
 * @extends Ext.Panel
 */
com.conjoon.groupware.email.form.EmailForm = Ext.extend(Ext.Panel, {

    __is : 'com.conjoon.groupware.email.EmailForm',


    initComponent : function()
    {
        var accSt        = com.conjoon.groupware.email.AccountStore;
        var accountStore = accSt.getInstance();

        var view = new Ext.grid.GridView({
            getRowClass : function(record, rowIndex, p, ds){
                return 'com-conjoon-groupware-email-EmailForm-gridrow';
            }
        });

        var standardAcc = accSt.getStandardAccount(false);

        this.fromComboBox = new Ext.form.ComboBox({
           name : 'from',
           tpl : '<tpl for="."><div class="x-combo-list-item">{address:htmlEncode} - {name:htmlEncode}</div></tpl>',
           fieldLabel : com.conjoon.Gettext.gettext("From"),
           anchor     : '100%',
           typeAhead: false,
           triggerAction: 'all',
           editable : false,
           lazyRender:true,
           displayField  : 'address',
           value : (standardAcc ? standardAcc.id : undefined),
           mode : 'local',
           valueField    : 'id',
           listClass: 'x-combo-list-small',
           store : accountStore
        });

        var addressQueryComboBox = new com.conjoon.groupware.email.form.RecipientComboBox();

        this.gridStore = new Ext.data.JsonStore({
            id       : 'id',
            fields   : ['receiveType', 'address']
        });

        var receiveTypeEditor = new Ext.form.ComboBox({
            typeAhead     : false,
            triggerAction : 'all',
            lazyRender    : true,
            editable      : false,
            mode          : 'local',
            value         : 'gg',
            listClass     : 'x-combo-list-small',
            store         : [
                ['to',  com.conjoon.Gettext.gettext('To:')],
                ['cc',  com.conjoon.Gettext.gettext('CC:')],
                ['bcc', com.conjoon.Gettext.gettext('BCC:')]
            ]
        });


        this.grid = new Ext.grid.EditorGridPanel({
            hideHeaders : true,
            region  : 'center',
            margins : '2 5 2 5',
            style   : 'background:none',
            store   : this.gridStore,
            columns : [{
                id        : 'receiveType',
                header    : 'receiveType',
                width     : 100,
                dataIndex : 'receiveType',
                editor    : receiveTypeEditor,
                renderer  : function(value, metadata, record, rowIndex, colIndex, store) {
                    var st  = receiveTypeEditor.store;
                    var ind = st.find('field1', value, 0, false, true);
                    var sRecord = null;
                    if (ind >= 0) {
                        sRecord = st.getAt(ind);
                    }
                    if(sRecord) {
                        return sRecord.get('field2');
                    } else {
                        '';
                    }
                }
            },{
                id : 'address',
                header: "address",
                dataIndex: 'address',
                editor: addressQueryComboBox,
                renderer: function(value, p, record) {
                    return Ext.util.Format.htmlEncode(value);
                }
            }],
            view : view,
            header : false,
            clicksToEdit:1
        });

        this.fileGridPanel = new com.conjoon.cudgets.grid.FilePanel({
            ui          : new com.conjoon.groupware.email.form
                            .ui.AttachmentPanelUi(),
            contextMenu : new com.conjoon.cudgets.grid.FilePanelContextMenu({
                ui : new com.conjoon.cudgets.grid.ui.DefaultFilePanelContextMenuUi({
                    cancelText   : com.conjoon.Gettext.gettext("Cancel"),
                    removeText   : com.conjoon.Gettext.gettext("Remove"),
                    downloadText : com.conjoon.Gettext.gettext("Download"),
                    renameText   : com.conjoon.Gettext.gettext("Rename")
                })
            })
        });

       this.subjectField = new Ext.form.TextField({
            name            : 'subject',
            fieldLabel      : com.conjoon.Gettext.gettext("Subject"),
            anchor          : '100%',
            enableKeyEvents : true
        });

        this.htmlEditor = new com.conjoon.groupware.email.form.EmailEditor();

        Ext.apply(this, {
            cls   : 'com-conjoon-groupware-email-form-EmailForm',
            items : [
                new Ext.Container({
                    cls       : 'northContainer',
                    region    : 'north',
                    layout    : 'border',
                    split     : true,
                    hideMode  : 'offsets',
                    height    : 125,
                    minSize   : 125,
                    items     : [
                        new Ext.Container({
                            layout    : 'border',
                            cls       : 'headerContainer',
                            region    : 'center',
                            split     : true,
                            hideMode  : 'offsets',
                            items     : [
                            new Ext.form.FormPanel({
                                labelWidth  : 30,
                                region : 'north',
                                height : 20,
                                minSize: 20,
                                hideMode : 'offsets',
                                margins: '4 5 2 5',
                                style  : 'background:none',
                                baseCls     : 'x-small-editor',
                                border : false,
                                defaults : {
                                    labelStyle : 'width:30px;font-size:11px'
                                },
                                defaultType : 'textfield',
                                items : [
                                    this.fromComboBox
                                ]
                          }), this.grid,
                            new Ext.form.FormPanel({
                                labelWidth  : 45,
                                region : 'south',
                                height : 20,
                                hideMode : 'offsets',
                                minSize: 20,
                                style  : 'background:none',
                                margins: '2 5 4 5',
                                baseCls     : 'x-small-editor',
                                border : false,
                                defaults : {
                                    labelStyle : 'width:45px;font-size:11px'
                                },
                                defaultType : 'textfield',
                                items : [
                                    this.subjectField
                                ]
                          })
                      ]}), this.fileGridPanel
                    ]
                }),
                new Ext.form.FormPanel({
                    region : 'center',
                    hideMode : 'offsets',
                    baseCls     : 'x-small-editor',
                    border:false,
                    items  : [this.htmlEditor]
                })
            ]

        });


        this.loadMask = null;

        com.conjoon.util.Registry.register('com.conjoon.groupware.email.EmailForm', this, true);

        com.conjoon.groupware.email.form.EmailForm.superclass.initComponent.call(this);
    },


    initEvents : function()
    {
        this.mon(this.grid, 'resize', function() {
            var cm = this.getColumnModel();
            var rem = this.getGridEl().getWidth(true)-this.view.getScrollOffset()
                      - cm.getColumnWidth(0)-2;

            cm.setColumnWidth(1, rem);
            this.view.focusEl.setWidth(rem);
        }, this.grid);

        this.on('destroy', this._onDestroy, this);

        this.mon(
            this.fileGridPanel, 'downloadcancel',
            this.onFilePanelDownloadCancel,  this
        );

        var DownloadManager = com.conjoon.groupware.DownloadManager;

        DownloadManager.on('request', this.onDownloadStart, this);
        DownloadManager.on('success', this.onDownloadEnd, this);
        DownloadManager.on('error',   this.onDownloadEnd, this);
        DownloadManager.on('failure', this.onDownloadEnd, this);
        DownloadManager.on('cancel',  this.onDownloadEnd, this);

        com.conjoon.groupware.email.form.EmailForm.superclass.initEvents.call(this);
    },

    /**
        * Listener for the attached filePanel's "downloadcancel" event.
     *
     * @param {com.conjoon.cudgets.grid.FilePanel} filePanel
     * @param {Array} records
     */
    onFilePanelDownloadCancel : function(filePanel, records)
    {
        var DownloadManager = com.conjoon.groupware.DownloadManager,
            type = null, metaType = null,
            FileRecord = com.conjoon.cudgets.data.FileRecord;

        for (var i = 0, len = records.length; i < len; i++) {
            metaType = records[i].get('metaType');

            type = metaType === FileRecord.META_TYPE_FILE
                   ? DownloadManager.TYPE_FILE
                    : metaType === FileRecord.META_TYPE_EMAIL_ATTACHMENT
                      ? DownloadManager.TYPE_EMAIL_ATTACHMENT
                      : false;

            if (type === false) {
                continue;
            }
            DownloadManager.cancelDownloadForIdAndKey(
                records[i].get('orgId'), records[i].get('key'), type
            );
        }
    },

    onDownloadStart : function(download, type, options)
    {
        var id;

        switch (type) {
            case 'file':
                id = options.fileId;
            break;

            case 'emailAttachment':
                id = options.attachmentId;
            break;
        }

        var store = this.fileGridPanel.getStore();
        var rec = store.getAt(store.find('orgId', id, false, false));

        if (rec) {
            rec.set('state', com.conjoon.cudgets.data.FileRecord.STATE_DOWNLOADING);
        }
    },

    onDownloadEnd : function(download, type, options)
    {
        var id;

        switch (type) {
            case 'file':
                id = options.fileId;
            break;

            case 'emailAttachment':
                id = options.attachmentId;
            break;
        }
        var store = this.fileGridPanel.getStore();
        var rec = store.getAt(store.find('orgId', id, false, false));

        if (rec) {
            rec.set('state', '');
        }
    },

    _onDestroy : function()
    {
        var DownloadManager = com.conjoon.groupware.DownloadManager;

        DownloadManager.un('request', this.onDownloadStart, this);
        DownloadManager.un('success', this.onDownloadEnd, this);
        DownloadManager.un('error',   this.onDownloadEnd, this);
        DownloadManager.un('failure', this.onDownloadEnd, this);
        DownloadManager.un('cancel',  this.onDownloadEnd, this);
    }




});
