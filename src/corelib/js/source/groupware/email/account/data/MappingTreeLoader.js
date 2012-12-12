/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
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

Ext.namespace('com.conjoon.groupware.email.account.data');

/**
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

com.conjoon.groupware.email.account.data.MappingTreeLoader = Ext.extend(
    com.conjoon.groupware.email.EmailTreeLoader, {


    /**
     *
     */
    createNode : function(attr)
    {
        switch (attr.type) {
            case 'root':
            case 'root_remote':
            case 'accounts_root':
            break;
            default:
                if (attr.isSelectable) {
                    Ext.apply(attr, {checked : false});
                }
            break;
        }

        return com.conjoon.groupware.email.account.data
            .MappingTreeLoader.superclass.createNode.call(this, attr);
    }

});