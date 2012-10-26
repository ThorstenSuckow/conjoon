/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
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
