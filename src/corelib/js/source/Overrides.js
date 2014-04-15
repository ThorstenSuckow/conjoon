/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
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

/*@REMOVE@*/
if (Ext.version != '3.4.0') {
    throw("Ext.version " + Ext.version + " detected, please check Overrides.js");
}
/*@REMOVE@*/


/**
 * Provides common overwrite functionality for Ext components to match
 * behavior as wished.
 */

/**
 * In preparation for ExtJS4 migration, these overrides mimic behavior of
 * Ext.create() and Ext.define()
 */
Ext.apply(Ext, {

    /**
     * Cache for
     */
    __myClassStack__ : {},

    /**
     * Helper function to create chained objects specified as string in classname
     *
     * @param className
     * @param all
     * @param strict
     * @return {Object}
     */
    conjoonCreateNs : function(className, all, strict) {

        var classNameParts = className.split('.'),
            stack = window,
            j = all === true ? 0 : 1;

        for (var i = 0, len = classNameParts.length - j; i < len; i++) {
            var part = classNameParts[i];

            if (typeof part != 'string') {
                stack = part;
            } else {
                if (!stack[part]) {
                    if (strict === true) {
                        throw("part not found in create for " + className);
                    } else {
                        stack[part] = {};
                    }

                }

                stack = stack[part];
            }
        }

        return stack
    },

    /**
     * Mimics ExtJS4 "Ext.Array.indexOf()"
     *
     * @param {Array}
     * @param mixed item
     *
     * @return {Number}
     */
    Array : {
        indexOf : function(arr, item) {
            // Ext 3
            return arr.indexOf(item);
        }
    },

    /**
     * Mimics ExtJS4 "Ext.create()"
     *
     * @param className
     * @param obj
     *
     * @return {Object}
     */
    createInstance : function(className, obj) {

        var cs = Ext.conjoonCreateNs(className, true, true);

        if (cs && !Ext.__myClassStack__[className]) {
            Ext.__myClassStack__[className] = cs;
        }

        if (!cs && !Ext.__myClassStack__[className]) {
            throw("className not defined: " + className);
        }

        return new Ext.__myClassStack__[className](obj);

    },

    /**
     * Mimics ExtJS4 "Ext.define()"
     *
     * @param className
     * @param obj
     *
     */
    defineClass : function(className, obj) {

        if (Ext.__myClassStack__[className]) {
            throw("class " + className + " already defined");
        }

        Ext.conjoonCreateNs(className);

        var cs,
            parts = className.split('.'),
            baseClass,
            baseCs;

        if (obj.extend) {

            baseClass = obj.extend;
            baseCs    = Ext.conjoonCreateNs(baseClass, true);

            delete obj.extend;

            obj.$className = className;

            cs = Ext.conjoonCreateNs(className);
            cs[parts[parts.length-1]] = Ext.extend(baseCs, obj);

        } else {

            cs = Ext.conjoonCreateNs(className);

            cs[parts[parts.length-1]] = function() {
                return cs[parts[parts.length-1]].prototype.constructor.apply(this, arguments);
            };

            obj.$className = className;

            cs[parts[parts.length-1]].prototype = obj;
        }

        Ext.__myClassStack__[className] = cs[parts[parts.length-1]];
    },

    /**
     * Mimics functionality of ExtJS4's Ext.getClassName().
     *
     * @param {Object} obj
     *
     * @return {String
     *
     * @throws {String} throws an error if the className for this obejct is
     * not available
     */
    getClassName : function(obj) {

        if (obj.$className !== undefined) {
            return obj.$className
        }

        throw("getClassName() for " + obj + " not successfull");

    }


});

// **********************
/**
 * Overrides ext-adapter behavior for allowing queuing of AJAX requests.
 *
 */

/**
 * The following override is valid for Ext 3.0.3 and Ext 3.1
 */
/*
 * Portions of this code are based on pieces of Yahoo User Interface Library
 * Copyright (c) 2007, Yahoo! Inc. All rights reserved.
 * YUI licensed under the BSD License:
 * http://developer.yahoo.net/yui/license.txt
 */
