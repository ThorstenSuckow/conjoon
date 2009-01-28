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
     * @cfg {Ext.Xtemplate} tpl template used to render the contents of this
     * component.
     */

    initComponent : function()
    {
        this.addEvents(
            /**
             * @event userload
             * @param {com.conjoon.service.twitter.UserInfoBox} userInfoBox
             * @param micced tweetRecord or TwitterUserRecord
             */
            'userload'
        );

        Ext.applyIf(this, {
            tpl : new Ext.XTemplate('<table cellspacing="0" cellpadding="0">' +
                  '<tbody>',
                  '<tr class="meta">',
                  '<td class="image" colspan="2" style="background-image:url({profileImageUrl})">',
                  '<div class="screenName">{screenName}</div>',
                  '<div class="name"><span class="label">Name:</span> {name}</div>',
                  '</td>',
                  '<tr class="location">',
                  '<td class="label">Location:</span></td>',
                  '<td class="value">{location}</td>',
                  '</tr>',
                  '<tr class="web">',
                  '<td class="label">Web:</span></td>',
                  '<td class="value">{url}</td>',
                  '</tr>',
                  '<tr class="description">',
                  '<td class="label">Bio:</span></td>',
                  '<td class="value">{description}</td>',
                  '</tr>',
                  '</tbody>',
                  '</table>'
            )
        });

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
     */
    loadUser : function(record)
    {
        this.tpl.overwrite(this.el, record.data);
        this.fireEvent('userload', this, record);
    }

});