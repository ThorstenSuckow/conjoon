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

Ext.namespace('com.conjoon.groupware');

/**
 * @class com.conjoon.groupware.ReponseInspector
 *
 * A singleton for inpecting raw responses as returned by the server and taking
 * appropriate action.
 * Every cllback that is either defined for "success" or "failure", or any
 * "loadexception" should use the inspector as it defines some basic handling
 * of application specific responses, such as error messages.
 * Some errors returned by the applicaton (e.g. the server) are recoverable, such
 * as an authentication failure. In this case, the server returns the 401 http status
 * code to which the {@see com.conjoon.groupware.Reception} listens, and
 * automatically builds a login window. Since the 401 is treated as a response error,
 * the inspector's handleFailure should be called along with a callback for the
 * 'loginsuccessfull' event of the reception.
 * The signature of a json encoded error-response for an authentication failure
 * is as follows:
 * <lu>
 *  <li>success : false</li>
 *  <li>authorized: false</li>
 *  <li>error : [an erro object given a detailed description of the error that occured</li>
 *  </lu>
 * In the same way is the "locked" state of the application treated, i.e. if the
 * application's current state was locked until the user provides his authentication
 * credentials again. The session will be refreshed automatically but no other
 * requests are allowed except for this session-keep alive. Any other request will
 * also be responded by the server with a 401 status code and the following response
 * signature:
 * <lu>
 *  <li>success : false</li>
 *  <li>locked: true</li>
 *  <li>error: [an erro object given a detailed description of the error that occured</li>
 *  </lu>
 * When the raw responses are passed to the inspector, the appropriate action
 * will be taken.
 *
 * @singleton
 */
