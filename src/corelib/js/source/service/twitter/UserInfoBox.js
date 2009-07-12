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
 * A component for rendering an info box for a user.
 * This component will fire the "userload" event if the data of a user has
 * been loaded and rendered into this component.
 *
 * @class com.conjoon.service.twitter.UserInfoBox
 * @extends Ext.BoxComponent
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.service.twitter.UserInfoBox = Ext.extend(Ext.BoxComponent, {

    /**
     * @cfg {Object} autoEl
     */
    autoEl :  {
        tag : 'div',
        cls : 'com-conjoon-service-twitter-UserInfoBox'
    },

    /**
     * @cfg {com.conjoon.service.twitter.data.TweetStore} tweetStore The tweet store
     * which loads recent tweet's of a specified user. The template of this component
     * will be populated with the data of the store upon load.
     */

    /**
     * @cfg {Ext.Xtemplate} tpl template used to render the contents of this
     * component.
     */

    /**
     * @type {Ext.data.Record} _loadedUser
     */
    _loadedUser : null,


    initComponent : function()
    {
        this.addEvents(
            /**
             * @event userload
             * @param {com.conjoon.service.twitter.UserInfoBox} userInfoBox
             * @param mixed tweetRecord or TwitterUserRecord
             */
            'userload',
            /**
             * @event userloadfailed
             * @param {com.conjoon.service.twitter.UserInfoBox} userInfoBox
             * @param
             */
            'userloadfailed'
        );

        Ext.applyIf(this, {
            tpl : new Ext.XTemplate(

                '<div>',
                '<div class="x-window">',
                '<div class="x-window-tl x-panel-noheader"><div class="x-window-tr"><div class="x-window-tc" style="height:8px;"></div></div></div>',
                '<div class="x-window-bwrap">',
                '<div class="x-window-ml"><div class="x-window-mr"><div class="x-window-mc">',
                '<div>',

                '<table cellspacing="0" cellpadding="0">',
                '<tbody>',
                '<tr class="meta">',
                '<td class="image" colspan="2">',
                '<div class="profileImageCont">',
                '<img class="profileImageClass" src="{profileImageUrl}" border="0"/>',
                '</div>',
                '<div class="nameCont">',
                '<div class="screenName">{screenName}</div>',
                '<div class="name"><span class="label">Name:</span> {name}</div>',
                '</div>',
                '<div class="x-clear"></div>',
                '</td>',
                '<tr class="location">',
                '<td class="label">Location:</span></td>',
                '<td class="value">{location}</td>',
                '</tr>',
                '<tr class="web">',
                '<td class="label">Web:</span></td>',
                '<td class="value">',
                '<tpl if="url != 0">',
                '<a href="{url}">{url}</a>',
                '</tpl>',
                '<tpl if="url == 0">&nbsp;</tpl>',
                '</td>',
                '</tr>',
                '<tr class="description">',
                '<td class="label">Bio:</span></td>',
                '<td class="value">{description}</td>',
                '</tr>',
                '</tbody>',
                '</table>',
                '</div>',
                '</div>',
                '</div>',
                '</div>',
                '</div>',
                '<div class="x-window-bl x-panel-nofooter"><div class="x-window-br"><div class="x-window-bc"></div></div></div>',
                '</div>',
                '</div>'
            )
        });

        if (this.tweetStore) {
            this.tweetStore.on('load',       this._onTweetStoreLoad, this);
            this.tweetStore.on('beforeload', this._onBeforeTweetStoreLoad, this);
        }

        var clickListener = com.conjoon.groupware.util.LinkInterceptor.getListener();

        for (var i in clickListener) {
            this.on(i, clickListener[i]);
        }

        com.conjoon.service.twitter.UserInfoBox.superclass.initComponent.call(this);
    },

// -------- public API

    /**
     * Loads user's data into this component and fires the "userload" event
     * afterwards.
     *
     * @param mixed record either {com.conjoon.service.twitter.data.TwitterUserRecord} or
     * {com.conjoon.service.twitter.data.TweetRecord} Attention! if a TweetRecord
     * is passed, the id of the user is stored in the userId property!
     * @param {Boolean} true to not fire the userload event
     *
     */
    loadUser : function(record, suspendEvents)
    {
        if (!record) {
            this.fireEvent('userloadfailed', this);
            return;
        }

        this._loadedUser = record.copy();

        var data = Ext.apply({}, record.data);


        this.tpl.overwrite(this.el, data);
        this.fireEvent('userload', this, record);
    },

    getLoadedUser : function()
    {
        return this._loadedUser;
    },

// -------- listeners

    /**
     *
     * @param {Ext.data.Store} store
     * @param {Array} records
     * @param {Object} options
     *
     */
    _onTweetStoreLoad : function(store, records, options)
    {
        this.loadUser(records[0]);
    },

    /**
     *
     *
     * @param {Ext.data.Store} store
     * @param {Array} records
     * @param {Object} options
     *
     */
    _onBeforeTweetStoreLoad : function(store, records, options)
    {
        this._loadedUser = null;

        this.tpl.overwrite(this.el, {
            profileImageUrl      : Ext.BLANK_IMAGE_URL,
            screenName           : com.conjoon.Gettext.gettext("Loading..."),
            name                 : '',
            location             : '',
            url                  : '',
            description          : ''
        });
    }

});