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
    id              : 'com.conjoon.groupware.provider',
    enableUrlEncode : 'extDirectData',
    url             : './groupware',
    format          : 'json',
    type            : 'zend',
    actions         : {
        feedsItem   : [{
            name    : 'getFeedItems',
            len     : 1
        }],
        feedsAccount : [{
            name : 'getFeedAccounts',
            len  : 0
        }],
        emailAccount : [{
            name   : 'getEmailAccounts',
            len    : 0
        }],
        emailImapMapping : [{
            name   : 'getMappings',
            len    : 0
        }/*, {
            name   : 'addAccount',
            len    : 1
        }, {
            name   : 'updateAccount',
            len    : 1
        }, {
            name   : 'removeAccount',
            len    : 1
        }*/]
    },
    namespace : 'com.conjoon.groupware.provider'
});