/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author$
 * $Id$
 * $Date$ 
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$ 
 */

/**
 * File needs to be loaded right after the Ext-framework has been loaded.
 * Sets some basic properties needed by ExtJs.
 */
 
/**
 * URL to a 1x1 transparent gif image used by Ext to create inline icons with 
 * CSS background images.
 */ 
Ext.BLANK_IMAGE_URL = "./s.gif";

/**
 * URL to a blank file used by Ext when in secure mode for iframe src and onReady 
 * src to prevent the IE insecure content warning 
 */
Ext.SSL_SECURE_URL  = "./blank.html";

/**
 * Setting the default timeout to a higher value gives requests a higher timeout 
 * tolerance when application used with slower systems, such as from a mobile
 * USB 1.0 device. 
 */
//Ext.override(Ext.data.Connection, {
//    timeout : 120000
//});
