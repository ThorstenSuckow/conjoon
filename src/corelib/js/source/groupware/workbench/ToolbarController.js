/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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
                            menu : [
                                {text: com.conjoon.Gettext.gettext("Contacts")},
                                {text: com.conjoon.Gettext.gettext("Emails")},
                                {text: com.conjoon.Gettext.gettext("Calendar")},
                                {text: com.conjoon.Gettext.gettext("Todos")}
                            ]
                        },
                        new Ext.form.TextField({
                            emptyText : com.conjoon.Gettext.gettext("<search in Emails>")
                        }), {
                            iconCls : 'com-conjoon-groupware-Toolbar-startSearchButton-icon'
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