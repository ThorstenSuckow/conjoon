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

Ext.namespace('com.conjoon.groupware.workbench');

/**
 *
 * @class com.conjoon.groupware.workbench.Toolbar
 * @singleton
 */
com.conjoon.groupware.workbench.ToolbarController = function(){

    var _container = null;

    var contId     = "DOM:com.conjoon.groupware.Toolbar.controls";
    var toolbars   = [];
    var actVisible = null;

    var _permanentToolbar = null;


    return {

        getContainer : function(config)
        {
            if (!_container) {
                _container = new Ext.BoxComponent({
                    autoEl : {
                        tag : 'div',
                        cls : 'com-conjoon-groupware-Toolpanel',
                        id  : 'DOM:com.conjoon.groupware.Toolpanel.container',
                        children : [{
                            tag : 'table',
                            children : [{
                                tag : 'tr',
                                children : [{
                                    tag   : 'td',
                                    id    : 'DOM:com.conjoon.groupware.Toolbar.controls'
                                }, {
                                    tag   : 'td',
                                    id    : 'DOM:com.conjoon.groupware.Toolbar.permanent'
                                }]
                            }]

                        }]
                    },
                    listeners : {
                        render : function() {
                            com.conjoon.groupware.workbench.ToolbarController.getPermanentToolbar();
                        }
                    }
                });

            }

            return _container;
        },

        getPermanentToolbar : function()
        {
            if (!_permanentToolbar) {
                _permanentToolbar = new Ext.Toolbar({
                    region   : 'center',
                    cls      : 'com-conjoon-groupware-Toolbar',
                    renderTo : 'DOM:com.conjoon.groupware.Toolbar.permanent',
                    items  : [
                        "->",
                        "-",
                        com.conjoon.Gettext.gettext("Search"),
                        {
                            iconCls : 'com-conjoon-groupware-Toolbar-searchMenu-email-icon',
                            menu : [{
                                text     : com.conjoon.Gettext.gettext("Contacts"),
                                disabled : true
                            }, {
                                text     : com.conjoon.Gettext.gettext("Emails"),
                                disabled : true
                            }, {
                                text     : com.conjoon.Gettext.gettext("Calendar"),
                                disabled : true
                            }, {
                                text     : com.conjoon.Gettext.gettext("Todos"),
                                disabled : true
                            }]
                        },
                        new Ext.form.TextField({
                            emptyText : com.conjoon.Gettext.gettext("<search in Emails>")
                        }), {
                            iconCls  : 'com-conjoon-groupware-Toolbar-startSearchButton-icon',
                            disabled : true
                        }
                    ]
                });
            }

            return _permanentToolbar;
        },


        get : function(id)
        {
            if (toolbars[id]) {
                return toolbars[id];
            }

            return null;
        },

        register : function(id, element)
        {
            element.addClass('com-conjoon-groupware-Toolbar');
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