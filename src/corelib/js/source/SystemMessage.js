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
 * @const {String} TYPE_WARNING
 */
com.conjoon.SystemMessage.TYPE_WARNING = 'warning';

/**
 * @const {String} TYPE_ERROR
 */
com.conjoon.SystemMessage.TYPE_ERROR = 'error';

/**
 * @const {String} TYPE_INFO
 */
com.conjoon.SystemMessage.TYPE_INFO =  'info';

/**
 * @const {String} TYPE_NOTICE
 */
com.conjoon.SystemMessage.TYPE_NOTICE = 'notice';

/**
 * @const {String} TYPE_CRITICAL
 */
com.conjoon.SystemMessage.TYPE_CRITICAL = 'critical';

/**
 * @const {String} TYPE_CONFIRM
 */
com.conjoon.SystemMessage.TYPE_CONFIRM = 'confirm';

/**
 * @const {String} TYPE_PROMPT
 */
com.conjoon.SystemMessage.TYPE_PROMPT = 'prompt';

/**
 * @const {String} TYPE_WAIT
 */
com.conjoon.SystemMessage.TYPE_WAIT = 'wait';