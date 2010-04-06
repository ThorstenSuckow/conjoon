/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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

Ext.namespace('com.conjoon.cudgets.data');

/**
 * This class represents a download. A download is a request for a file
 * that gets processed on the server. To enable server/client communication,
 * cookies will be used. Once initiated on the client, a cookie will be
 * created which has to be edited on the server. The cookie accepts 3
 * different states:
 *
 *  requested - download was initiated, request has been send to the server
 *      (set by the client)
 *  downloading - file was found on the server
 *      (set by the server)
 *  error_forbidden - the request has been processed by the server, but the
 *      download will not be initiated due to security reasons
 *      (set by the server)
 *  error_notFound - the request has been processed by the server, but the
 *      download will not be initiated since the requested file was not found
 *      (set by the server)
 *
 *
 * Inspired by Raj Bandi
 * http://www.rajbandi.net/blog/post/2009/11/13/Ajax-File-Download.aspx
 *
 * @class com.conjoon.cudgets.data.Download
 * @extends Ext.util.Observable
 */
com.conjoon.cudgets.data.Download = function(config) {

    var config = config || {};

    Ext.apply(this, config);

    this.addEvents(
        /**
         * Fired before the request for downloading is made. Return false
         * to cancel this event.
         * @event request
         * @param this
         */
        'beforerequest',
        /**
         * Fired when the request to the server for downloading from url has
         * been initiated.
         * @event request
         * @param this
         */
        'request',
        /**
         * Fired when the download has been cancelled.
         * @event request
         * @param this
         */
        'cancel',
        /**
         * Fired when the request to the server for downloading from "url" has
         * been initiated, the request has been processed and the server responded
         * with a cookie set to error_forbidden or error_notFound.
         * @event request
         * @param this
         * @param {String} type either error_forbidden or error_notFound
         */
        'failure',
        /**
         * Fired when the request to the server for downloading from "url" has
         * been initiated, but the request could not be processed, i.e. no
         * cookie has been set.
         * @event request
         * @param this
         * @param {String} responseString The response as returned by the
         * server, i.e. the content of the iframe's document
         */
        'error',
        /**
         * Fired when the request to the server for downloading from url has
         * been initiated, and the server responded with a cookie set to
         * "downloading".
         * @event download
         * @param this
         */
        'success'
    );

    if (!this.cookieName) {
        this.cookieName = "dloadCookie_"+Ext.id();
    }

    com.conjoon.cudgets.data.Download.superclass.constructor.call(this);
};


Ext.extend (com.conjoon.cudgets.data.Download, Ext.util.Observable, {

    /**
     * @cfg {String} cookieName The name for the download cookie.
     * this schould be a unique name, if not provided, the value will be
     * auto generated
     */
    cookieName : null,

    /**
     * @cfg {String} url The url to download the file from
     */
    url : null,

    /**
     * @type {Boolean} isDownloading Set to true once the download has started.
     * @protected
     */
    isDownloading : false,

    /**
     * @type {HtmlElement} iframe The iframe that was build for managing this
     * download.
     * @protected
     */
    iframe : null,

    /**
     * @type {Ext.util.TaskRunner} task The task which checks cookie and iframe
     * state in a frequent interval.
     * @protected
     */
    task : null,

    /**
     * @type {Object} taskConfig Configuration for the task.
     * @protected
     */
    taskConfig : null,

    /**
     * Returns the url for this download
     *
     * @return {String}
     */
    getUrl : function()
    {
        return this.url;
    },

    /**
     * Returns the cookie name for this download
     *
     * @return {String}
     */
    getCookieName : function()
    {
        return this.cookieName;
    },

    /**
     * Starts the download from url. Throws an error if url or cookieName
     * is not defined.
     * If the download was already initiated, an error will be thrown.
     *
     * @return {Boolean} true if download was started, otherwise false
     */
    start : function()
    {
        if (this.isDownloading) {
            throw("download for "+this.url+" already started");
        }

        if (!this.url) {
            throw("no url defined for download");
        }

        if (!this.cookieName) {
            throw("no cookieName defined for download");
        }

        if (this.fireEvent("beforerequest") === false) {
            return false;
        }


        if (this.buildDownloadAndInitiate()) {
            this.fireEvent("request", this);
            this.isDownloading = true;

            this.taskConfig = {
                run      : this.checkDownloadState,
                scope    : this,
                interval : 500
            };

            this.task = new Ext.util.TaskRunner();
            this.task.start(this.taskConfig);

            return true;
        }

        return false;
    },

    /**
     * Cancels the download.
     *
     */
    cancel : function(suspend)
    {
        if (this.isDownloading) {
            this.isDownloading = false;
            this.task.stop(this.taskConfig);
            this.task = null;
            document.body.removeChild(this.iframe);
            this.iframe = null;
            Ext.util.Cookies.clear(this.cookieName);
            if (suspend !== true) {
                this.fireEvent("cancel", this);
            }
        }
    },


// -------- helper

    /**
     * Checks the download state, i.e. cookies and iframe contents
     * This method is API only and will be called in the task created
     * for this instance.
     */
    checkDownloadState : function()
    {
        // give cookies presedence
        var cookieValue = Ext.util.Cookies.get(this.cookieName);

        switch (cookieValue) {

            case 'requested':
                // still waiting for the server to process

                // check iframes contents if an error occured
                if (!this.iframe) {
                    this.cancel(true);
                    this.fireEvent("error", this);
                    return;
                } else if (!this.iframe.contentWindow.document.forms[0]) {
                    var resp = this.iframe.contentWindow.document.innerHTML;
                    this.cancel(true);
                    this.fireEvent("error", this, resp);
                    return;
                }

                return;
            break;

            case 'downloading':
                // everything okay, cookie was set, we cannot
                // interfere from this point on
                this.fireEvent('success', this);
                this.cancel(true);
                return;
            break;

            case 'error_forbidden':
            case 'error_notFound':
                this.fireEvent('failure', this, cookieValue);
                this.cancel(true);
            break;
        }


    },

    /**
     * Initiates the download.
     *
     * @return {Boolean} returns true if client was prepared to send
     * the request, otherwise false.
     *
     * @protected
     */
    buildDownloadAndInitiate : function()
    {
        if (Ext.isGecko) {
            if (!com.conjoon.cudgets.data.Download._tmpFrame) {
                var iframe = document.createElement('iframe');
                iframe.style.cssText = 'width:1px;height:1px;display:none';
                com.conjoon.cudgets.data.Download._tmpFrame = iframe;
            }
        }

        var iframe = document.createElement('iframe');
        iframe.style.cssText = 'width:1px;height:1px;display:none';

        document.body.appendChild(iframe);

        var doc = iframe.contentWindow.document;
        doc.open();
        doc.clear();
        doc.writeln(
            '<html>'
             + '<body>'
             + '<form method="post" action="'+this.url+'">'
             + '<input type="hidden" name="downloadCookieName" value="'+this.cookieName+'" />'
             + '</form>'
             + '</body>'
             + '</html>'
        );
        doc.close();

        var form = doc.forms[0];
        Ext.util.Cookies.set(this.cookieName, 'requested');
        form.submit();

        if (Ext.isGecko) {
            document.body.appendChild(com.conjoon.cudgets.data.Download._tmpFrame);
            document.body.removeChild(com.conjoon.cudgets.data.Download._tmpFrame);
        }

        this.iframe = iframe;

        return true;
    }

});

// Mozilla hack, needed to prevent hourglass in tab
com.conjoon.cudgets.data.Download._tmpFrame = null;