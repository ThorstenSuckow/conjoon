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

/**
 * Provides common overwrite functionality for Ext components to match
 * behavior as wished.
 */


// **********************
/**
 * Overrides ext-adapter behavior for allowing queuing of AJAX requests.
 *
 */
/*
 * Portions of this code are based on pieces of Yahoo User Interface Library
 * Copyright (c) 2007, Yahoo! Inc. All rights reserved.
 * YUI licensed under the BSD License:
 * http://developer.yahoo.net/yui/license.txt
 */
Ext.lib.Ajax = function() {

    var activeX = ['MSXML2.XMLHTTP.3.0',
                   'MSXML2.XMLHTTP',
                   'Microsoft.XMLHTTP'];

    /**
     * @type {Array} _queue A FIFO queue for processing pending requests
     */
    var _queue = [];

    /**
     * @type {Number} _activeRequests The number of requests currently
     * being processed.
     */
    var _activeRequests = 0;

    /**
     * @type {Number} _concurrentRequests The number of max. concurrent requests
     * requests allowed.
     */
    var _concurrentRequests = 2;

    switch (true) {
        case Ext.isIE8:
            _concurrentRequests = window.maxConnectionsPerServer;
        break;

        case Ext.isIE:
            _concurrentRequests = 2;
        break;

        case Ext.isSafari:
        case Ext.isChrome:
        case Ext.isGecko3:
            _concurrentRequests = 4;
        break;
    }


    // private
    function setHeader(o)
    {
        var conn = o.conn,
            prop;

        function setTheHeaders(conn, headers){
            for (prop in headers) {
                if (headers.hasOwnProperty(prop)) {
                    conn.setRequestHeader(prop, headers[prop]);
                }
            }
        }

        if (pub.defaultHeaders) {
            setTheHeaders(conn, pub.defaultHeaders);
        }

        if (pub.headers) {
            setTheHeaders(conn, pub.headers);
            pub.headers = null;
        }
    }

    // private
    function createExceptionObject(tId, callbackArg, isAbort, isTimeout)
    {
        return {
            tId : tId,
            status : isAbort ? -1 : 0,
            statusText : isAbort ? 'transaction aborted' : 'communication failure',
                isAbort: true,
                isTimeout: true,
            argument : callbackArg
        };
    }

    // private
    function initHeader(label, value)
    {
        (pub.headers = pub.headers || {})[label] = value;
    }

    // private
    function createResponseObject(o, callbackArg)
    {
        var headerObj = {},
            headerStr,
            conn = o.conn;

        try {
            headerStr = o.conn.getAllResponseHeaders();
            Ext.each(headerStr.split('\n'), function(v){
                var t = v.split(':');
                headerObj[t[0]] = t[1];
            });
        } catch(e) {}

        return {
            tId : o.tId,
            status : conn.status,
            statusText : conn.statusText,
            getResponseHeader : headerObj,
            getAllResponseHeaders : headerStr,
            responseText : conn.responseText,
            responseXML : conn.responseXML,
            argument : callbackArg
        };
    }

    // private
    function releaseObject(o)
    {
        o.conn = null;
        o      = null;

        _activeRequests--;
        _processQueue();
    }

    // private
    function handleTransactionResponse(o, callback, isAbort, isTimeout) {
        if (!callback) {
            releaseObject(o);
            return;
        }

        var httpStatus, responseObject;

        try {
            if (o.conn.status !== undefined && o.conn.status != 0) {
                httpStatus = o.conn.status;
            }
            else {
                httpStatus = 13030;
            }
        }
        catch(e) {
            httpStatus = 13030;
        }

        if ((httpStatus >= 200 && httpStatus < 300) || (Ext.isIE && httpStatus == 1223)) {
            responseObject = createResponseObject(o, callback.argument);
            if (callback.success) {
                if (!callback.scope) {
                    callback.success(responseObject);
                }
                else {
                    callback.success.apply(callback.scope, [responseObject]);
                }
            }
        }
        else {
            switch (httpStatus) {
                case 12002:
                case 12029:
                case 12030:
                case 12031:
                case 12152:
                case 13030:
                    responseObject = createExceptionObject(o.tId, callback.argument, (isAbort ? isAbort : false), isTimeout);
                    if (callback.failure) {
                        if (!callback.scope) {
                            callback.failure(responseObject);
                        }
                        else {
                            callback.failure.apply(callback.scope, [responseObject]);
                        }
                    }
                break;

                // handles 401
                case 401:
                    Ext.ux.util.MessageBus.publish('ext.lib.ajax.authorizationRequired', {
                        requestObject : o,
                        rawResponse   : o.conn
                    });

                default:
                    responseObject = createResponseObject(o, callback.argument);
                    if (callback.failure) {
                        if (!callback.scope) {
                            callback.failure(responseObject);
                        }
                        else {
                            callback.failure.apply(callback.scope, [responseObject]);
                        }
                    }
            }
        }

        releaseObject(o);
        responseObject = null;
    }

    // private
    function handleReadyState(o, callback){
    callback = callback || {};
        var conn = o.conn,
            tId = o.tId,
            poll = pub.poll,
    cbTimeout = callback.timeout || null;

        if (cbTimeout) {
            pub.timeout[tId] = setTimeout(function() {
                pub.abort(o, callback, true);
            }, cbTimeout);
        }

        poll[tId] = setInterval(
            function() {
                if (conn && conn.readyState == 4) {
                    clearInterval(poll[tId]);
                    poll[tId] = null;

                    if (cbTimeout) {
                        clearTimeout(pub.timeout[tId]);
                        pub.timeout[tId] = null;
                    }

                    handleTransactionResponse(o, callback);
                }
            },
            pub.pollInterval);
    }

    function asyncRequest(method, uri, callback, postData)
    {
        var o = getConnectionObject();

        if (!o) {
            return null;
        } else {
            _queue.push({
               o        : o,
               method   : method,
               uri      : uri,
               callback : callback,
               postData : postData
            });

            return _processQueue();
        }
    }

    function _processQueue()
    {
        var to = _queue[0];
        if (to && _activeRequests < _concurrentRequests) {
            to = _queue.shift();
            _activeRequests++;
            return _asyncRequest(to.method, to.uri, to.callback, to.postData);
        }
    }


    // private
    function _asyncRequest(method, uri, callback, postData) {
        var o = getConnectionObject() || null;

        if (o) {
            o.conn.open(method, uri, true);

            if (pub.useDefaultXhrHeader) {
                initHeader('X-Requested-With', pub.defaultXhrHeader);
            }

            if(postData && pub.useDefaultHeader && (!pub.headers || !pub.headers['Content-Type'])){
                initHeader('Content-Type', pub.defaultPostHeader);
            }

            if (pub.defaultHeaders || pub.headers) {
                setHeader(o);
            }

            handleReadyState(o, callback);
            o.conn.send(postData || null);
        }
        return o;
    }

    // private
    function getConnectionObject() {
        var o;

        try {
            if (o = createXhrObject(pub.transactionId)) {
                pub.transactionId++;
            }
        } catch(e) {
        } finally {
            return o;
        }
    }

    // private
    function createXhrObject(transactionId) {
        var http;

        try {
            http = new XMLHttpRequest();
        } catch(e) {
            for (var i = 0; i < activeX.length; ++i) {
                try {
                    http = new ActiveXObject(activeX[i]);
                    break;
                } catch(e) {}
            }
        } finally {
            return {conn : http, tId : transactionId};
        }
    }

    var pub = {
        request : function(method, uri, cb, data, options) {
            if(options){
                var me = this,
                    xmlData = options.xmlData,
                    jsonData = options.jsonData;

                Ext.applyIf(me, options);

                if(xmlData || jsonData){
                    initHeader('Content-Type', xmlData ? 'text/xml' : 'application/json');
                    data = xmlData || Ext.encode(jsonData);
                }
            }
            return asyncRequest(method || options.method || "POST", uri, cb, data);
        },

        serializeForm : function(form) {
            var fElements = form.elements || (document.forms[form] || Ext.getDom(form)).elements,
                hasSubmit = false,
                encoder = encodeURIComponent,
                element,
                options,
                name,
                val,
                data = '',
                type;

            Ext.each(fElements, function(element) {
                name = element.name;
                type = element.type;

                if (!element.disabled && name){
                    if(/select-(one|multiple)/i.test(type)){
                        Ext.each(element.options, function(opt) {
                            if (opt.selected) {
                                data += String.format("{0}={1}&",
                                                     encoder(name),
                                                      (opt.hasAttribute ? opt.hasAttribute('value') : opt.getAttribute('value') !== null) ? opt.value : opt.text);
                            }
                        });
                    } else if(!/file|undefined|reset|button/i.test(type)) {
                        if(!(/radio|checkbox/i.test(type) && !element.checked) && !(type == 'submit' && hasSubmit)){

                            data += encoder(name) + '=' + encoder(element.value) + '&';
                            hasSubmit = /submit/i.test(type);
                        }
                    }
                }
            });
            return data.substr(0, data.length - 1);
        },

        useDefaultHeader    : true,
        defaultPostHeader   : 'application/x-www-form-urlencoded; charset=UTF-8',
        useDefaultXhrHeader : true,
        defaultXhrHeader    : 'XMLHttpRequest',
        poll                : {},
        timeout             : {},
        pollInterval        : 50,
        transactionId       : 0,

        abort : function(o, callback, isTimeout)
        {
            var me = this, tId = o.tId, isAbort = false;

            if (me.isCallInProgress(o)) {
                o.conn.abort();
                clearInterval(me.poll[tId]);
                me.poll[tId] = null;
                if (isTimeout) {
                    me.timeout[tId] = null;
                }

                //aborted may be called with no callback-parameter, so no loadexception
                //or else would be generated in handleTransactionResponse.
                if (!callback) {
                    Ext.ux.util.MessageBus.publish('ext.lib.ajax.abort', {
                        requestObject : o
                    });
                }

                handleTransactionResponse(o, callback, (isAbort = true), isTimeout);
                return isAbort;
            } else {
                for (var i = 0, max_i = _queue.length; i < max_i; i++) {
                    if (_queue[i].o.tId == o.tId) {
                        _queue.splice(i, 1);
                        break;
                    }
                }
                return false;
            }
        },

        isCallInProgress : function(o) {
            // if there is a connection and readyState is not 0 or 4
            return o.conn && !{0:true,4:true}[o.conn.readyState];
        }
    };

    return pub

}();
// **********************

