/*
 * Ext JS Library 3.0 Pre-alpha
 * Copyright(c) 2006-2008, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */

/**
 * @class Ext.data.HttpProxy
 * @extends Ext.data.DataProxy
 * An implementation of {@link Ext.data.DataProxy} that reads a data object from a {@link Ext.data.Connection Connection} object
 * configured to reference a certain URL.<br>
 * <p>
 * <b>Note that this class cannot be used to retrieve data from a domain other than the domain
 * from which the running page was served.<br>
 * <p>
 * For cross-domain access to remote data, use a {@link Ext.data.ScriptTagProxy ScriptTagProxy}.</b><br>
 * <p>
 * Be aware that to enable the browser to parse an XML document, the server must set
 * the Content-Type header in the HTTP response to "text/xml".
 * @constructor
 * @param {Object} conn an {@link Ext.data.Connection} object, or options parameter to {@link Ext.Ajax#request}.
 * <p>Note that if this HttpProxy is being used by a (@link Ext.data.Store Store}, then the Store's call to
 * {@link #load} will override any specified <tt>callback</tt> and <tt>params</tt> options. In this
 * case, use the Store's {@link Ext.data.Store#events events} to modify parameters, or react to loading events.
 * The Store's {@link Ext.data.Store#baseParams baseParams} may also be used to pass parameters known at
 * instantiation time.</p>
 * <p>If an options parameter is passed, the singleton {@link Ext.Ajax} object will be used to make the request.</p>
 */
Ext.data.HttpProxy = function(conn){
    Ext.data.HttpProxy.superclass.constructor.call(this, conn);

    /**
     * The Connection object (Or options parameter to {@link Ext.Ajax#request}) which this HttpProxy uses to make requests to the server.
     * Properties of this object may be changed dynamically to change the way data is requested.
     * @property
     */
    this.conn = conn;

    // nullify the connection url.  The url param has been copied to "this" above.  The connection
    // url will be set during each execution of doRequest when buildUrl is called.  This makes it easier for users to override the
    // connection url during beforeaction events (ie: beforeload, beforesave, etc).  The connection url will be nullified
    // after each request as well.  Url is always re-defined during doRequest.
    this.conn.url = null;

    this.useAjax = !conn || !conn.events;

    //private.  A hash containing active requests, keyed on action [Ext.data.Api.CREATE|READ|UPDATE|DESTROY]
    this.activeRequest = {};
    var verbs = Ext.data.Api.getVerbs();
    for (var n=0,len=verbs.length;n<len;n++) {
        this.activeRequest[verbs[n]] = undefined; // <-- initialize availale activeRequest verbs.
    }

    /**
     * @event loadexception
     * Fires if an exception occurs in the Proxy during data loading.  This event can be fired for one of two reasons:
     * <ul><li><b>The load call returned success: false.</b>  This means the server logic returned a failure
     * status and there is no data to read.  In this case, this event will be raised and the
     * fourth parameter (read error) will be null.</li>
     * <li><b>The load succeeded but the reader could not read the response.</b>  This means the server returned
     * data, but the configured Reader threw an error while reading the data.  In this case, this event will be
     * raised and the caught error will be passed along as the fourth parameter of this event.</li></ul>
     * Note that this event is also relayed through {@link Ext.data.Store}, so you can listen for it directly
     * on any Store instance.
     * @param {Object} this
     * @param {Object} options The loading options that were specified (see {@link #load} for details)
     * @param {Object} response The XMLHttpRequest object containing the response data
     * @param {Error} e The JavaScript Error object caught if the configured Reader could not read the data.
     * If the load call returned success: false, this parameter will be null.
     */
};

