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

Ext.namespace('com.conjoon');

/**
 * @class com.conjoon.Gettext
 * @singleton
 *
 */
com.conjoon.Gettext= function() {

    return {

        /**
         * Returns the passed value. Used for identifying text ids for the server when
         * parsing the scripts for translatable strings.
         *
         * @return {Mixed}
         */
        gettext : function(value)
        {
            return value;
        },

        /**
         * Returns either the first or the second parameter based on the cardinality
         * of the "n".
         * If n is equal to "1", the first parameter will be returned, otherwise
         * teh second.
         *
         * @return {Mixed}
         */
        ngettext : function(value1, value2, n)
        {
            return (n == 1 ? value1 : value2);
        }
    };

}();
