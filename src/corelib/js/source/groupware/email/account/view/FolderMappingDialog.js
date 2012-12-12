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

/**
 * @class com.conjoon.groupware.email.account.view.FolderMappingDialog
 * @extends Ext.Window
 *
 */
com.conjoon.groupware.email.account.view.FolderMappingDialog = Ext.extend(Ext.Window, {


    modal     : true,
    resizable : false,
    closable  : true,
    height    : 400,
    width     : 350,
    bodyStyle : 'background-color:#F6F6F6',
    layout    : 'border',

    treePanel : null,

    textField : null,

    record : null,

    selectedData : null,

    type : null,

    initComponent : function()
    {

        if (!this.type) {
            throw("No type selected. Please put this dialog in a context.");
        }

        this.addEvents(
            /**
             * @param {com.conjoon.groupware.email.account.view.FolderMappingDialog} this
             * @param {Array} path
             */
            'selectpath'
        );

        this.selectedData = null;

        this.treePanel = this.getTreePanel();

        this.textField = this.getTextField();

        this.items = [
            this.treePanel,
            new Ext.FormPanel({
                baseCls : 'x-small-editor',
                bodyStyle : 'padding:4px 2px 2px 5px;border-top:1px solid #99BBE8',
                region  : 'south',
                height  : 30,
                layout  : 'form',
                labelWidth : 50,
                items   : [
                    this.textField
                ]
            })
        ];

        this.buttons = [{
            text    : com.conjoon.Gettext.gettext("OK"),
            width   : 75,
            handler : function() {
                this.fireEvent('selectpath', this, this.selectedData, this.type);
                this.close();
            },
            scope : this
        }, {
            text    : com.conjoon.Gettext.gettext("Cancel"),
            width   : 75,
            handler : this.close,
            scope   : this
        }]

        com.conjoon.groupware.email.account.view.FolderMappingDialog
            .superclass.initComponent.call(this);
    },

// -------- listener

    onBeforeLoad : function(treeLoader, node)
    {
        treeLoader.baseParams.groupwareEmailAccountsId = this.record.get('id');
    },

    onCheckChange : function(node, checked)
    {
        var me = this, treePanel = me.treePanel, textField = me.textField, path;

        if (checked) {
            var checkedNodes = treePanel.getChecked();
            treePanel.suspendEvents();
            for (i = 0; i < checkedNodes.length; i++) {
                if (checkedNodes[i] === node) {
                    continue;
                }
                checkedNodes[i].getUI().toggleCheck(false);
            }

            path = node.getPathAsArray('idForPath');

            me.selectedData = path;


            path.shift();
            path.shift();
            textField.setValue(path.join(' > '));

            treePanel.resumeEvents();

            return;
        }

        me.selectedData = null;
        textField.setValue("");
    },


// -------- helper

    getTextField : function()
    {
        if (this.textField) {
            return this.textField;
        }

        return new Ext.form.TextField({
            fieldLabel : com.conjoon.Gettext.gettext("Current"),
            emptyText  : '[' + this.title + ']',
            readOnly   : true,
            labelStyle : 'font-size:11px',
            anchor     : '99%'
        });
    },

    getTreePanel : function()
    {
        if (this.treePanel) {
            return this.treePanel;
        }

        return new Ext.tree.TreePanel({
            border : false,
            region : 'center',
            root : new com.conjoon.cudgets.tree.AsyncTreeNode({
                id            : 'root',
                idForPath     : 'root',
                iconCls       : 'com-conjoon-groupware-email-EmailTree-rootIcon',
                draggable     : false,
                isTarget      : false,
                allowChildren : false,
                expanded      : true,
                type          : 'root'
            }),
            listeners : {
                'checkchange' : {
                    fn    : this.onCheckChange,
                    scope : this
                }
            },
            loader : new com.conjoon.groupware.email.account.data.MappingTreeLoader({
                dataUrl   : './groupware/email.folder/get.folder/format/json',
                baseAttrs : {
                    uiProvider : com.conjoon.groupware.email.account.view.MappingNodeUi
                },
                listeners : {
                    'beforeload' : {
                        fn    : this.onBeforeLoad,
                        scope : this
                    }
                }
            }),
            rootVisible     : false,
            autoScroll      : true,
            cls             : 'com-conjoon-groupware-email-EmailTree',
            lines           : false,
            useArrows       : true,
            containerScroll : true,
            animate         : false,
            header          : false
        });

    }



});