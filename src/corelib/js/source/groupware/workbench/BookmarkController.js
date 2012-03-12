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

        /**
         * Returns the index in the menu where the first dynamic item
         * can be added.
         *
         * @return {Number}
         */
        getDynIndex : function()
        {
            /**
             * @todo don't hardcode, instead get position of last separator
             */
            return 7;
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
                        text     : com.conjoon.Gettext.gettext("Contacts"),
                        disabled : true
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
                        text     : com.conjoon.Gettext.gettext("Calendar"),
                        disabled : true
                    }, {
                        text     : com.conjoon.Gettext.gettext("Todos"),
                        disabled : true
                    }, '-'
                ]});
            }

            return _button;
        }

    };

}();