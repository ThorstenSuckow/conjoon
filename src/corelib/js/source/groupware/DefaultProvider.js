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

Ext.Direct.addProvider({
    enableUrlEncode : 'extDirectData',
    url             : './default',
    format          : 'json',
    type            : 'zend',
    actions         : {
        registry : [{
            name : 'getEntries',
            len  : 0
        }],
        reception : [{
            name : 'getUser',
            len  : 0
        }, {
            name : 'ping',
            len  : 0
        }, {
            name : 'logout',
            len  : 0
        }, {
            name : 'lock',
            len  : 0
        }]
    },
    namespace : 'com.conjoon.default.provider'
});