com.conjoon.groupware.ResponseInspector = function() {

    /**
     * Tries to json-decode the passed parameter and returns the result.
     * If the parameter is already of the type Object, no action will be taken
     * and the parameter will be returned unprocessed.
     * If the parameter is of type String and cannot be json-decoded, an error
     * will be thrown. An error will be thrown if the value is neither of type
     * String nor of type Object.
     *
     * @param {Object} value
     *
     * @return Object
     *
     * @see Ext.decode
     */
    var _tryDecode = function(value)
    {
        if ((typeof value).toLowerCase() === 'string') {
            var v = null;
            try {
                v = Ext.decode(value);
            } catch (e) {
                throw(
                    'com.conjoon.groupware.ResponseInspector._tryDecode: '
                    + 'Argument does not seem to be json-encoded.'
                );
            }
            return v;
        } else if ((typeof value).toLowerCase() === 'object') {
            return value;
        }

        throw(
            'com.conjoon.groupware.ResponseInspector._tryDecode: '
            + 'Argument is neither of type String nor Object.'
        );
    };

    return {

        /**
         * Failure type indicating response said "not authorized", since someone has
         * logged in with the same user credentials
         * @param {Number}
         */
        FAILURE_TOKEN : 8,

        /**
         * Failure type indicating response said "not authorized"
         * @param {Number}
         */
        FAILURE_AUTH : 4,

        /**
         * Failure type indicating response said "workbench locked"
         * @param {Number}
         */
        FAILURE_LOCK : 2,

        /**
         * Type indicating response stated no specific failure
         * @param {Number}
         */
        FAILURE_NONE : -1,

        /**
         * Returns true if the passed argument contains a property called
         * "authorized" which equals to false.
         * The passed parameter can be either of type String or Object. If the
         * type is String, the method will try to json-decode the string.
         * Throws an error if that fails.
         * This method must only be called when inspecting ajax-responses.
         * The method assumes that no authorization failure is present,
         * if any error occurres while trying to decode the response.
         *
         * @param {Object|String} response The response to inspect for an
         * authentication failure.
         *
         * @return {Boolean} true, if the decoded argument has a property
         * "authorized" which equals to false, otherwise true
         *
         * @see _tryDecode
         *
         * @throws Error if decoding the argument fails
         */
        isAuthenticationFailure : function(response)
        {
            if (response && response.responseText) {
                response = response.responseText;
            }

            try {
                var obj = _tryDecode(response);
            } catch (e) {
                return false;
            }

            if (obj.authorized === false) {
                return true;
            }

            return false;
        },

        /**
         * Returns true if the passed argument contains a property called
         * "locked" which equals to true.
         * The passed parameter can be either of type String or Object. If the
         * type is String, the method will try to json-decode the string.
         * Throws an error if that fails.
         * This method must only be called when inspecting ajax-responses.
         *
         * @param {Object|String} response The response to inspect, expecting
         * that it indicates the workbench lock-state.
         *
         * @return {Boolean} true, if the decoded argument has a property
         * "locked" which equals to true, otherwise false
         *
         * @see _tryDecode
         *
         * @throws Error if decoding the argument fails
         */
        isWorkbenchLocked : function(response)
        {
            var obj = _tryDecode(response);

            if (obj.locked === true) {
                return true;
            }

            return false;
        },

        /**
         * Inspects the properties of config and does either call success
         * or failure based on the inspected elements.
         *
         * @param {Object} config
         *  - result
         *  - remotingObject
         *  - success
         *  - failure
         *  - scope
         *
         */
        doCallbackForDirectApiResponse : function(config)
        {
            var result         = config.result,
               remotingObject = config.remotingObject,
               success        = config.success,
               failure        = config.failure,
               scope          = config.scope;

            if (!result) {
                failure.call(scope, remotingObject);
            } else {
                if (remotingObject.code === Ext.Direct.exceptions.PARSE) {
                    failure.call(scope, remotingObject);
                } else {
                    // syntax error or result available
                    success.call(scope, result);
                }
            }
        },

        /**
         * Returns the failure type associated with the response values, if any.
         *
         * @param {String|XmlHttpResponse} response The response to inspect for errors
         *
         * @return {Number}
         */
        getFailureType : function(response)
        {
            var resp = null;

            if (response) {
                if (response.responseText) {
                    resp = _tryDecode(response.responseText);
                } else {
                    resp = _tryDecode(response);
                }
            }

            // check whether the response was generated for an Ext.Direct
            // request
            if (resp.result) {
                resp = resp.result;
            }

            if (resp.tokenFailure === true) {
                return this.FAILURE_TOKEN;
            }

            if (resp.authorized === false) {
                return this.FAILURE_AUTH;
            }

            if (resp.locked === true) {
                return this.FAILURE_LOCK;
            }

            return this.FAILURE_NONE;
        },

        /**
         * Returns the json decoded response if there was a property called
         * "success" which was set to "true", otherwise null.
         *
         * @param {String|XmlHttpResponse|Object}
         *
         * @return {Object}
         *
         */
        isSuccess : function(response)
        {
            var resp = null;

            // take Ex.Direct respons into account
            if (response.result && response.result.success) {
                var stat = response.result.success;
                return stat === true
                       ? response.result
                       : stat === false
                         ? false
                         : null;
            }

            try {
                if (response.responseText) {
                    resp = _tryDecode(response.responseText);
                } else {
                    resp = _tryDecode(response);
                }

                if (resp.success === true) {
                    return resp;
                } else if (resp.success === false) {
                    return false;
                }
            } catch (e) {
                // ignore
            }

            return null;
        },

        /**
         * Returns a SystemMessage object passed on the response passed to this method.
         *
         * @return com.conjoon.SystemMessage
         */
        generateMessage : function(response, options)
        {
            response = response.xhr
                       ? response.xhr
                        : response.message
                        ? response.message
                         : response.result
                         ? response.result
                       : response;

            var resp = response;

            options = options || {};

            try {
                if (resp) {
                    if (resp.responseText) {
                        resp = _tryDecode(resp.responseText);
                    } else {
                        resp = _tryDecode(resp);
                    }
                }
            } catch (e) {
                // ignore, so we can show an unexpected error
            }

            var json = com.conjoon.util.Json;

            var error = null;

            var opt = {};

            if (!resp.error || (resp.error && !Ext.isObject(resp.error))) {
                error = json.forceErrorDecode(resp, options);

                opt = {
                    title : error.title,
                    text  : error.message,
                    type  : error.level
                };
            } else {
                error = resp.error;
                opt = {
                    title : Ext.util.Format.htmlEncode(error.title) || options.title,
                    text  : options.message
                            ? options.message + "<br />" + Ext.util.Format.htmlEncode(error.message)
                            : (error.message
                               ? Ext.util.Format.htmlEncode(error.message)
                                 .split("\n").join("<br />")
                               : '<b>' +
                                  com.conjoon.Gettext.gettext("An unexpected error occurred. The server returned the following response:") +
                                  '</b><br />-----<br />'+
                                   Ext.util.JSON.encode(error)
                            ),
                    type  : error.level
                };
            }

            return new com.conjoon.SystemMessage(opt);

        },

        /**
         * A method for taking automatic actions when a erroneous response from
         * the server is expected.
         * Shows a message dialog given more detailed information about the error.
         * If the response object did contain an error, it's error-details will
         * be shown. In any other case, an "unexpected error" dialog will be
         * rendered, showing the raw responseText-property of the response.
         * Depending on the configuration of the parameter "options", callbacks
         * may be attached to the {@see com.conjoon.groupware.Reception._loginWindow},
         * if the error is recoverable. A recoverable error is for example an
         * authentication failure, when the user has to re-authenticate at the
         * application.
         * Also, properties in the options-object, which could be properties of
         * the error-dialog will be given presedence to the properties that get
         * set over this method.
         * Valid proerties of the options-object are:
         * <ul>
         *  <li>onLogin : an observable-valid callback for the 'loginsuccessfull'
         *  event of the {@see com.conjoon.groupware.Reception}, containing
         *  the properties
         *   <ul>
         *    <li>fn : the function to call</li>
         *    <li>scope : the scope in which the function gets called</li>
         *   </ul>
         *  </li>
         *  <li>title : the title displayed in the error-dialogs titlebar</li>
         *  <li>show : true or false. True to automatically render the failure
         *  as an informal message on the screen</li>
         * </ul>
         *
         * @param {String|XmlHttpResponse} response The response to inspect for errors
         *
         * @return {Object} An object with detailed information on the error
         *                  that occured
         *
         * @see _tryDecode
         *
         * @throws Error if decoding the argument fails
         *
         * @see com.conjoon.util.Json.forceErrorDecode
         */
        handleFailure : function(response, options)
        {
            var systemMessage = this.generateMessage(response, options);

            options = options || {};

            // check if the response send an authentication failure
            if (options.onLogin && com.conjoon.groupware.ResponseInspector.isAuthenticationFailure(response)) {
                var ol = options.onLogin;
                com.conjoon.groupware.Reception.onLogin(
                    ol.fn, ol.scope
                );
            } else if (options.onGeneral) {
                var ol = options.onGeneral;
                ol.fn.call(ol.scope);
            }

            var ret = {
                title   : systemMessage.title || com.conjoon.Gettext.gettext("Error"),
                msg     : systemMessage.text
            };

            if (options.show === false) {
                return ret;
            }

            var msg  = Ext.MessageBox;

            com.conjoon.SystemMessageManager.show(Ext.apply({
                buttons : msg.OK,
                icon    : msg[systemMessage.type.toUpperCase()],
                cls     :'com-conjoon-msgbox-'+systemMessage.type,
                width   : 400
            }, ret));
        }

    };


}();