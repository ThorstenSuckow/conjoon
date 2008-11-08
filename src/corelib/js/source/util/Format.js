/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: EmailViewPanel.js 63 2008-07-30 13:49:24Z T. Suckow $
 * $Date: 2008-07-30 15:49:24 +0200 (Mi, 30 Jul 2008) $
 * $Revision: 63 $
 * $LastChangedDate: 2008-07-30 15:49:24 +0200 (Mi, 30 Jul 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/js/source/groupware/email/EmailViewPanel.js $
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