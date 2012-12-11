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

    var typeFile            = 'file';
    var typeEmailAttachment = 'emailAttachment';

    var downloads = {};
    downloads[typeFile]            = [];
    downloads[typeEmailAttachment] = [];

    var events = ['cancel', 'failure', 'error', 'request', 'success'];


    var removeDownload = function(url, type) {
        delete downloads[type][url];
    };

    var getUrl = function(id, key, type) {
        return './groupware/file/download.file/id/'+id+'/key/'+key
               +'/type/'+type;

    };

    var downloadImpl = function(id, key, name, type, messageId, path) {

        var DownloadManager = com.conjoon.groupware.DownloadManager;

        var url = getUrl(id, key, type),
            messageProperties = null;

        switch (type) {
            case DownloadManager.TYPE_EMAIL_ATTACHMENT:
                messageProperties = {
                    attachmentId : id, attachmentKey : key, attachmentName : name
                };
            break;

            case DownloadManager.TYPE_FILE:
                messageProperties = {
                    fileId : id, fileKey : key, fileName : name
                };
            break;

            default:
                throw("Unregistered download type: \""+type+"\"");
            break;
        }

        if (downloads[type][url]) {
            return;
        }

        var download = new com.conjoon.cudgets.data.Download({
            url    : url,
            params : {
                name      : name,
                messageId : messageId,
                path      : path
            }
        });

        downloads[type][url] = download;

        var func = null;
        for (var i = 0, len = events.length; i < len; i++) {
            func = function(download, eventName) {
                if (eventName != 'request') {
                    removeDownload(url, type);
                }
                kernel.fireEvent(
                    eventName, download, type, messageProperties,
                    DownloadManager
                );
            };

            download.on(events[i], func.createDelegate(
                DownloadManager, [download, events[i]]
            ));
        }

        download.start();

    };

    return {

        TYPE_FILE             : typeFile,
        TYPE_EMAIL_ATTACHMENT : typeEmailAttachment,

        /**
         * Cancels the download for the specified id, key and type.
         *
         * @param {Number} id
         * @param {Mixed} key
         * @param {String} type
         */
        cancelDownloadForIdAndKey : function(id, key, type)
        {
            var url = getUrl(id, key, type);
            var dl  = downloads[type][url];
            if (dl) {
                dl.cancel();
                removeDownload(url, type);
            }
        },

        /**
         * Downloads a regular file.
         *
         * The options argument for the events fired from the DownloadManager
         * related to this download type are the following:
         * fileId, fileKey, fileName.
         * The type available in the events fired is "file".
         *
         * @param {Number} id
         * @param {Mixed} key
         * @param {String} name
         */
        downloadFile : function(id, key, name)
        {
            downloadImpl(id, key, name,
                com.conjoon.groupware.DownloadManager.TYPE_FILE);
        },

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
        downloadEmailAttachment : function(id, key, name, messageId, path)
        {
            downloadImpl(id, key, name,
                com.conjoon.groupware.DownloadManager.TYPE_EMAIL_ATTACHMENT,
                messageId, Ext.util.JSON.encode(path));
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