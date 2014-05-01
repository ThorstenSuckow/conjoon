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

Ext.namespace('com.conjoon.util');

com.conjoon.util.Record = function(){


    return {

        convertTo : function(recordClass, data, id)
        {
            var rec = new recordClass({}, id);
            rec.fields.each(
                function(field) {
                    /**
                     * @see CN-850
                     */
                    rec.set(field.name, data[field.name]);

                    rec.data[field.name] = field.type
                                           ? field.convert(rec.get(field.name))
                                           : rec.get(field.name);
                }
            );

            return rec;
        }


    }


}();
