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

Ext.namespace('com.conjoon.iphone.service.twitter');

/**
 * A component for displaying the startscreen of a twitter application.
 *
 * @class com.conjoon.service.twitter.HomePanel
 * @extends Ext.BoxComponent
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
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
                       '(c) 2009 <a href="http://conjoon.org" target="_blank">conjoon open source project</a><br />'+
                       '<br />'+
                       '"Twitter" is a service by <a target="_blank" href="http://www.twitter.com">Twitter, Inc.</a>, San Francisco, USA<br /><br />'+
                       'This client is powered by the <br /><a target="_blank" href="http://www.extjs.com">Ext JS framework</a>'
            }]
        }]
    }

});