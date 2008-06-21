Ext.namespace('de.intrabuild.util');

/**
 * Convionient utility methods for working with AJAX-responses in the intrabuild
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

de.intrabuild.util.Json = function() {


    return {
        
        /**
         * Checks wether the passed argument is a Json encoded error. If the check
         * succeds, the decoded object will be returned. If the check fails, an
         * object will be returned with 'description' set to the content of the value.
         *
         * @param {XmlHttpRequest} The response of the request to decode
         */
        forceErrorDecode : function(response)
        {
            var error = this.isError(response.responseText);
            
            if (error === null) {
                return { 
                    message   : '<b>An unexpected error occured. The server returned the '+
                                  'following response:</b><br />-----<br />'+
                                  '<b>Response Status:</b> '+response.status+'<br />'+
                                  '<b>Response Text:</b><br />'+
                                  Ext.util.Format.stripTags(response.responseText),
                    code      :  -1,
                    level     : 'critical',
                    title     : 'Unexpected Error'
                };
            } else {
                if (error.level == 'critical') {
                    error.title = error.title || 'Unexpected Error';
                    error.message = '<b>An unexpected error occured. The server returned the '+
                                    'following response:</b><br />-----<br />'+
                                    '<b>Response Status:</b> '+response.status+'<br />'+
                                    '<b>Response Text:</b><br />'+
                                    error.message || Ext.util.Format.stripTags(response.responseText);
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
                return null;
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
