/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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