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

Ext.namespace('com.conjoon');

/**
 * This class represents a system message in the conjoon project.
 *
 * @class com.conjoon.SystemMessage
 * @singleton
 */
com.conjoon.SystemMessage = function(config){

    /**
     * @cfg {String} title
     */

    /**
     * @cfg {String} text
     */

    /**
     * @cfg {String} type
     */

    Ext.apply(this, config);
};

com.conjoon.SystemMessage.prototype = {

};

/**
 * @const {Number} TYPE_WARNING
 */
com.conjoon.SystemMessage.TYPE_WARNING = 0;

/**
 * @const {Number} TYPE_ERROR
 */
com.conjoon.SystemMessage.TYPE_ERROR = 2;

/**
 * @const {Number} TYPE_INFO
 */
com.conjoon.SystemMessage.TYPE_INFO =  4;

/**
 * @const {Number} TYPE_NOTICE
 */
com.conjoon.SystemMessage.TYPE_NOTICE = 8;

/**
 * @const {Number} TYPE_CRITICAL
 */
com.conjoon.SystemMessage.TYPE_CRITICAL = 16;