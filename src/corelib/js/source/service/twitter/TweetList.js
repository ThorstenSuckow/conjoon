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
     * @type {com.conjoon.service.twitter.data.AccountRecord} _accountRecord
     * The Twitter-account for which this data was rendered, i.e. the user who
     * requested to view the tweets.
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
    itemSelector : 'div.tweet',

    /**
     * Inits this component.
     *
     */
    initComponent : function()
    {
        if (!this.emptyText) {
            this.emptyText = com.conjoon.Gettext.gettext("No Tweets available");
        }

        var my = this;

        Ext.applyIf(this, {
            loadingText : com.conjoon.Gettext.gettext("Loading tweets..."),
            tpl         : new Ext.XTemplate('<tpl for=".">' +
                          '<div class="tweet {[xindex % 2 === 0 ? "even" : "odd"]}">'+
                          '<div class="tweetAction">'+
                          '<tpl if="!this.isCurrentAccount(userId)">'+
                              '<div class="tweet_reply_icon"></div>'+
                          '</tpl>'+
                          '<tpl if="this.isCurrentAccount(userId)">'+
                              '<div class="tweet_delete_icon"></div>'+
                          '</tpl>'+

                          '<tpl if="favorited == 0">'+
                              '<div class="tweet_bookmark_icon"></div>'+
                          '</tpl>'+
                          '<tpl if="favorited != 0">'+
                              '<div class="tweet_unbookmark_icon"></div>'+
                          '</tpl>'+

                          '</div>'+
                          '<img class="image" src="{profileImageUrl}"/>'+
                          '<h1 class="authorName">'+
                          '{screenName}'+
                          '</h1>'+
                          '<p class="tweetEntry">'+
                          '{text} '+
                          '<span class="meta"><a class="when" href="#">{when}</a>'+
                          ' from '+

                          '<tpl if="sourceUrl != 0">'+
                              '<a class="source" href="{sourceUrl}">{source}</a>'+
                          '</tpl>'+
                          '<tpl if="sourceUrl == 0">'+
                              '{source}'+
                          '</tpl>'+

                          '<tpl if="inReplyToScreenName != 0">'+
                              ' <a class="inReplyTo" href="#">in reply to {inReplyToScreenName}</a>'+
                          '</tpl>'+

                          '</span>'+
                          '</p>'+
                          '<div style="clear: both;"></div>'+
                          '</div>'+
                          '</tpl>', {
                              isCurrentAccount : function(accountId) {
                                  if (!my._accountRecord) {
                                      return false;
                                  }
                                  return my._accountRecord.get('twitterId') == accountId;
                              }
                          })
        });

        com.conjoon.service.twitter.TweetList.superclass.initComponent.call(this);
    },

    /**
     * Function which can be overridden to provide custom formatting for each
     * Record that is used by this DataView's {@link #tpl template} to render
     * each node.
     * This implementation will take care of computing the posted time based
     * on the record's "createdAt" property and save the generated string in the
     * return value's property "when".
     * The text of the status message will also be parsed. If a username referenced
     * with "@" is found, this username will be replaced with a link.
     * The method will also take care of looking up urls in the text and wrap them
     * with a-tags, so the link is highlited and clickable.
     *
     * @param {Array/Object} data The raw data object that was used to create the
     * Record. Notice, that this is the original data of the record, thus a reference.
     * Changes to this object will be reflected in teh record itself.
     * @param {Number} recordIndex the index number of the Record being prepared
     * for rendering.
     * @param {Record} record The Record being prepared for rendering.
     *
     * @return {Array/Object} The formatted data in a format expected by the internal
     * {@link #tpl template}'s overwrite() method. (either an array if your params
     * are numeric (i.e. {0}) or an object (i.e. {foo: 'bar'}))
     */
    prepareData : function(data, recordIndex, record)
    {
        var when = this._getTimeString(record);

        // replace all usernames referenced with an "@" with appropriate links
        var text = record.get("text").replace(
            /@(.*?)(\.|\)|:|!|$|\s)/ig,
            '@<a class="screenName" href="#">$1</a>$2'
        );

        text = com.conjoon.util.Format.formatUrls(text, {
            'class' : 'tweetUrl'
        });

        var f = Ext.apply({}, data);

        return Ext.apply(f, {
            when : when,
            text : text
        });
    },

    /**
     * Calls parent's implementation and adjusts the css-classes for the entris.
     *
     * @param {Ext.data.Store} ds
     * @param {Array} records
     * @param {Number} index
     */
    onAdd : function(ds, records, index)
    {
        com.conjoon.service.twitter.TweetList.superclass.onAdd.call(this, ds, records, index);

        this._updateRows();
    },

    /**
     * Called when an element gets removed from the store.
     * Highlights the element to remove, then calls its parents
     * implementation afterwards.
     *
     * @param {Ext.data.Store} ds
     * @param {Array} records
     * @param {Number} index
     */
    onRemove : function(ds, record, index)
    {
        var all = this.all;
        var el = all.item(index);

        this.deselect(index);

        el.fadeOut({
            endOpacity : 0,
            easing     : 'easeOut',
            duration   : .2,
            remove     : false,
            useDisplay : true,
            callback   : function() {
                com.conjoon.service.twitter.TweetList.superclass.onRemove.call(this, ds, record, index);
                this._updateRows();
            },
            scope  : this,
            // make sure all blocking fx get removed so we can remove this
            // element
            // when switching between tweet lists, removing an element in the view
            // without specifying this property might fail.
            // Example:
            // 1 remove this property
            // 2 go to Account A
            // 3 Add tweet X
            // 4 go to Account B
            // 5 switch back to Account A
            // 6 Delete tweet X
            // As of Ext 3.0.0, without specifying stopFx == true, the tweet won't
            // be removed from the view (though its clearly not available anymore in
            // the store)
            stopFx : true
        });

    },

    /**
     * Resets the css classes for the entries in this view. Should
     * be called whenever a record gets added or removed from the store.
     * Will also recalculate the time string, i.e. the entry when the
     * tweet was posted (see tpl "when" placeholder),
     *
     * @protected
     */
    _updateRows : function()
    {
        var i = 0;
        var all = this.all;
        var el = all.item(i);

        while(el) {
            el.removeClass('even');
            el.removeClass('odd');
            el.addClass((i % 2 === 0) ? 'even' : 'odd');
            i++;
            el = all.item(i);
        }
    },

    /**
     * Returns a textual representation of the time difference between
     * the date stored in the createdAt property of the passed tweetRecord
     * and the current time.
     *
     * @param {com.conjoon.service.twitter.data.TweetRecord} record
     *
     * @return {String}
     *
     * @protected
     */
    _getTimeString : function(record)
    {
        // compute the time difference
        var now     = Math.floor(((new Date()).getTime()/1000));
        var posted  = Math.floor((record.get('createdAt').getTime()/1000));
        var minutes = (now-posted)/60;
        var when    = "";

        switch (true) {
            // seconds
            case (minutes < 0.05):
                when = String.format(
                    com.conjoon.Gettext.gettext("less than 5 seconds ago"),
                    Math.floor(minutes)
                );
            break;

            // minutes
            case (minutes <= 60):
                if (minutes <= 0.5) {
                    when = com.conjoon.Gettext.gettext("half a minute ago");
                } else if (minutes < 2) {
                    when = com.conjoon.Gettext.gettext("1 minute ago");
                } else {
                    when = String.format(
                        com.conjoon.Gettext.gettext("{0} minutes ago"),
                        Math.floor(minutes)
                    );
                }
            break;

            // hours
            case (minutes < 1440):
                if (minutes < 120) {
                    when = com.conjoon.Gettext.gettext("about 1 hour ago");
                } else {
                    minutes = Math.floor(minutes/60);
                    when = String.format(
                        com.conjoon.Gettext.gettext("about {0} hours ago"),
                        minutes
                    );
                }
            break;

            // days
            case (minutes < 44640):
                if (minutes < 2880) {
                    when = com.conjoon.Gettext.gettext("1 day ago");
                } else {
                    minutes = Math.floor(minutes/1440);
                    when = String.format(
                        com.conjoon.Gettext.gettext("{0} days ago"),
                        minutes
                    );
                }
            break;

            // months
            case (minutes >= 44640):
                if (minutes < 89280) {
                    when = com.conjoon.Gettext.gettext("about 1 month ago");
                } else {
                    minutes = Math.floor(minutes/44640);
                    when = String.format(
                        com.conjoon.Gettext.gettext("about {0} months ago"),
                        minutes
                    );
                }
            break;
        }

        return when;
    },