Ext.extend(Ext.data.HttpProxy, Ext.data.DataProxy, {
    /**
     * @cfg {Boolean} prettyUrls
     * <p>If set to <tt>true</tt>, a {@link Ext.data.Record#phantom non-phantom} record's
     * {@link Ext.data.Record#id id} will be appended to the url (defaults to <tt>false</tt>).</p><br>
     * <p>The url is built based upon the action being executed <tt>[load|create|save|destroy]</tt>
     * using the commensurate <tt>{@link #api}</tt> property, or if undefined default to the
     * configured {@link Ext.data.Store}.{@link Ext.data.Store#url url}.</p><br>
     * <p>Some MVC (eg, Ruby on Rails, Merb and Django) support this style of segment based urls
     * where the segments in the URL follow the Model-View-Controller approach.</p><pre><code>
     * someSite.com/controller/action/id
     * </code></pre>
     * Where the segments in the url are typically:<div class="mdetail-params"><ul>
     * <li>The first segment : represents the controller class that should be invoked.</li>
     * <li>The second segment : represents the class function, or method, that should be called.</li>
     * <li>The third segment : represents the ID (a variable typically passed to the method).</li>
     * </ul></div></p>
     * <p>For example:</p>
     * <pre><code>
api: {
    load :    '/controller/load',
    create :  '/controller/new',  // Server MUST return idProperty of new record
    save :    '/controller/update',
    destroy : '/controller/destroy_action'
}

// destroying a record having id: 13, the url would look like
"/controller/destroy_action/13"
// updating a single record having id: 13, the url would look like
"/controller/update/13"
     * </code></pre>
     */
    prettyUrls : false,

    /**
     * Return the {@link Ext.data.Connection} object being used by this Proxy.
     * @return {Connection} The Connection object. This object may be used to subscribe to events on
     * a finer-grained basis than the DataProxy events.
     */
    getConnection : function(){
        return this.useAjax ? Ext.Ajax : this.conn;
    },

    /**
     * Used for overriding the url used for a single request.  Designed to be called during a beforeaction event.  Calling setUrl
     * will override any urls set via the api configuration parameter.  Set the optional parameter makePermanent to set the url for
     * all subsequent requests.  If not set to makePermanent, the next request will use the same url or api configuration defined
     * in the initial proxy configuration.
     * @param {String} url
     * @param {Boolean} makePermanent (Optional) [false]
     *
     * (eg: beforeload, beforesave, etc).
     */
    setUrl : function(url, makePermanent) {
        this.conn.url = url;
        if (makePermanent === true) {
            this.url = url;
        }
    },

    /**
     * buildUrl
     * Sets the appropriate url based upon the action being executed.  If prettyUrls is true, and only a single record is being acted upon,
     * url will be built Rails-style, as in "/controller/action/32".
     * @param {String} action The api action being executed [load|create|update|destroy]
     * @param {Ext.data.Record/Ext.data.Record[]} The record or Array of Records being acted upon.
     * @return {String} url
     * @private
     */
    buildUrl : function(action, record) {
        record = record || null;
        var url = (this.api[action]) ? this.api[action] : this.url;

        // if we have no url here, throw an exception.
        if (typeof(url) == 'undefined') {
            throw new Error('HttpProxy tried to build an url for the action "' + action + '" but could not find an api definition for this action or an url to fall-back to.  Please review your proxy configuration.');
        }

        if (this.prettyUrls === true && record instanceof Ext.data.Record && !record.phantom) {
            url += '/' + record.id;
        }
        return url;
    },

    /**
     * HttpProxy implementation of DataProxy#doRequest
     * @param {String} action
     * @param {Ext.data.Record/Ext.data.Record[]} rs If action is load, rs will be null
     * @param {Object} params An object containing properties which are to be used as HTTP parameters
     * for the request to the remote server.
     * @param {Ext.data.DataReader} reader The Reader object which converts the data
     * object into a block of Ext.data.Records.
     * @param {Function} callback The function into which to pass the block of Ext.data.Records.
     * The function must be passed <ul>
     * <li>The Record block object</li>
     * <li>The "arg" argument from the load function</li>
     * <li>A boolean success indicator</li>
     * </ul>
     * @param {Object} scope The scope in which to call the callback
     * @param {Object} arg An optional argument which is passed to the callback as its second parameter.
     */
    doRequest : function(action, rs, params, reader, cb, scope, arg) {
        var  o = {
            params : params || {},
            request: {
                callback : cb,
                scope : scope,
                arg : arg
            },
            reader: reader,
            callback : this.createCallback(action),
            scope: this
        };
        if(this.useAjax){
            // Set the connection url.  If this.conn.url is not null here,
            // the user may have overridden the url during a beforeaction event-handler.
            // this.conn.url is nullified after each request.
            if (this.conn.url === null) {
                this.conn.url = this.buildUrl(action, rs);
            }
            else if (this.prettyUrls === true && rs instanceof Ext.data.Record && !rs.phantom) {
                this.conn.url += '/' + rs.id;
            }

            Ext.applyIf(o, this.conn);

            // If a currently running request is found for this action, abort it.
            if (this.activeRequest[action]) {
                Ext.Ajax.abort(this.activeRequest[action]);
            }
            this.activeRequest[action] = Ext.Ajax.request(o);

            // request is sent, nullify the connection url in preparation for the next request
            this.conn.url = null;

        }else{
            this.conn.request(o);
        }
    },

    /**
     * createCallback
     * returns a request-callback function.  Note a special case is made for the Ext.data.Api.READ action vs all the others.
     * @param {String} action [create|update|delete|load]
     * @param {Record[]/DataReader} A list of records beinged acted upon or a DataReader for the "load" request
     * @param {Function} cb callback function
     * @param {Object} arg
     * @return {Function}
     * @private
     */
    createCallback : function(action) {
        return (action == Ext.data.Api.READ)
            // special case for load callback
            ? function(o, success, response){
                this.activeRequest[action] = undefined;
                if(!success){
                    this.fireEvent(action+"exception", this, o, response);
                    o.request.callback.call(o.request.scope, null, o.request.arg, false);
                    return;
                }
                var result;
                try {
                    result = o.reader.read(response);
                }catch(e){
                    this.fireEvent(action+"exception", this, o, response, e);
                    o.request.callback.call(o.request.scope, null, o.request.arg, false);
                    return;
                }
                this.fireEvent(action, this, o, o.request.arg);
                o.request.callback.call(o.request.scope, result, o.request.arg, true);
            }
            // callbacks for all others:  create, save, destroy
            : function(o, success, response) {
                this.activeRequest[action] = undefined;
                var reader = o.reader;
                var res = reader.readResponse(response);
                if(!res[reader.meta.successProperty] === true){
                    this.fireEvent("writeexception", this, action, o, res);
                    o.request.callback.call(o.request.scope, null, res, false);
                    return;
                }
                this.fireEvent("write", this, action, res[reader.meta.root], res, o.request.arg );
                o.request.callback.call(o.request.scope, res[reader.meta.root], res, true);
            }
    }
});