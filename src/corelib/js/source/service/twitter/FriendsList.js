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

Ext.namespace('com.conjoon.service.twitter');

/**
 * A view for rendering the firends list of a Twitter user.
 *
 * @class com.conjoon.service.twitter.FriendsList
 * @extends com.conjoon.service.twitter.DataView
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
com.conjoon.service.twitter.FriendsList = Ext.extend(com.conjoon.service.twitter.DataView, {

    /**
     * @cfg {com.conjoon.service.twitter.data.TwitterUserStore} store The store
     * which data is rendered into a visual representation by this view.
     */

    /**
     * @cfg {Boolean} maskToParent
     */
    maskToParent : false,

    /**
     * @cfg {String} cls
     */
    cls : 'com-conjoon-service-twitter-FriendsList',

    /**
     * @cfg {Boolean} multiSelect
     */
    multiSelect : false,

    /**
     * @cfg {Boolean} singleSelect
     */
    singleSelect : true,

    /**
     * @cfg {String} overClass
     */
    overClass : 'over',

    /**
     * @cfg {String} itemSelector
     */
    itemSelector : 'div.user',

    /**
     * Inits this component.
     */
    initComponent : function()
    {
        if (!this.emptyText) {
            this.emptyText = com.conjoon.Gettext.gettext("No Friends available");
        }

        Ext.applyIf(this, {
            loadingText : com.conjoon.Gettext.gettext("Loading friends..."),
            tpl         : '<tpl for=".">'+
                          '<div class="user" title="{name} ({screenName})" style="background-image:url({profileImageUrl})"></div>'+
                          '</tpl>'+
                          '<div class="x-clear"></div>'
        });

        com.conjoon.service.twitter.FriendsList.superclass.initComponent.call(this);
    }

});