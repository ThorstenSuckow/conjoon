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

Ext.namespace('com.conjoon.cudgets.direct');

/**
 * A helper class for easing the process of calling functions based on collected
 * data from batched responses.
 * See http://www.extjs.com/forum/showthread.php?t=84090.
 *
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.cudgets.direct.BatchedResponseHelper
 * @singleton
 */
com.conjoon.cudgets.direct.BatchedResponseHelper = function() {

    var defaultTimeout = 100;

    var currentConfig = {};

    /**
     * Called in the scope of this singleton.
     */
    var processIfResultValidTask = function(id)
    {
        if (!currentConfig[id]) {
            throw(
                "com.conjoon.cudgets.direct.BatchedResponseHelper."
                +"processIfResultValidTask(): "+id+" was not found in the "
                +"currentConfig property"
            );
        }

        var cc = currentConfig[id];

        if (cc.result.indexOf(false) == -1) {
            cc.success.call(cc.scope);
        } else {
            cc.failure.call(cc.scope);
        }

        this.clearConfigForId(id);
    };


    return {

        /**
         * Checks if config.result contains "false" or onyl "true" and calls either
         * config.failure or config.success. The callbacks will be called after
         * a given interval so outstanding objects have the ability to alter
         * the behavior.
         *
         * @param {Object} config
         *  - success The fucntion to execute when all result properties
         *    were true
         *  - failure The function to call if not all result properties were
         *    set to "true"
         *  - timeout The time in ms to wait before fn is called. Defaults to
         *    defaultTimeout
         *  - result The actual value of the success property
         *  - scope The scope in which success/failure is to be called
         *  - id The id used to identify further calls to this
         *    method and the given config to properly call success/failure
         *
         */
        processIfResultValid : function(config)
        {
            Ext.applyIf(config, {
                timeout : defaultTimeout,
                scope   : window
            });

            var id = config.id;
            if (!currentConfig[id]) {
                currentConfig[id] = {
                    success : config.success,
                    failure : config.failure,
                    scope   : config.scope,
                    result  : [],
                    task    : null
                };
            }

            currentConfig[id]['result'].push(config.result);

            if (currentConfig[id]['task']) {
                window.clearTimeout(currentConfig[id]['task']);
            }

            currentConfig[id]['task'] = processIfResultValidTask.defer(
                config.timeout, this, [id]
            );
        },

        /**
         * Clears any timeout associated with the given id and removes the
         * configuration for this id entirely.
         *
         * @param {String} id The id for which previously a config was set up
         */
        clearConfigForId : function(id)
        {
            if (currentConfig[id]) {
                if (currentConfig[id]['task']) {
                    window.clearTimeout(currentConfig[id]['task']);
                }
                currentConfig[id] = null;
                delete currentConfig[id];
            }
        }



    };

}();