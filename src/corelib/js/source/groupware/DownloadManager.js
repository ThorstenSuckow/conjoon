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

Ext.namespace('com.conjoon.groupware');

/**
 * The DownloadManager allows for subscribing to the following download
 * related events:
 * success, error, failure, request, cancel
 *
 *
 * @class com.conjoon.groupware.DownloadManager
 * @singleton
 */
com.conjoon.groupware.DownloadManager = function() {

    var _kernel = function(){
        this.addEvents(
            /**
             * @event success
             * Fired when a download was processed on the server side
             * successfully
             * @param {com.conjoon.cudgets.data.Download} download
             * @param {String} type
             * @param {Object} options
             */
            'success',
            /**
             * @event error
             * Fired when a download failed
             * @param {com.conjoon.cudgets.data.Download} download
             * @param {String} type
             * @param {Object} options
             */
            'error',
            /**
             * @event failure
             * Fired when a download failed
             * @param {com.conjoon.cudgets.data.Download} download
             * @param {String} type
             * @param {Object} options
             */
            'failure',
            /**
             * @event request
             * Fired when a download is requested
             * @param {com.conjoon.cudgets.data.Download} download
             * @param {String} type
             * @param {Object} options
             */
            'request',
            /**
             * @event cancel
             * Fired when a download was canceled.
             * @param {com.conjoon.cudgets.data.Download} download
             * @param {String} type
             * @param {Object} options
             */
            'cancel'
        );

        _kernel.superclass.constructor.call(this);
    };

    Ext.extend(_kernel, Ext.util.Observable, {});

    var kernel = new _kernel();

    var downloads = [];

    var events = ['cancel', 'failure', 'error', 'request', 'success'];


    var removeDownload = function(url) {
        delete downloads[url];
    };

    var DownloadManager = com.conjoon.groupware.DownloadManager;

    return {

        /**
         * Downloads an email attachment.
         * The options argument for the events fired from the DownloadManager
         * related to this download type are the following:
         * attachmentId, attachmentKey, attachmentName.
         * The type available in the events fired is "emailAttachment".
         *
         * @param {Number} id
         * @param {Mixed} key
         * @param {String} name
         *
         */
        downloadEmailAttachment : function(id, key, name)
        {
            var url = './groupware/email.attachment/download.attachment/id/'
                + id
                + '/key/'
                + key;

            if (downloads[url]) {
                return;
            }

            var download = new com.conjoon.cudgets.data.Download({
                url : url
            });

            downloads[url] = download;

            var func = null;
            for (var i = 0, len = events.length; i < len; i++) {
                func = function(download, eventName) {
                    if (eventName != 'request') {
                        removeDownload(url);
                    }
                    kernel.fireEvent(eventName, download, 'emailAttachment', {
                        attachmentId : id, attachmentKey : key, attachmentName : name
                    }, DownloadManager);
                };

                download.on(events[i], func.createDelegate(DownloadManager, [download, events[i]]));
            }

            download.start();
        },

        on : function(eventName, func, scope, options)
        {
            kernel.on(eventName, func, scope, options);
        },

        un : function(eventName, func, scope)
        {
            kernel.un(eventName, func, scope);
        }

    };


}();