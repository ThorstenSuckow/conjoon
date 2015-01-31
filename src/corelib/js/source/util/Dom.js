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

Ext.namespace('com.conjoon.util');

/**
 * Utility functions for DOM operations not provided by the ExtJs-framework.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
com.conjoon.util.Dom = function() {

    // shorthand
    var css = Ext.util.CSS;


    return {

        /**
         * Returns the cssText property of a cssRule-object for the passed style-name.
         *
         * @param {String} styleName The name the definition starts with.
         *
         * @return {String} The cssText-property found, or an empty string if not
         * found
         */
        getCssTextFromStyleSheet : function(styleName)
        {
            var rule = css.getRule(styleName);

            if (!rule) {
                return "";
            }

            return Ext.isIE
                  ? styleName + '{' + rule.style.cssText + '}'
                  : rule.cssText;
        },


        /**
         * Returns the href attribute of the given stylesheet without the
         * stylesheet's name.
         * Returns an empty string if the stylesheet cannot be found.
         *
         */
        getHrefFromStyleSheet : function(styleName)
        {
            var styleSheets = document.styleSheets;
            var styleNameLength = styleName.length;
            var href, index;

            for (var i = 0, len = styleSheets.length; i < len; i++) {
                href = styleSheets[i]['href'];
                index = href.lastIndexOf(styleName);
                if (index ===  href.length - styleNameLength) {
                    return href.substring(0, index);
                }
            }

            return "";
        },

        /**
         *
         *
         */
        divideNode : function(splitNode, copyNode, node)
        {
            while (splitNode != node) {
                var parent = splitNode.parentNode;
                if(!parent) {
                    return null;
                }
                var secondHalf = parent.cloneNode(false);
                secondHalf.appendChild(copyNode);
                var nextSibling=splitNode.nextSibling;
                while(nextSibling!=null){
                    parent.removeChild(nextSibling);
                    secondHalf.appendChild(nextSibling);
                    nextSibling=splitNode.nextSibling
                }
                splitNode=parent;
                copyNode=secondHalf;
            }

            return copyNode;
        }

    };


}();