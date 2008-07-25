/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: LinkInterceptor.js 2 2008-06-21 10:38:49Z T. Suckow $
 * $Date: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $ 
 * $Revision: 2 $
 * $LastChangedDate: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/js/source/groupware/util/LinkInterceptor.js $ 
 */
 
Ext.namespace('de.intrabuild');

/**
 * @class de.intrabuild.Gettext
 * @singleton
 * 
 */
de.intrabuild.Gettext= function() {
	
	return {
		
		/**
		 * Returns the passed value. Used for identifying text ids for the server when
		 * parsing the scripts for translatable strings. 
		 * 
		 * @return {Mixed}
		 */
		gettext : function(value) 
		{
		    return value;
		},		
		
        /**
         * Returns either the first or the second parameter based on the cardinality
         * of the "n".
         * If n is equal to "1", the first parameter will be returned, otherwise
         * teh second. 
         * 
         * @return {Mixed}
         */
        ngettext : function(value1, value2, n) 
        {
            return (n == 1 ? value1 : value2);
        }		
	};
	
}();
