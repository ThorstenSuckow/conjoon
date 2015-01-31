/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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
 * A component that contains a  {com.conjoon.service.twitter.InputBox} and a
 * {com.conjoon.service.twitter.TweetList}. Its purpose is to show the recent tweets
 * of a user's friends along with an InputBox to be able to reply to tweets/update
 * the status.
 *
 * @class com.conjoon.service.twitter.RecentTweetsContainer
 * @extends Ext.BoxComponent
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
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