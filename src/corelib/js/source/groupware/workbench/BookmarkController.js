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