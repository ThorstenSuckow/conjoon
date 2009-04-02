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

Ext.namespace('com.conjoon.util');

/**
 * @class com.conjoon.util.Format
 * @singleton
 *
 * Class provides convenient methods for formatting strings/numbers.
 */
com.conjoon.util.Format = function() {

    return {

        /**
         * Looks up URLS and in the given text and wraps them in a-tags.
         *
         * For example:
         * "This is a text with an url in the form of http://domain.com"
         * will be returned as
         * "This is a text with an url in the form of <a href="http://domain.com>http://domain.com</a>"
         *
         * @param {String} text The text that may contains URLS.
         * @param {Object} attributes An optional object that contains attribute/value
         * pairs to be added to the generated link-tags.
         *
         * @return {String}
         */
        formatUrls : function(text, attributes)
        {
            var attrStr = "";
            if (attributes) {
                for (var i in attributes) {
                    attrStr += " "+i+"="+'"'+attributes[i]+'"';
                }
            }

            text = ""+text;
            return text.replace(
                /((www\.|(http|https|ftp|news|file)+\:\/\/)[_.a-z0-9-]+\.[a-z0-9\/_:@=.+?,##%&~-]*[^.|\'|\# |!|\(|?|,| |>|<|;|\)])/ig,
                '<a '+attrStr+' href="$1">$1</a>'
            );
        },


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