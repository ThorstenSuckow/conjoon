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

Ext.namespace('com.conjoon.iphone.service.twitter');

/**
 * A component for displaying the startscreen of a twitter application.
 *
 * @class com.conjoon.service.twitter.HomePanel
 * @extends Ext.BoxComponent
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.iphone.service.twitter.HomePanel = Ext.extend(Ext.BoxComponent, {

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
                cls  : 'text',
                html : 'conjoonTwitter<br />' +
                       '(c) 2009 <a href="http://www.conjoon.org" target="_blank">conjoon open source project</a><br />'+
                       '<br />'+
                       '"Twitter" is a service by <a target="_blank" href="http://www.twitter.com">Twitter, Inc.</a>, San Francisco, USA<br /><br />'+
                       'This client is powered by the <br /><a target="_blank" href="http://www.extjs.com">Ext JS framework</a>'
            }]
        }]
    }

});