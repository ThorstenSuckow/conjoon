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

Ext.namespace('com.conjoon.groupware.localCache');

/**
 *
 * @class com.conjoon.groupware.localCache.Baton
 * @singleton
 */
com.conjoon.groupware.localCache.Baton = function() {

    var dialog = null;

    return {

        createCache : function()
        {


        },

        removeCache : function()
        {

        },

        showDialog : function()
        {
            if (dialog) {
                dialog.show();
                return;
            }

            dialog = new com.conjoon.groupware.localCache.options.Dialog();
            dialog.on('close', function() {
                dialog = null;
            });
            dialog.show();
        }

    };


}();
