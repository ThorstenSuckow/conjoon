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