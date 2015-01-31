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
 * A component that contains a  {com.conjoon.service.twitter.UserInfoBox} and a
 * {com.conjoon.service.twitter.TweetList}. Its purpose is to show the recent tweets
 * of a user along with an infoBox which shows informations about the user.
 *
 * @class com.conjoon.service.twitter.TwitterUserContainer
 * @extends Ext.BoxComponent
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
com.conjoon.service.twitter.TwitterUserContainer = Ext.extend(Ext.BoxComponent, {

    /**
     * @cfg {com.conjoon.service.twitter.UserInfoBox} userInfoBox The UserInfoBox
     * to use for this container.
     */

    /**
     * @cfg {com.conjoon.service.twitter.TweetList} usersRecentTweets The TweetList
     * to use for this container.
     */

    /**
     * @cfg {Object} autoEl
     */
    autoEl : {
        cls      : 'com-conjoon-service-twitter-TwitterUserContainer',
        tag      : 'div',
        children : [{
            tag : 'div',
            cls : 'viewContainer'
        }]
    },

    /**
     * @type {HtmlElement} _viewContainer Native HtmlElement that wraps the TweetList.
     * @protected
     */
    _viewContainer : null,

    /**
     * Overriden to initialize custome elements and attach the listeners.
     *
     */
    afterRender : function()
    {
        com.conjoon.service.twitter.TwitterUserContainer.superclass.afterRender.call(this);

        this._viewContainer = this.el.dom.firstChild;

        this.usersRecentTweets.render(this._viewContainer);
        this.userInfoBox.render(this.el.dom);
        this.el.dom.insertBefore(this.userInfoBox.el.dom, this._viewContainer);

        this.syncSize();

        this.mon(this.userInfoBox, 'userload', function(userInfoBox, record) {
            this.syncSize();
        }, this);
    },

    /**
     * Calls parent's implementation and adjusts the height of _viewContainer to
     * resize TweetList properly.
     *
     * @param {Number} adjWidth
     * @param {Number} adjHeight
     * @param {Number} rawWidth
     * @param {Number} rawHeight
     *
     */
    onResize : function(adjWidth, adjHeight, rawWidth, rawHeight)
    {
        com.conjoon.service.twitter.TwitterUserContainer.superclass.onResize.call(
            this, adjWidth, adjHeight, rawWidth, rawHeight
        );

        this._viewContainer.style.height = (this.el.dom.offsetHeight - this.userInfoBox.el.dom.offsetHeight)+"px";
    }

});