Ext.lib.Ajax = function() {

    /**
     * @type {Array} _queue A FIFO queue for processing pending requests
     */
    var _queue = [];

    /**
     * @type {Number} _activeRequests The number of requests currently being processed.
     */
    var _activeRequests = 0;

    /**
     * @type {Number} _concurrentRequests The number of max. concurrent requests requests allowed.
     */
    var _concurrentRequests = 6;

    switch (true) {
        case Ext.isIE:
            if (!window.maxConnectionsPerServer) {
                throw("\"window.maxConnectionsPerServer\" not found");
            }
            _concurrentRequests = window.maxConnectionsPerServer;
            break;
    }

    var activeX = [
        'Msxml2.XMLHTTP.6.0',
        'Msxml2.XMLHTTP.3.0',
        'Msxml2.XMLHTTP'
    ], CONTENTTYPE = 'Content-Type';

    // private
    function setHeader(o)
    {
        var conn = o.conn, prop;

        function setTheHeaders(conn, headers) {
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
            delete pub.headers;
        }
    }

    // private
    function createExceptionObject(tId, callbackArg, isAbort, isTimeout)
    {
        return {
            tId : tId,
            status : isAbort ? -1 : 0,
            statusText : isAbort ? 'transaction aborted' : 'communication failure',
            isAbort: isAbort,
            isTimeout: isTimeout,
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
            conn = o.conn,
            t,
            s,
            isBrokenStatus = conn.status == 1223;

        try {
            headerStr = o.conn.getAllResponseHeaders();
            Ext.each(headerStr.replace(/\r\n/g, '\n').split('\n'), function(v){
                t = v.indexOf(':');
                if(t >= 0){
                    s = v.substr(0, t).toLowerCase();
                    if(v.charAt(t + 1) == ' '){
                        ++t;
                    }
                    headerObj[s] = v.substr(t + 1);
                }
            });
        } catch(e) {}

        return {
            tId : o.tId,
            // Normalize the status and statusText when IE returns 1223, see the above link.
            status : isBrokenStatus ? 204 : conn.status,
            statusText : isBrokenStatus ? 'No Content' : conn.statusText,
            getResponseHeader : function(header){return headerObj[header.toLowerCase()];},
            getAllResponseHeaders : function(){return headerStr},
            responseText : conn.responseText,
            responseXML : conn.responseXML,
            argument : callbackArg
        };
    }

    // private
    function releaseObject(o)
    {
        //console.log(o.tId+" releasing");
        _activeRequests--;

        if (o.tId) {
            pub.conn[o.tId] = null;
        }

        o.conn = null;
        o = null;

        _processQueue();
    }

    // private
    function handleTransactionResponse(o, callback, isAbort, isTimeout)
    {
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

    function checkResponse(o, callback, conn, tId, poll, cbTimeout){
        if (conn && conn.readyState == 4) {
            clearInterval(poll[tId]);
            poll[tId] = null;

            if (cbTimeout) {
                clearTimeout(pub.timeout[tId]);
                pub.timeout[tId] = null;
            }
            handleTransactionResponse(o, callback);
        }
    }

    function checkTimeout(o, callback){
        pub.abort(o, callback, true);
    }


    function handleReadyState(o, callback){
        callback = callback || {};
        var conn = o.conn,
            tId = o.tId,
            poll = pub.poll,
            cbTimeout = callback.timeout || null;

        if (cbTimeout) {
            pub.conn[tId] = conn;
            pub.timeout[tId] = setTimeout(checkTimeout.createCallback(o, callback), cbTimeout);
        }
        poll[tId] = setInterval(checkResponse.createCallback(o, callback, conn, tId, poll, cbTimeout), pub.pollInterval);
    }

    /**
     * Pushes the request into the queue if a connection object can be created
     * na dimmediately processes the queue.
     *
     */
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
            //console.log(o.tId+" was put into the queue");
            var head = _processQueue();

            if (head) {
                //console.log(o.tId+" is being processed a the  head of queue");
                return head;
            } else {
                //console.log(o.tId+" was put into the queue and will be processed later on");
                return o;
            }
        }
    }

    /**
     * Initiates the async request and returns the request that was created,
     * if, and only if the number of currently active requests is less than the number of
     * concurrent requests.
     */
    function _processQueue()
    {
        var to = _queue[0];
        if (to && _activeRequests < _concurrentRequests) {
            to = _queue.shift();
            _activeRequests++;
            return _asyncRequest(to.method, to.uri, to.callback, to.postData, to.o);
        }
    }


    // private
    function _asyncRequest(method, uri, callback, postData, o)
    {
        if (o) {
            o.conn.open(method, uri, true);

            if (pub.useDefaultXhrHeader) {
                initHeader('X-Requested-With', pub.defaultXhrHeader);
            }

            if(postData && pub.useDefaultHeader && (!pub.headers || !pub.headers[CONTENTTYPE])){
                initHeader(CONTENTTYPE, pub.defaultPostHeader);
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
    function getConnectionObject()
    {
        var o;

        try {
            //console.log(pub.transactionId+" is the current transaction id");
            if (o = createXhrObject(pub.transactionId)) {
                pub.transactionId++;
            }
        } catch(e) {
        } finally {
            return o;
        }
    }

    // private
    function createXhrObject(transactionId)
    {
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
                    jsonData = options.jsonData,
                    hs;

                Ext.applyIf(me, options);

                if(xmlData || jsonData){
                    hs = me.headers;
                    if(!hs || !hs[CONTENTTYPE]){
                        initHeader(CONTENTTYPE, xmlData ? 'text/xml' : 'application/json');
                    }
                    data = xmlData || (Ext.isObject(jsonData) ? Ext.encode(jsonData) : jsonData);
                }
            }
            return asyncRequest(method || options.method || "POST", uri, cb, data);
        },

        serializeForm : function(form) {
            var fElements = form.elements || (document.forms[form] || Ext.getDom(form)).elements,
                hasSubmit = false,
                encoder = encodeURIComponent,
                name,
                data = '',
                type,
                hasValue;

            Ext.each(fElements, function(element){
                name = element.name;
                type = element.type;

                if (!element.disabled && name) {
                    if (/select-(one|multiple)/i.test(type)) {
                        Ext.each(element.options, function(opt){
                            if (opt.selected) {
                                hasValue = opt.hasAttribute ? opt.hasAttribute('value') : opt.getAttributeNode('value').specified;
                                data += String.format("{0}={1}&", encoder(name), encoder(hasValue ? opt.value : opt.text));
                            }
                        });
                    } else if (!(/file|undefined|reset|button/i.test(type))) {
                        if (!(/radio|checkbox/i.test(type) && !element.checked) && !(type == 'submit' && hasSubmit)) {
                            data += encoder(name) + '=' + encoder(element.value) + '&';
                            hasSubmit = /submit/i.test(type);
                        }
                    }
                }
            });
            return data.substr(0, data.length - 1);
        },

        useDefaultHeader : true,
        defaultPostHeader : 'application/x-www-form-urlencoded; charset=UTF-8',
        useDefaultXhrHeader : true,
        defaultXhrHeader : 'XMLHttpRequest',
        poll : {},
        timeout : {},
        conn: {},
        pollInterval : 50,
        transactionId : 0,

        abort : function(o, callback, isTimeout)
        {
            var me = this,
                tId = o.tId,
                isAbort = false;

            //console.log(o.tId+" is aborting - was "+o.tId+" in progress?: "+me.isCallInProgress(o));

            if (me.isCallInProgress(o)) {
                o.conn.abort();
                clearInterval(me.poll[tId]);
                me.poll[tId] = null;

                clearTimeout(pub.timeout[tId]);
                me.timeout[tId] = null;

                // @ext-bug 3.0.2 why was this commented out? if the request is aborted
                // programmatically, the timeout for the "timeout"-handler is never destroyed,
                // thus this method would at least be called once, if the initial reason is
                // that no timeout occured.
                //if (isTimeout) {
                //    me.timeout[tId] = null;
                //}

                //aborted may be called with no callback-parameter, so no loadexception
                //or else would be generated in handleTransactionResponse.
                if (!callback) {
                    Ext.ux.util.MessageBus.publish('ext.lib.ajax.abort', {
                        requestObject : o
                    });
                }

                handleTransactionResponse(o, callback, (isAbort = true), isTimeout);
            } else {
                // check here if the current call was in progress. This might not be the case
                // if the connection was put into the queue, waiting to get triggered
                for (var i = 0, max_i = _queue.length; i < max_i; i++) {
                    if (_queue[i].o.tId == o.tId) {
                        _queue.splice(i, 1);
                        //console.log(o.tId+" was not a call in progress, thus removed from the queue at "+i);
                        break;
                    }
                }

            }

            return isAbort;
        },

        isCallInProgress : function(o) {
            // if there is a connection and readyState is not 0 or 4
            return o.conn && !{0:true,4:true}[o.conn.readyState];
        }
    };
    return pub;
}();
// **********************

/**
 * Adds focus/blur events to Ext.Viewport which generally translate to
 * browser window focus/blur.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
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

/*@REMOVE@*/
if (Ext.version != '3.4.0') {
    throw(
        "Check override for Ext.ProgressBar in Overrides.js"
    );
}
/*@REMOVE@*/
/**
 * This override removes the back-text of the progress bar to overcome
 * rendering issues win Google Chrome when bot text elements have the
 * color black and are on top of each other. So this override is mainly
 * to show the progress bar text all the time in the color black (doesn't
 * adjust it's size based on the progress, it's always visible if text
 * is provided)
 */

var _overrideOnRender       = Ext.ProgressBar.prototype.onRender;
var _overrideUpdateProgress = Ext.ProgressBar.prototype.updateProgress;
Ext.override(Ext.ProgressBar, {

    // private
    onRender : function(ct, position){

        _overrideOnRender.call(this, ct, position);

        this.textEl = Ext.get(this.progressBar.dom.firstChild);
        this.textEl.addClass('x-hidden');
        delete this.textTopEl;
    },

    updateProgress : function(value, text, animate){

        var ret = _overrideUpdateProgress.call(this, value, text, animate);

        if (Ext.isChrome) {
            (function() {
                this.textEl.setWidth(this.el.dom.firstChild.offsetWidth);
                this.textEl.removeClass('x-hidden');
            }).defer(50, this);
        } else {
            this.textEl.setWidth(this.el.dom.firstChild.offsetWidth);
            this.textEl.removeClass('x-hidden');
        }

        return ret;
    }
});
delete _overrideOnRender;
delete _overrideUpdateProgress;
