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
 * An concrete Application Cache implementation for use with
 * com.conjoon.cudgets.localCache.Api based on HTML5 specifications.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.groupware.localCache.Html5Adapter
 * @extends com.conjoon.cudgets.localCache.Adapter
 */
com.conjoon.groupware.localCache.Html5Adapter = Ext.extend(com.conjoon.cudgets.localCache.Adapter, {




// -------- com.conjoon.cudgets.localCache.Adapter

    /**
     * @return {Boolean}
     */
    isCacheAvailable : function()
    {
        return window.applicationCache ? true : false;
    },

    /**
     * @return {String}
     */
    getCacheType : function()
    {
        return 'HTML5 Application Cache';
    },

    /**
     *
     */
    clearCache : function()
    {
        // fire the beforeclear event
        this.fireEvent('beforeclear', this);

        com.conjoon.defaultProvider.applicationCache.setClearFlag(
            {clear : true},
            function(provider, response) {
                var succ = com.conjoon.groupware.ResponseInspector.isSuccess(response);
                if (succ === null || succ === false) {
                    this.fireEvent('clearfailure', this);
                } else {

                    window.applicationCache.swapCache();
                    window.applicationCache.update();
                    /**
                     * @todo need listener to trigger setting flag to false
                     */
                    /*com.conjoon.defaultProvider.applicationCache.setClearFlag(
                        {clear : false},
                        function(provider, response) {
                            var succ = com.conjoon.groupware.ResponseInspector.isSuccess(response);
                            if (succ === null || succ === false) {
                                this.fireEvent('clearfailure', this);
                            } else {
                                this.fireEvent('clearsuccess', this);
                            }
                        },
                        this
                    );*/
                }

        }, this);
    }

});