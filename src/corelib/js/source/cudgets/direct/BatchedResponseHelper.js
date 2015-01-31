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

Ext.namespace('com.conjoon.cudgets.direct');

/**
 * A helper class for easing the process of calling functions based on collected
 * data from batched responses.
 * See http://www.extjs.com/forum/showthread.php?t=84090.
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
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
    var collectMessageTask = function(id)
    {
        if (!currentConfig[id]) {
            throw(
                "com.conjoon.cudgets.direct.BatchedResponseHelper."
                +"collectMessageTask(): "+id+" was not found in the "
                +"currentConfig property"
            );
        }

        var cc = currentConfig[id];

        cc.fn.call(cc.scope, cc.messages);

        this.clearConfigForId(id);
    };


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
         * Collects messages and calls fn of the passed config if no other
         * messages are collected within a given interval.
         *
         * @param {Object} config
         *  - fn The function to call once all messages have been collected.
         *    The argument passed will be an array of collected messages
         *  - scope The scope in which fn should be called. Defaults to
         *    window
         *  - timeout The timeout to wait for new messages. If no new messages
         *    within this timeframe have been collected, fn will be called.
         *    Defaults to defaultTimeout
         *  - id The id used to identify the collected messages
         *  - message an object with the following properties: text, title
         *
         * Note: if an object uses the BatchedResponseHelper for various tasks,
         * each task needs a different id
         */
        collectMessage : function(config)
        {
            Ext.applyIf(config, {
                timeout : defaultTimeout,
                scope   : window
            });

            var id = config.id;
            if (!currentConfig[id]) {
                currentConfig[id] = {
                    fn       : config.fn,
                    scope    : config.scope,
                    messages : [],
                    task     : null
                };
            }

            currentConfig[id]['messages'].push(config.message);

            if (currentConfig[id]['task']) {
                window.clearTimeout(currentConfig[id]['task']);
            }

            currentConfig[id]['task'] = collectMessageTask.defer(
                config.timeout, this, [id]
            );
        },

        /**
         * Checks if config.result contains "false" or only "true" and calls either
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
         * Note: if an object uses the BatchedResponseHelper for various tasks,
         * each task needs a different id
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