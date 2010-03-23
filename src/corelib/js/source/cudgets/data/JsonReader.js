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

Ext.namespace('com.conjoon.cudgets.data');

if (Ext.version != '3.1.1') {
    throw("Using Ext "+Ext.version+" - please check overrides in com.conjoon.cudgets.data.JsonReader");
}

com.conjoon.cudgets.data.JsonReader = Ext.extend(Ext.data.JsonReader, {

    /**
     * @ext bug 3.0.3
     * @see http://www.extjs.com/forum/showthread.php?p=400495
     *
     * returns extracted, type-cast rows of data.  Iterates to call #extractValues for each row
     * @param {Object[]/Object} data-root from server response
     * @param {Boolean} returnRecords [false] Set true to return instances of Ext.data.Record
     * @private
     */
    extractData : function(root, returnRecords) {
        if (!Ext.isArray(root)) {
            throw(
                "com.conjoon.cudgets.data.JsonReader.extractData() - "
                +"\"root\" does not seem to be valid data"
            );
        }

        return com.conjoon.cudgets.data.JsonReader.superclass.extractData.call(
            this, root, returnRecords
        );
     },

    /**
     * @ext bug 3.0.3
     * @see http://www.extjs.com/forum/showthread.php?p=400495
     *
     * type-casts a single row of raw-data from server
     * @param {Object} data
     * @param {Array} items
     * @param {Integer} len
     * @private
     */
    extractValues : function(data, items, len) {
        if (!Ext.isObject(data)) {
            throw(
                "com.conjoon.cudgets.data.JsonReader.extractValues() - data "
                +"seems not to be a Json-encoded Object"
            );
        }

        return com.conjoon.cudgets.data.JsonReader.superclass.extractValues.call(
            this, data, items, len
        );
    }

});