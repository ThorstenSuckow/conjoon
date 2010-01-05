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
 * A component for displaying the startscreen of a twitter application.
 *
 * @class com.conjoon.service.twitter.HomePanel
 * @extends Ext.BoxComponent
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.service.twitter.HomePanel = Ext.extend(Ext.BoxComponent, {

    /**
     * @cfg {Object} autoEl
     */
    autoEl : {
        tag : 'div',
        cls : 'com-conjoon-service-twitter-HomePanel',
        children : [{
            tag      : 'div',
            cls      : 'introContainer',
            children : [{
                tag  : 'div',
                cls  : 'text'
            }]
        }]
    }

});