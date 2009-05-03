/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
 * licensing@conjoon.org
 *
 * $Author: T. Suckow $
 * $Id: javascript.phtml 322 2008-12-08 10:00:57Z T. Suckow $
 * $Date: 2008-12-08 11:00:57 +0100 (Mo, 08 Dez 2008) $
 * $Revision: 322 $
 * $LastChangedDate: 2008-12-08 11:00:57 +0100 (Mo, 08 Dez 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/live/vs170240.vserver.de/svn_repository/conjoon/trunk/src/www/application/modules/default/views/scripts/index/javascript.phtml $
 */

Ext.namespace('com.conjoon.groupware.workbench');

/**
 *
 * @class com.conjoon.groupware.workbench.BookmarkController
 * @singleton
 */
com.conjoon.groupware.workbench.BookmarkController = function() {

    var _container = null;
    var _button    = null;

    return {

        getContainer : function()
        {
            if (!_container) {
                _container = new Ext.BoxComponent({
                    autoEl : {
                        tag : 'div',
                        cls : 'com-conjoon-groupware-Bookmarks-container',
                        id  : 'DOM:com.conjoon.groupware.Bookmarks.container'
                    },
                    listeners : {
                        render : function() {
                            com.conjoon.groupware.workbench.BookmarkController.getButton().render('DOM:com.conjoon.groupware.Bookmarks.container');
                        }
                    }
                });
            }

            return _container;
        },

        getButton : function()
        {
            if (!_button) {

                var reg          = com.conjoon.util.Registry;
                var contentPanel = reg.get('com.conjoon.groupware.ContentPanel');

                _button = new Ext.Toolbar.Button({
                    iconCls : 'com-conjoon-groupware-Bookmark-toolbar-bookmarkButton-icon',
                    menu    : [{
                        text    : com.conjoon.Gettext.gettext("Home"),
                        handler : function(){
                            var homePanel = reg.get('com.conjoon.groupware.HomePanel');
                            contentPanel.setActiveTab(homePanel);
                        }
                    }, '-', {
                        text : com.conjoon.Gettext.gettext("Contacts")
                    }, {
                        text    : com.conjoon.Gettext.gettext("Emails"),
                        handler : function(){
                            var emailPanel = reg.get('com.conjoon.groupware.email.EmailPanel');
                            if (!emailPanel) {
                                emailPanel = new com.conjoon.groupware.email.EmailPanel();
                                contentPanel.add(emailPanel);
                            }
                            contentPanel.setActiveTab(emailPanel);
                        }
                    }, {
                        text : com.conjoon.Gettext.gettext("Calendar")
                    }, {
                        text:com.conjoon.Gettext.gettext("Todos")
                    }, '-'
                ]});
            }

            return _button;
        }

    };

}();