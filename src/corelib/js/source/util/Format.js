/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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
            text = ""+text;

            var attrStr = "";
            if (attributes) {
                for (var i in attributes) {
                    attrStr += " "+i+"="+'"'+attributes[i]+'"';
                }
            }

            return text.replace(
                /((www\.|(http|https|ftp|news|file)+\:\/\/)[_.a-z0-9-]+\.[a-z0-9\/_:@=.+?,##%&~-]*[^.|\'|\# |!|\(|?|,| |>|<|;|\)])/ig,
                function(m, key, value) {
                    if (m == key) {
                        var prots = ['http', 'https', 'ftp', 'news', 'file'];
                        var found = false;
                        for (var i = 0, len = prots.length; i < len; i++) {
                            if (m.indexOf(prots[i]) == 0) {
                                found = true;
                                break;
                            }
                        }

                        return '<a '+attrStr+' href="'+(found ? '' : 'http://')+m+'">'+m+'</a>';
                    } else {
                        return m;
                    }
                }
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