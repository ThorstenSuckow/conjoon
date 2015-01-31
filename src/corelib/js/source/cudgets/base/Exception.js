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

/**
 * Base exception class.
 *
 * @class {cudgets.base.Exception}
 */
Ext.defineClass('cudgets.base.Exception', {

    /**
     * The message for this exception.
     * @type {String}
     */
    message : null,

    /**
     * Creates a new instance of this class.
     *
     * @param obj
     */
    constructor : function(obj) {

        if (!Ext.isObject(obj)) {
            this.message = arguments[0];
        } else {
            Ext.apply(this, obj)
        }

    },

    /**
     * Returns a textual representation of this exception.
     *
     * @return {String}
     */
    toString : function() {

        var className = "[unresolved exception name]";

        try {
            className = Ext.getClassName(this);
        } catch (e) {
            // ignore, use default className
        }

        return className + ": " + this.message;

    }



});
