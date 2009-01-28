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

Ext.namespace('com.conjoon.service.twitter');

/**
 * A custom implementation of  {com.conjoon.service.twitter.DataView} to render the
 * contents of a {com.conjoon.service.twitter.data.TweetStore}, i.e. rendering Tweets.
 *
 * @class com.conjoon.service.twitter.TweetList
 * @extends com.conjoon.service.twitter.DataView
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.service.twitter.TweetList = Ext.extend(com.conjoon.service.twitter.DataView, {

    /**
     * @cfg {com.conjoon.service.twitter.data.TweetStore} store The store
     * which data is rendered into a visual representation by this view.
     */

    /**
     * @cfg {String} cls
     */
    cls : 'com-conjoon-service-twitter-TweetList',

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
    itemSelector : 'tr.tweet',

    /**
     * Inits this component.
     *
     */
    initComponent : function()
    {
        if (!this.emptyText) {
            this.emptyText = com.conjoon.Gettext.gettext("No Tweets available");
        }

        Ext.applyIf(this, {
            loadingText : com.conjoon.Gettext.gettext("Loading tweets..."),
            tpl         : '<table cellspacing="0" cellpadding="0">' +
                          '<tbody>' +
                          '<tpl for=".">' +
                          '<tr class="tweet {[xindex % 2 === 0 ? "row" : ""]}">' +
                          '<td class="image"><img src="{profileImageUrl}" width="34" heigth="34" title="{author}"></td>' +
                          '<td class="tweetEntry"><span class="authorName">{screenName}</span> {text} <span class="meta">2 hours ago</span></td>' +
                          '<td class="tweetAction"><div class="tweet_reply_icon"></div></td>' +
                          '</tr>' +
                          '</tpl>' +
                          '</tbody>' +
                          '</table>'
        });

        com.conjoon.service.twitter.TweetList.superclass.initComponent.call(this);
    }

});