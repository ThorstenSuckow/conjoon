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

Ext.namespace('com.conjoon.service.twitter');

/**
 * A view for rendering the firends list of a Twitter user.
 *
 * @class com.conjoon.service.twitter.FriendsList
 * @extends com.conjoon.service.twitter.DataView
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
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