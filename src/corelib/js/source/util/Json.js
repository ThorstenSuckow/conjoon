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
 * Convionient utility methods for working with AJAX-responses in the conjoon
 * namespace.
 * Every called Action to the Zend Framework returns a Json encoded string, which
 * root is always 'response', followed by the fields 'type', which represents the
 * datatype of the JSON response, and 'value', which actually holds the response's
 * value, determined through the datatype 'type'.
 * As a special exception, errors do not have a field called 'value', but instead
 * fields called 'message', 'description' and 'code', which should be self explaining.
 *
 * Examples:
   <pre><code>

   // a return type of boolean with a value that defaults to true:
   {'response' : {'type':'boolean', 'value' : true}}

   // a return type of integer with a value that defaults to 2000:
   {'response' : {'type':'integer', 'value' : 2000}}

   // an error that occured:
   {'response' : {'type':'error', 'message' : 'DB Failure', 'code' : '0x003',
                 'description' : 'While trying to connect to the db on localhost
                                  the following exception was thrown'}}

   </code></pre>
 *
 */

com.conjoon.util.Json = function() {


    return {

        /**
         * Checks wether the passed argument is a Json encoded error. If the check
         * succeds, the decoded object will be returned. If the check fails, an
         * object will be returned with 'description' set to the content of the value.
         *
         * @param {Object|XmlHttpRequest} The response of the request to decode
         */
        forceErrorDecode : function(response, options)
        {
            var error = response.error || this.isError(response.responseText);

            options = options || {};

            if (error === null) {

                if (response && !response.responseText) {

                    if ((response.status === 0 && response.statusText === 'communication failure') ||
                        (response.status === -1 && response.statusText === 'transaction aborted')) {

                        var isAbort       = response.isAbort && !response.isTimeout,
                            isCommFailure = !response.isAbort &&
                                            response.statusText === 'communication failure',
                            errorMsg = isAbort
                                  ? com.conjoon.Gettext.gettext("A communication failure occurred, due to an aborted HTTP request.")
                                  : (isCommFailure
                                     ? com.conjoon.Gettext.gettext("A communication failure occurred. Is the network and the server available?")
                                     : com.conjoon.Gettext.gettext("A communication failure occurred, most likely due to a response timeout.")),
                            title = isAbort || isCommFailure
                                    ? com.conjoon.Gettext.gettext("Communication failure")
                                    : com.conjoon.Gettext.gettext("Response Timeout");

                        return {
                            message   : '<b>' + errorMsg +
                                '</b><br />-----<br />'+
                                '<b>'+com.conjoon.Gettext.gettext("Additional information:")+'</b><br />'+
                                (Ext.isObject(response)
                                    ? Ext.util.JSON.encode(response)
                                    : response),
                            code      :  -1,
                            level     : 'critical',
                            title     : options.title || title
                        };
                    }

                    return {
                        message   : '<b>'
                            +com.conjoon.Gettext.gettext("An unexpected error occurred. The server returned an error that could not be parsed as an error object:")
                            +'</b><br />-----<br />'+
                            '<b>'+com.conjoon.Gettext.gettext("Additional information:")+'</b><br />'+
                            (Ext.isObject(response)
                            ? Ext.util.JSON.encode(response)
                            : response),
                        code      :  -1,
                        level     : 'critical',
                        title     : options.title || com.conjoon.Gettext.gettext("Unexpected Error")
                    };
                } else {
                    return {
                        message   : '<b>'
                            +com.conjoon.Gettext.gettext("An unexpected error occured. The server returned an empty response, most likely due to a response timeout:")
                            +'</b><br />-----<br />'+
                            '<b>'+com.conjoon.Gettext.gettext("Additional information:")+'</b><br />'+
                            com.conjoon.Gettext.gettext("No data returned by the server. Response was empty."),
                        code      :  -1,
                        level     : 'critical',
                        title     : options.title || com.conjoon.Gettext.gettext("Unexpected Error")
                    };
                }
            } else {

                if (!Ext.isObject(error)) {
                    return {
                        message   : error,
                        code      :  -1,
                        level     : 'critical',
                        title     : com.conjoon.Gettext.gettext("Unknown Error")
                    };
                } else if (error.level == 'critical') {
                    error.title = error.title || com.conjoon.Gettext.gettext("Unexpected Error");
                    error.message = '<b>'
                                    +com.conjoon.Gettext.gettext("An unexpected error occured. The server returned the following response:")
                                    +'</b><br />-----<br />'+
                                    '<b>'+com.conjoon.Gettext.gettext("Response Status:")+'</b> '+response.status+'<br />'+
                                    '<b>'+com.conjoon.Gettext.gettext("Response Text:")+'</b><br />'+
                                    (error.message ? error.message : Ext.util.Format.stripTags(response.responseText));
                } else if (error.fields) {
                    var str = [];
                    for (var i in error.fields) {
                        str.push('<b>'+i+'</b>:<br /> '+error.fields[i].join('<br />'));
                    }
                    error.message += '<br />'+str.join('<br />');
                }

            }

            return error;
        },

        /**
         * Checks if the passed arguments is a JSON encoded error object.
         * Returns <tt>null</tt> if there is no error, otherwise
         * the decoded JSON string. If the string is no JSON string,
         * the method will also return <tt>null</tt>.
         *
         */
        isError : function(source)
        {
            var obj = null;

            try {
                obj = Ext.decode(source);
            } catch (e) {
                if (source != "") {
                    // no json returned, most likely plain error message
                    // generated by the server
                    return {
                        level : 'critical'
                    };
                } else {
                    return null;
                }
            }

            if (obj && obj.response && obj.response.type === 'error') {
                var sh = obj.response.value;
                return {
                    message     : sh.message,
                    description : sh.description,
                    code        : sh.code,
                    level       : sh.level
                };
            } else if (obj && obj.error !== null) {
                var sh = obj.error;
                return {
                    message     : sh.message,
                    title       : sh.title,
                    code        : sh.code,
                    type        : sh.type,
                    file        : sh.file,
                    line        : sh.line,
                    level       : sh.level,
                    fields      : sh.fields || null
                };
            }

            return null;
        },

        /**
         * Returns <tt>true</tt> if the json response equals to
         *
         * {'response':{'type':type}}
         *
         */
        isResponseType : function(type, source)
        {
            var obj;

            var myType = (typeof source);

            if (myType == "string") {
                try {
                    obj = Ext.decode(source);
                    return obj.response.type === type;
                } catch (e) {
                    return false;
                }
            }

            if (myType == "object" && source.response.type === type) {
                return true;
            }

            return false;
        },

        /**
         * Returns the response value from a JSON encoded string.
         * The function does not take care of checking wether the passed
         * argument is in valid JSON format, so exceptions may be thrown.
         * You should make sure to check for a valid type by making a call
         * to <tt>isResponseType</tt> first off.
         *
         */
        getResponseValue : function(source)
        {
            var json = Ext.decode(source);

            return json.response.value;
        },

        /**
         * Returns the response value from a JSON encoded string.
         * The function does not take care of checking wether the passed
         * argument is in valid JSON format, so exceptions may be thrown.
         * You should make sure to check for a valid type by making a call
         * to <tt>isResponseType</tt> first off.
         *
         */
        getResponseValues : function(responseText)
        {
            var json = Ext.decode(responseText);

            return json;
        }

    }


}();
