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

Ext.namespace('com.conjoon.cudgets.util');

/**
 * A simple approach implementing a global Registry for keeping single
 * instances in a global scope.
 * The singleton allows for adding listeners for "register"/"unregister" events,
 * so components may be aware of the state of the Registry.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.cudgets.util.Registry
 */
com.conjoon.cudgets.util.Registry = function() {


    var kernelImpl = function(){
        this.addEvents(
            'register',
            'unregister'
        );

        kernelImpl.superclass.constructor.call(this);
    };

    Ext.extend(kernelImpl, Ext.util.Observable);

    var kernel = new kernelImpl();

    var map = {};

    return {

        /**
         * Removes a previously registered listener for the "register" event.
         *
         * @param {Function} fn
         * @param {Object} scope
         * @param {Object} options
         */
        removeRegisterListener : function(fn, scope, options)
        {
            kernel.un('register', fn, scope, options);
        },

        /**
         * Removes a previously registered listener for the "register" event.
         *
         * @param {Function} fn
         * @param {Object} scope
         * @param {Object} options
         */
        removeUnregisterListener : function(fn, scope, options)
        {
            kernel.un('unregister', fn, scope, options);
        },

        /**
         * Allows for adding a listener for when objects get registered.
         *
         * @param {Function} fn
         * @param {Object} scope
         * @param {Object} options
         */
        onRegister : function(fn, scope, options)
        {
            kernel.on('register', fn, scope, options);
        },

        /**
         * Allows for adding a listener for when objects get unregistered.
         *
         * @param {Function} fn
         * @param {Object} scope
         * @param {Object} options
         */
        onUnregister : function(fn, scope, options)
        {
            kernel.on('unregister', fn, scope, options);
        },

        /**
         * Adds the specified object to the registry given the specified
         * name. If removeOnDestroy is set to true, the passed object will
         * be inspected whether it supports the "destroy" event, and
         * automatically unregister it once this event fires.
         *
         * @param {String} name
         * @param {Object} obj
         * @param {Boolean} removeOnDestroy
         *
         */
        add : function(name, obj, removeOnDestroy)
        {
            if (map[name]) {
                return;
            }

            map[name] = obj;

            if (removeOnDestroy || (obj.events && obj.events['destroy'])) {
                obj.on('destroy', this.remove.createDelegate(this, [name]), this);
            }

            kernel.fireEvent('register', name, obj);
        },

        /**
         * Removes the object found under this name from the registry.
         *
         * @param {String} name
         */
        remove : function(name)
        {
            if (!map[name]) {
                return;
            }

            delete map[name];

            kernel.fireEvent('unregister', name);
        },

        /**
         * Returns  the specified object to the registry given the specified
         * name. If no value is found and defaultValue is specified, this value
         * will be registered under this name. If removeOnDestroy is set to true,
         * the passed and newly added object will be inspected whether it supports
         * the "destroy" event, and automatically unregister it once this event
         * fires.
         *
         * @param {String} name
         * @param {Object} defaultValue
         * @param {Boolean} removeOnDestroy
         *
         * @return {Mixed}
         */
        get : function(name, defaultValue, removeOnDestroy)
        {
            if (!map[name] && defaultValue) {
                this.add(name, defaultValue, removeOnDestroy);
                return defaultValue;
            } else if (map[name]) {
                return map[name];
            }

            return null;
        }
    };

}();