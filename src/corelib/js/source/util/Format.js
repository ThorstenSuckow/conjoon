/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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

Ext.namespace('de.intrabuild.util');

/**
 * @class de.intrabuild.util.Format
 * @singleton
 *
 * Class provides convenient methods for formatting strings/numbers.
 */
de.intrabuild.util.Format = function() {

    return {

        /**
         * Replaces a pair of whitespaces in the passed argument
         * with a pair of " &nbsp;". Whitespaces will not be replaced
         * within opening and closing angle brackets.
         * For example
         * <div class="test">This  i<br />s   a test</div>
         * will become
         * <div class="test">This &nbsp;i<br />s &nbsp; a test</div>
         *
         * @param {String} text
         *
         * @return {String} text
         */
        replaceWhitespacePairs : function(text)
        {
            // simple cast
            text = ""+text;

            return text.replace(
                /((<\/?[^>]+>)| *)/ig,
                function(m, key, value) {
                    if (m == value) {
                        return m;
                    } else {
                        return m.replace(/  /g, " &nbsp;");
                    }
                }
            );
        }


    };

}();