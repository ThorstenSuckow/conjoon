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

    pathInfo : null,

    /**
     * @type {Boolean} mayTriggerEvent indicates whether the selectpath event
     * gets fired. Set to true as soon as the user explicitely selects a tree
     * node. This is so the previous value does not get accidently overwritten
     * if the user does not select a node and intentionally closes the window
     * using the "OK" button.
     */
    mayTriggerEvent : false,

    initComponent : function()
    {

        if (!this.type) {
            throw("No type selected. Please put this dialog in a context.");
        }

        this.addEvents(
            /**
             * @param {com.conjoon.groupware.email.account.view.FolderMappingDialog} this
             * @param {Object} An object with the property "path" holding the
             *                 path as computed by the framework, and "parts" with
             *                 the individual path parts, without being polluted with
             *                 a folder delimiter
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
                if (this.mayTriggerEvent) {
                    this.fireEvent('selectpath', this, this.selectedData, this.type);
                }

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

    onLoad : function(treeLoader, node)
    {
        var me = this, treePanel = me.treePanel,
            path = me.pathInfo && me.pathInfo.path
                  ? me.pathInfo.path : "",
            foundNode;

        if (treePanel.getChecked().length > 0 || !path) {
            return;
        }

        node.findChildBy(function(node) {
            if (node.getPath('idForPath') == path) {
                foundNode = node;
                return true;
            }
        });

        if (foundNode) {
            foundNode.getUI().toggleCheck(true);
        }
    },

    onBeforeLoad : function(treeLoader, node)
    {
        treeLoader.baseParams.groupwareEmailAccountsId = this.record.get('id');
    },

    onCheckChange : function(node, checked)
    {
        this.mayTriggerEvent = true;

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

            me.selectedData = {
                parts : path,
                path  : node.getPath('idForPath')
            };

            // needed to store org array in selectedData
            path = node.getPathAsArray('idForPath');
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
        var me = this;

        if (me.textField) {
            return me.textField;
        }

        var path = [];

        if (me.pathInfo && me.pathInfo.parts) {
            for (var i = 0, len = me.pathInfo.parts.length; i < len; i++) {
                path.push( me.pathInfo.parts[i]);
            }

            path.shift();
            path.shift();
        }

        return new Ext.form.TextField({
            fieldLabel : com.conjoon.Gettext.gettext("Current"),
            emptyText  : '[' + this.title + ']',
            readOnly   : true,
            labelStyle : 'font-size:11px',
            anchor     : '99%',
            value      : path.join(' > ')
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
                    beforeload : {
                        fn    : this.onBeforeLoad,
                        scope : this
                    },
                    load : {
                        fn    : this.onLoad,
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