/**
 * Adds focus/blur events to Ext.Viewport which generally translate to
 * browser window focus/blur.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
Ext.Viewport.prototype.initComponent = Ext.Viewport.prototype.initComponent.createInterceptor(
    function() {
       this.addEvents(
            /**
             * @event blur
             * Fires when the viewport loses its focus, i.e. when the browser window/tab
             * loses its focus
             * @param {Ext.Viewport}
             * @param {HTMLElement} lastActiveElement
             */
            'blur',
            /**
             * @event focus
             * @event blur
             * Fires when the viewport gains focus, i.e. when the browser window/tab
             * gains focus
             * @param {Ext.Viewport}
             * @param {HTMLElement} lastActiveElement
             */
            'focus'
        );

        var focusEl    = window;
        var eventNames = ['focus', 'blur'];

        if (Ext.isIE) {
            focusEl    = document;
            eventNames = ['focusin', 'focusout'];
        }

        Ext.EventManager.on(focusEl, eventNames[0], function(e) {
            if (this._hasFocus) {
                return;
            }
            this._hasFocus = true;
            this.fireEvent('focus', this, this._activeElement);
        }, this, {stopPropagation : true});

        Ext.EventManager.on(focusEl, eventNames[1], function(e) {
            if (this._activeElement != document.activeElement) {
                this._activeElement = document.activeElement;
                // ie detects focus loss if current activeElement
                // equals to last active element
                if (Ext.isIE) {
                    return;
                }
            }
            this._hasFocus = false;
            this.fireEvent('blur', this, this._activeElement);
        }, this, {stopPropagation : true});

        this._activeElement = document.activeElement;
    }
);
