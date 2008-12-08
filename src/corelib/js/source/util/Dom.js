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
 * Utility functions for DOM operations not provided by the ExtJs-framework.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.util.Dom = function() {



    return {

        /**
         * Returns the cssText property of a cssRule-object for the passed style-name.
         * The method will fetch the cssText-property if the cssText begins with
         * "styleName" efr the opening paranthesis. An object can be passed to the method
         * which will ignore each stylesheet which property matches the property defined
         * in "exclude". Wildcards can broaden the search so one does not have to specifiy
         * the excludevalues exactly, which is helpful in situations where for example one
         * wants to exclude all stylesheets from the search that where included using a specified
         * "href"-value, but where the domain-name path to the css file is unknown.
         * Example:
         * <code>
// search for the cssText which definition is ".bodyElement",
// but ignore the ext-all.css stylesheet
var cssText = com.conjoon.util.Dom.getCssTextFromStyleSheet(
    '.bodyElement', {
        href : '*ext-all.css';
    }
);
         </code>
         *
         * Note: this method depends on the trim() function as defined by the ExtJs framework.
         *
         * @param {String} styleName The name the definition starts with.
         * @param {Object} excludes An optional object providing key/value pairs of attributes
         * used for excluding a stylesheet in the search-process
         *
         * @return {String} The cssText-property found, or an empty string if not
         * found
         */
        getCssTextFromStyleSheet : function(styleName, excludes)
        {
            styleName = styleName.toLowerCase().trim();
            var styleSheets   = document.styleSheets;
            var styleSheet    = null;
            var excludeValue  = "";
            var wildcardIndex = -1;
            var excludeValueLength = 0;
            var attributeValue = "";
            var exclLength = 0;
            var matches    = 0;
            var cssRules   = null;
            var cssText    = null;
            for (var i = 0, len = styleSheets.length; i < len; i++) {
                styleSheet = styleSheets[i];
                // check if we can ignore this
                if (excludes) {
                    exclLength = 0;
                    matches    = 0;
                    for (var attribute in excludes) {
                        attributeValue = styleSheet[attribute];
                        if (attributeValue) {
                            excludeValue  = excludes[attribute];
                            wildcardIndex = excludeValue.indexOf('*');
                            excludeValueLength = excludeValue.length;
                            // wildcard found at beginning of string
                            if (wildcardIndex === 0) {
                                excludeValueLength--;
                                excludeValue = excludeValue.substring(1);
                                if (attributeValue.lastIndexOf(excludeValue) == attributeValue.length - excludeValueLength) {
                                    matches++;
                                }
                            // wildcard found at end of string
                            } else if (wildcardIndex == excludeValueLength-1) {
                                excludeValueLength--;
                                excludeValue = excludeValue.substring(0, excludeValueLength-1);
                                if (attributeValue.indexOf(excludeValue) == 0) {
                                    matches++;
                                }
                            } else {
                                if (attributeValue == excludeValue) {
                                    matches++;
                                }
                            }
                        }
                        exclLength++;
                    }
                    // all matched?
                    if (exclLength != 0 && exclLength == matches) {
                        continue;
                    }
                }

                // exclude did not match, process cssRules
                cssRules = Ext.isIE ? styleSheet.rules : styleSheet.cssRules;
                for (var a = 0, lena = cssRules.length; a < lena; a++) {
                    cssText = cssRules[a].selectorText.toLowerCase();
                    if (cssText == styleName) {
                        return Ext.isIE
                              ? cssText + '{' + cssRules[a].style.cssText + '}'
                              : cssRules[a].cssText;
                    }
                }
            }

            return "";

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