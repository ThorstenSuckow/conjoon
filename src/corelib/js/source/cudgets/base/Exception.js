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
