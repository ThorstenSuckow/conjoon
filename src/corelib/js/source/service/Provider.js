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

Ext.Direct.addProvider({
    enableUrlEncode : 'extDirectData',
    url             : '/service',
    format          : 'json',
    type            : 'zend',
    actions         : {
        twitterAccount : [{
            name   : 'getAccounts',
            len    : 0
        }, {
            name   : 'addAccount',
            len    : 1
        }, {
            name   : 'updateAccount',
            len    : 1
        }, {
            name   : 'removeAccount',
            len    : 1
        }]
    },
    namespace : 'com.conjoon.service.provider'
});