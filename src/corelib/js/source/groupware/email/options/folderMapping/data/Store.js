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

Ext.namespace('com.conjoon.groupware.email.options.folderMapping.data');

/**
 * A store for managing local/IMAP folder mappings.
 *
 * @class com.conjoon.groupware.email.options.folderMapping.data.Store
 * @singleton
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
com.conjoon.groupware.email.options.folderMapping.data.Store = function() {

    var _store = null;

    var _getStore = function() {
        return new Ext.data.Store({
            storeId : Ext.id(),
            proxy   : new com.conjoon.cudgets.data.DirectProxy({
                api : {
                    read : com.conjoon.groupware.provider.emailFolderMapping.getMappings
                }
            }),
            autoLoad : false,
            reader   : new com.conjoon.cudgets.data.JsonReader({
                    root            : 'mappings',
                    id              : 'id',
                    successProperty : 'success',
                }, com.conjoon.groupware.email.options.folderMapping.data.Record)
        });
    };


    return {

        getInstance : function()
        {
            if (!_store) {
                _store = _getStore();
            }

            return _store;
        }

    }

}();