// -------- public API

    /**
     * Forces this view to update some html nodes, but not to re-render itself.
     * Updated text will be for example the timestamp "when" in the template".
     * This method should be called from a controller that checks if the tweetPoller
     * triggered the updateempty event, so the contents can be refreshed in a
     * frequent interval.
     *
     * Developers note:
     * The implementation will be used in other methods in this class without calling
     * this method directly. If you change anything here, make sure you update/refactore
     * other implementation too.
     */
    updateMetaInfo : function()
    {
        var i = 0;
        var all = this.all;
        var el = all.item(i);

        while(el) {
            // returns a composite element
            /**
             * @todo think about performance enhancement
             */
            el.select('p span a.when').update(
                this._getTimeString(this.store.getAt(i))
            );

            i++;
            el = all.item(i);
        }
    },

    /**
     * Removes the tweetRecord with the specified id out of the store.
     *
     * @param {Number} id the Id of the tweet to remove.
     */
    removeTweet : function(id)
    {
        var rec = this.store.getById(id);

        if (!rec) {
            return;
        }

        this.store.remove(rec);
    },

    /**
     * Sets the account record for this tweet list.
     *
     * @param {com.conjoon.service.twitter.data.AccountRecord} accountRecord
     */
    setAccountRecord : function(accountRecord)
    {
        this._accountRecord = accountRecord;
    }

});