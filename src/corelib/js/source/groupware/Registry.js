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

Ext.namespace('com.conjoon.groupware');

/**
 * The Registry's purpose is to send application specific settings to the
 * client, and query those settings using specific keys.
 * In addition, values for keys can also be edited using the "setValue()" method,
 * which also invokes server action by sending only those values to the server
 * which actually have changed.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.groupware.Registry
 * @singleton
 */
com.conjoon.groupware.Registry = function() {

    /**
     * @type {Boolean} isInitialized set to true once a loading request was made
     * to load the registry.
     */
    var isInitialized = false;

    /**
     * @type {Array} initCallbacks An array of callbacks that should be called once
     * the Registry is available.
     */
    var initCallbacks = [];

    /**
     * @type {Array} beforeloadCallbacks An array of callbacks that should be called
     * before the Registry gets initialized, i.e. before the Registry gets its data
     * from the server
     */
    var beforeloadCallbacks = [];

    /**
     * @type {Ext.tree.TreeNode} rootNode since the Registry's internal structure is
     * a tree structure, entries will be stored within nodes. rootNode has no other
     * purpose than to represent the root node of the registry, as the name already
     * tells (and which makes me wonder why I'm writing so much about a self
     * explainary variable...)
     */
    var rootNode = new Ext.tree.TreeNode({
        text : com.conjoon.Gettext.gettext("Registry")
    });

    /**
     * @type {Array} modified Keeps track of all modified Registry entries. This array
     * will store the original values prior to editing a key/value pair.
     */
    var modified  = [];

    /**
     * @type {Array} pathCache caches already queried keys so they do not have to
     * be looked up a second time.
     */
    var pathCache = [];

    /**
     * Function gets called once the request to load the Registry's values from
     * the server was successfull and will recursively build the tree with the
     * key/value pairs.
     *
     * @param {Array} entries The entries of the Registry as returned by the server
     * @param {Mixed} parentId The current parent id, 0, if the entries to build are
     * on their first level
     * @param {Ext.tree.TreeNode} parentNode The parent node to which the entries get
     * appended (rootNode if entries to build are on their first level)
     */
    var initRegistry = function(entries, parentId, parentNode)
    {
        if (!parentId) {
            parentId   = 0;
            parentNode = rootNode;
        }

        var entry = null;
        var node  = null;
        for (var i = 0, len = entries.length; i < len; i++) {
            entry = entries[i];

            if (entry['parentId'] == parentId) {

                var values = entry['values'];
                for (var a = 0, lena = values.length; a < lena; a++) {
                    values[a]['value'] = castValue(
                        values[a]['value'], values[a]['type']
                    );
                    values[a]['isEditable'] = castValue(
                        values[a]['isEditable'], 'BOOLEAN'
                    );
                }

                node = new Ext.tree.TreeNode({
                    text     : entry['key'],
                    id       : entry['id'],
                    parentId : entry['parentId'],
                    values   : values
                });
                parentNode.appendChild(node);
                initRegistry(entries, entry['id'], node);
            }
        }

    };

    /**
     * Normalizes the passed key.
     *
     * @param {String} key
     *
     * @return {String}
     */
    var normalizeKey = function(key)
    {
        return key.indexOf('/') === 0 ? key.substr(1) : key;
    };

    /**
     * Casts a value to the given type. Only primitive datatypes such as INTEGER,
     * BOOLEAN and STRING are supported.
     *
     * @param {Mixed} value The value to cast
     * @param {String} type The primitive datatype to cast to. Supported values
     * are: STRING, INTEGER, BOOLEAN
     *
     * @return {Mixed}
     *
     * @throws {Exception} if the specified type is not supported
     */
    var castValue = function(value, type)
    {
        switch(type) {
            case 'STRING':
                return "" + value;

            case 'BOOLEAN':
                switch (value) {
                    case 'false':
                    case 'FALSE':
                    case '0':
                        return false;
                    case 'true':
                    case 'TRUE':
                    case '1':
                        return true;
                    default:
                        return value ? true : false;
                }

            case 'INTEGER':
                return parseInt(value, 10);
            default:
                throw(
                    String.format(
                        "com.conjoon.groupware.Registry[@.castValue()]: "
                        +"type \"{0}\""
                        +" is not supported",
                        type
                    )
                 );
        }

        return value;
    };

    /**
     * Returns the parent node for the specified key. I.e. if the
     * key is "/key1/key2/value", the node found under the path
     * "/key1/key2" is returned.
     * Returns null if the node cannot be found.
     *
     * @param {String} key
     *
     * @return {Mixes} Ext.tree.TreeNode if the node was found, otherwise
     * null
     */
    var getParentNodeForKey = function(key)
    {
        key        = normalizeKey(key);
        var keys   = key.split('/');
        keys.pop();

        if (keys.length == 0) {
            throw("Cannot return parent node for \""+key+"\"");
        }

        var orgKey = keys.join('/');
        if (!pathCache[orgKey]) {
            var node   = rootNode;
            var pCache = [];
            key        = keys.shift();
            var traversed = [];
            while (true) {

                node = node.findChild('text', key);

                if (!node) {
                    delete pathCache[orgKey];
                    return null;
                }

                traversed.push(key);
                pathCache[traversed.join('/')] = node;

                if (keys.length == 0) {
                    break;
                }

                key = keys.shift();
            }
        }

        return pathCache[orgKey];
    };

    /**
     * Commits outstanding records to the registry.
     *
     */
    var commit = function()
    {
        modified = [];
    };

    /**
     * Commits outstanding changes and simultaneously rejects changes for keys
     * as found in "keys"
     *
     * @param {Array} keys A list of keys that should get reset to
     * their original value.
     */
    var commitWithout = function(keys)
    {
        for (var i = 0, len = keys.length; i < len; i++) {
            keys[i] = normalizeKey(keys[i]);
        }

        for (var i = 0, len = modified.length; i < len; i++) {
            if (keys.indexOf(modified[i]['key']) != -1) {
                setValue(modified[i]['key'], modified[i]['value'])
            }
        }

        modified = [];
    };

    /**
     * Rejects any outstanding changes made to registry entries.
     *
     */
    var reject = function()
    {
        for (var i = 0, len = modified.length; i < len; i++) {
            setValue(modified[i]['key'], modified[i]['value']);
        }
    };

    /**
     * Registers an registry key that's about to get modified and stores its
     * current (i.e. unchanged) value locally, so it can be reset to its value
     * afterwards.
     *
     * @param {String} key
     * @param {Mixed} value
     */
    var registerModified = function(key, value)
    {
        key = normalizeKey(key);

        var found = false;
        for (var i = 0, len = modified.length; i < len; i++) {
            if (modified[i]['key'] == key) {
                modified[i]['value'] = value;
                found = true;
                break;
            }
        }

        if (!found) {
            modified.push({
                key   : key,
                value : value
            });
        }

    };

    /**
     * Sets the specified value for the key. The value will be internally
     * casted to its type as specified for this key.
     * A call to this method will not automatically register the key's previous
     * value so the key can be rejected to its original value; this is something
     * public implementations have to take care of.
     *
     * @param {String} key
     * @param {Mixed} value
     *
     * @return {Object} based on the state of the key in the registry, the
     * returned object holds several properties which indicate the process of
     * changing the key.
     * Possible properties in this object can equal to one or more of the
     *  following:
     * - modified: either set to true or false according to the changes made
     *             to the value. This can equal to false in cases where
     *             the new value for the key equals to its previous value.
     * - orgValue: The value of the key before it was changed, if "modified"
     *             was set to true or false.
     * - newValue: The new value of the key, if modified was set to true or
     *             false.
     * - found: explicitly set to false if the value was not found. This has
     *          to be checked first since other properties of the returning
     *          object may not get explicitly set.
     * - isEditable: explicitly set to false if the key was found but is not
     *               editable due to its configuration. This has to be checked
     *               right after "found" was checked by the implementing API.
     *
     * @throws {Exception} throws an exception if the node representing the
     * "folder" of the value does not exist, i.e. if "/key1/key2/value" is needed,
     * but "/key1/key2" was not found.
     */
    var setValue = function(key, value)
    {
        var keys, valueKey, node, vs, org, tmp = null;

        key = normalizeKey(key);

        keys     = key.split('/');
        valueKey = keys[keys.length-1];
        node     = getParentNodeForKey(key);

        // make sure node is available
        if (!node) {
            throw("Key \""+key+"\" not available in Registry");
        }

        vs = node.attributes.values;

        var found = false;

        // search for an existing key in the values property of
        // the registry key
        for (a = 0, lena = vs.length; a < lena; a++) {
            if (vs[a]['name'] == valueKey) {
                found = true;
                if (!vs[a]['isEditable']) {
                    return {
                        isEditable : false
                    };
                }

                // update the value in the values property
                tmp = castValue(value, vs[a]['type']);
                if (tmp !== vs[a]['value']) {
                    org            = vs[a]['value'];
                    vs[a]['value'] = tmp;

                    return {
                        modified : true,
                        orgValue : org,
                        newValue : tmp
                    };

                } else {
                    return {
                        modified : false,
                        found    : true,
                        orgValue : tmp,
                        newValue : tmp
                    };
                }
            }
        }

        if (!found) {
            return {
                found : false
            };
        }

    };

    return {

        /**
         * Method allows for adding callbacks to the Registry which get called
         * before the Registry is about to send a request to the server to get
         * its initial data.
         * This method can be called multiple time - each time its called, the
         * configuration as passed to this method gets stored internally.
         * The callback queue works as a FIFO queue.
         *
         * @param {Object} callbackConfig An object with the following properties:
         * - fn: The function to call before the Registry loads
         * - scope: The scope in which "fn" should get called.
         *
         * @throws {Exception} Throws an exception if the Registry was already
         * initialized
         */
        beforeLoad : function(callbackConfig)
        {
            if (isInitialized) {
                throw("Could not add \"beforeload\"-callback to Registry - Registry already initialized");
            }

            beforeloadCallbacks.push(callbackConfig);
        },

        /**
         * Tells the Registry to finally send a server request to load the
         * Registry's data. An additional callback configuration can be passed
         * to the method that should get called once loading the Registry was
         * loaded _successfully_.
         *
         * @param {Object} callbackConfig An object with the following properties:
         * - fn: The function to call once the Registry was loaded successfully
         * - scope: The scope in which "fn" should get called.
         *
         *
         * @throws {Exception} throws an exception if the Registry was not loaded
         * successfully, due to a server error or an error that occurred while
         * trying to parse the response.
         */
        load : function(callbackConfig)
        {
            if (isInitialized) {
                throw("Could not load Registry since it was aready initialized");
            }

            isInitialized = true;

            initCallbacks.push(callbackConfig);

            var cb = null;
            for (var i = 0, len = beforeloadCallbacks.length; i < len; i++) {
                cb = beforeloadCallbacks[i];
                cb['fn'].call(cb['scope'] ? cb['scope'] : window);
            }

            com.conjoon.defaultProvider.registry.getEntries(function(provider, response){
                if (!response.status) {
                    throw("Unexpected error. Could not load Registry.");
                }

                var entries = response.result.entries;

                initRegistry(entries);

                cb = null;
                for (var i = 0, len = initCallbacks.length; i < len; i++) {
                    cb = initCallbacks[i];
                    cb['fn'].call(cb['scope'] ? cb['scope'] : window);
                }

            });
        },

        /**
         * Get's the value for the specified key.
         * Returns null if the key was not found.
         *
         * @param {String} key
         *
         * @return {Mixed}
         */
        get : function(key)
        {
            key      = normalizeKey(key);
            var node = getParentNodeForKey(key);

            if (!node) {
                return null;
            }

            key = key.split('/').pop();

            var values = node.attributes.values;
            if (values) {
                for (var i = 0, len = values.length; i < len; i++) {
                    if (values[i]['name'] == key) {
                        return values[i]['value'];
                    }
                }
            }

            return null;
        },

        /**
         * Sets the registry entries and does trigger a server request to store
         * the settings in the datastorage.
         * An error is thrown if any of the passed values could not be mapped to
         * entries in the registry.
         *
         * @param {Object} config An object with the following key/value pairs:
         * - values: Array an array with key/value pairs
         * - beforewrite: function to be called before data gets send to the
         * server. You can return "false" with this callback - no data will be send
         * to the server then. However, registry values will be changed locally
         * and any changes will be committed.
         * - success: function to be called when the response successfully passed
         * through, and the response's "success" value was set to true, which
         * indicates that _all_ entries have been saved successfully.
         * The arguments passed to the configured listeners are:
         *     - response: the response object as created for the server response
         *     - updated: the list of saved key/value pairs
         *     - failed: the list of key-value pairs that could not be saved
         * - failure: function to be called when either communicating with the
         * server  failed, or the success property of the result was set to "false",
         * which indicates that one or more values could not be saved on the server.
         * The arguments passed to the configured listeners are:
         *     - response: the response object as created for the server response
         *     - updated: the list of saved key/value pairs
         *     - failed: the list of key/value pairs that could not be saved
         * Note, that values which could not be saved will be reset to their
         * original value before they were changed automatically.
         * - scope: the scope in which the functions should be called
         *
         * @return {Boolean} true if there are values changed which nvokes a server
         * request, otherwise false
         *
         * @throws {Exception} throws an exception if the key is not editable,
         * or if the key was not found.
         */
        setValues : function(config)
        {
            var values   = config.values;
            var cfValues = [];

            var key, value, states = null;

            for (var i = 0, len = values.length; i < len; i++) {

                key   = normalizeKey(values[i]['key']);
                value = values[i]['value'];

                states = setValue(key, value);

                if (states.isEditable === false) {
                    // value is not editable! throw exception!
                    throw("Registry key \""+key+"\" is not editable");
                }

                if (states.found === false) {
                    // value is not available in the registry, add it to the values and
                    // assume it gets saved as type 'STRING' if the config for a call
                    // to this method did not hold the type
                    throw(
                        "Key \""+key+"\" not found in Registry"
                    );
                    /*
                    if (values[i]['type']) {
                        cfValues.push({
                            key   : key,
                            value : castValue(value, values[i]['type']),
                            type  : values[i]['type']
                        });
                    } else {
                        cfValues.push({
                            key   : key,
                            value : castValue(value, 'STRING'),
                            type  : 'STRING'
                        })
                    }*/
                }

                // add the value to the request config, if the new value differs
                // from the current value
                if (states.modified === true) {
                    registerModified(key, states.orgValue);
                    cfValues.push({
                        key   : key,
                        value : states.newValue
                    });
                }

            }

            if (cfValues.length != 0) {

                var scope = config.scope ? config.scope : window;

                if (config.beforewrite) {
                    var send = config.beforewrite.call(scope, cfValues);

                    if (send === false) {
                        commit();
                        return false;
                    }
                }

                com.conjoon.defaultProvider.registry.setEntries(
                    cfValues,
                    function(provider, response) {

                        var succ = com.conjoon.groupware.ResponseInspector.isSuccess(response);

                        if (succ === null) {
                            reject();
                            if (config.failure) {
                                config.failure.call(scope, response, [], cfValues);
                            }
                        } else if (succ === false) {
                            var failed = response.result.failed;
                            var upd    = [];
                            var ff     = [];

                            commitWithout(failed);

                            for (var i = 0, len = cfValues.length; i < len; i++) {
                                if (ff.indexOf(cfValues[i]['key']) != -1) {
                                    ff.push(cfValues[i]);
                                } else {
                                    upd.push(cfValues[i]);
                                }
                            }

                            if (config.failure) {
                                config.failure.call(scope, response, upd, ff);
                            }
                        } else {
                            commit();

                            if (config.success) {
                                config.success.call(scope, response, cfValues, []);
                            }
                        }
                    }
                );

                return true;
            }

            return false;
        },

        /**
         * Returns the passed registry key, normalized.
         *
         * @param {String} key
         *
         * @return {String}
         */
        normalizeKey : function(key)
        {
            return normalize(key);
        }

    };

}();