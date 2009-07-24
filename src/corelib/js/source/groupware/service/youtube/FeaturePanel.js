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


Ext.namespace('com.conjoon.groupware.service.youtube');

/**
 *
 *
 * @class com.conjoon.groupware.service.youtube.FeaturePanel
 * @extends Ext.Panel
 */
com.conjoon.groupware.service.youtube.FeaturePanel = Ext.extend(Ext.Panel, {

    initComponent : function()
    {
        Ext.apply(this, {
            layout   : 'fit',
            title    : "Youtube",
            closable : true,
            border   : true,
            iconCls  : 'com-conjoon-service-youtube-youtubeIcon'
        });

    }

});