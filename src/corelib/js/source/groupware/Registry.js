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
 * @class com.conjoon.groupware.Registry
 * @singleton
 */
com.conjoon.groupware.Registry = function() {

    var isInitialized = false;

    var initCallbacks = [];

    var beforeloadCallbacks = [];

    var rootNode = new Ext.tree.TreeNode({
        text : 'Registry'
    });

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
                    switch(values[a]['type']) {
                        case 'STRING':
                            values[a]['value'] = "" + values[a]['value'];
                        break;

                        case 'BOOLEAN':
                            var val = values[a]['value'];
                            switch (val) {
                                case 'false':
                                case 'FALSE':
                                case '0':
                                    val = false;
                                break;
                                case 'true':
                                case 'TRUE':
                                case '1':
                                    val = true;
                                break;
                                default:
                                    val = val ? true : false;
                                break;
                            }
                            values[a]['value'] = val;
                        break;

                        case 'INTEGER':
                            values[a]['value'] = parseInt(values[a]['value'], 10);
                        break;
                    }
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

    return {

        beforeLoad : function(callbackConfig)
        {
            if (isInitialized) {
                throw("Could not add \"beforeload\"-callback to Registry - Registry already initialized");
            }

            beforeloadCallbacks.push(callbackConfig);
        },

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
         * @return {mixed}
         */
        get : function(key)
        {
            key = key.indexOf('/') === 0 ? key.substr(1) : key;
            var keys = key.split('/');

            var node = rootNode;
            var key  = keys.shift();
            while (true) {
                node = node.findChild('text', key);
                if (!node) {
                    return null;
                }
                key = keys.shift();

                if (keys.length == 0) {
                    var values = node.attributes.values;
                    if (values) {
                        for (var i = 0, len = values.length; i < len; i++) {
                            if (values[i]['name'] == key) {
                                return values[i]['value'];
                            }
                        }
                    }
                }
            }

            return null;
        }
    };

}();