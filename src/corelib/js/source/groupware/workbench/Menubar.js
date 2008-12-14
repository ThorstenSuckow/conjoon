/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
 * @class com.conjoon.groupware.workbench.Menubar
 * @singleton
 */
com.conjoon.groupware.workbench.Menubar = function(){

    var _menubar = null;

    return {

        /**
         * @return {Ext.Toolbar}
         */
        getInstance : function()
        {
            if (!_menubar) {

                _menubar = new Ext.Toolbar({
                    cls   : 'com-conjoon-groupware-Menubar',
                    items : [
{
            text:'File',
            menu: [{
                text:'New',
                menu : [com.conjoon.groupware.email.decorator.AccountActionComp.decorate(new Ext.menu.Item({
                    text : 'Email...',
                    handler : function() {
                       com.conjoon.groupware.email.EmailEditorManager.createEditor();
                    }
                }))]
            }, '-', {
                text    : "Logout...",
                handler : function() {
                    com.conjoon.groupware.Reception.showLogout();
                }
            }]
        },
            {
                text:'Extras',
                menu: [{
                      text : "Feeds...",
                      handler : function() {
                          var dialog = new com.conjoon.groupware.feeds.FeedOptionsDialog();
                          dialog.show();
                      }
                      }, '-', {
                      text:com.conjoon.Gettext.gettext("Email accounts..."),
                      handler : function() {
                        var dialog = new com.conjoon.groupware.email.EmailAccountDialog();
                        dialog.show();
                      }
                     }]
            },
            {
                text:'?',
                menu: [{
                      text:com.conjoon.Gettext.gettext("About")
                     }]
            }

                    ]

                });

            }

            return _menubar;
        }
    };

}();