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

Ext.namespace('com.conjoon.service.twitter');

/**
 * A component that contains a  {com.conjoon.service.twitter.InputBox} and a
 * {com.conjoon.service.twitter.TweetList}. Its purpose is to show the recent tweets
 * of a user's friends along with an InputBox to be able to reply to tweets/update
 * the status.
 *
 * @class com.conjoon.service.twitter.RecentTweetsContainer
 * @extends Ext.BoxComponent
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.service.twitter.RecentTweetsContainer = Ext.extend(Ext.BoxComponent, {

    /**
     * @cfg {com.conjoon.service.twitter.InputBox} inputBox The InputBox used for
     * this container.
     */

    /**
     * @cfg {com.conjoon.service.twitter.TweetList} recentTweets The TweetList used
     * for this container.
     */

    /**
     * @type {HtmlElement} _viewContainer Native HtmlElement which wraps the TweetList.
     * Its height will be adjusted according to the height of the surrounding container
     * and the height of the InputBox.
     */

    /**
     * @cfg {Object} autoEl
     */
    autoEl : {
        cls      : 'com-conjoon-service-twitter-RecentTweetsContainer',
        tag      : 'div',
        children : [{
            tag : 'div',
            cls : 'viewContainer'
        }]
    },

    /**
     * Overrides parent implementation by initializing custom elements.
     *
     */
    afterRender : function()
    {
        com.conjoon.service.twitter.RecentTweetsContainer.superclass.afterRender.call(this);

        this._viewContainer = this.el.dom.firstChild;

        this.recentTweets.render(this._viewContainer);
        this.inputBox.render(this.el.dom);
        this.el.dom.appendChild(this._viewContainer);

        this.syncSize();
    },

    /**
     * Calls parent's implementation and adjusts the height of _viewContainer to
     * resize _tweetList properly.
     *
     * @param {Number} adjWidth
     * @param {Number} adjHeight
     * @param {Number} rawWidth
     * @param {Number} rawHeight
     *
     */
    onResize : function(adjWidth, adjHeight, rawWidth, rawHeight)
    {
        com.conjoon.service.twitter.RecentTweetsContainer.superclass.onResize.call(
            this, adjWidth, adjHeight, rawWidth, rawHeight
        );

        this._viewContainer.style.height = (this.el.dom.offsetHeight - this.inputBox.el.dom.offsetHeight)+"px";
    }

});