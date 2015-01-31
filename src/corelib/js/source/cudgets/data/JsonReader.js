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

Ext.namespace('com.conjoon.cudgets.data');

/*@REMOVE@*/
if (Ext.version != '3.4.0') {
    throw("Using Ext "+Ext.version+" - please check overrides in com.conjoon.cudgets.data.JsonReader");
}
/*@REMOVE@*/

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