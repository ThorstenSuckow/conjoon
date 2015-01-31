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

/**
 * @const {String} TYPE_PROGRESS
 */
com.conjoon.SystemMessage.Type_PROGRESS = 'progress';