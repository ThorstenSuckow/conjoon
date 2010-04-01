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
 *
 * @class com.conjoon.groupware.DownloadManager
 * @singleton
 */
com.conjoon.groupware.DownloadManager = function() {

    var downloads = [];


    var removeDownload = function(url) {
        delete downloads[url];
    };

    return {

        downloadEmailAttachment : function(id, key, name, htmlElement)
        {
            var url = './groupware/email.item/download.attachment/id/'
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

            var func = function() {
                Ext.fly(htmlElement).removeClass('request');
                removeDownload(url);
            };

            var Bus = Ext.ux.util.MessageBus;

            download.on('success', function(){
                func();
                Bus.publish(
                    'com.conjoon.groupware.DownloadManager.success', {
                        type : 'emailAttachment',
                        name : name,
                        url  : url
                    }
                )
            });
            download.on('error', function() {
                func();
                Bus.publish(
                    'com.conjoon.groupware.DownloadManager.error', {
                        type : 'emailAttachment',
                        name : name,
                        url  : url,
                        text : com.conjoon.Gettext.gettext("Could not download.")
                    }
                );
            });
            download.on('request',       function() {
                Ext.fly(htmlElement).addClass('request');
                Ext.ux.util.MessageBus.publish(
                    'com.conjoon.groupware.DownloadManager.request', {
                        type : 'emailAttachment',
                        name : name,
                        url  : url
                    }
                );
            });
            download.on('cancel', function() {
                func();
                Bus.publish(
                    'com.conjoon.groupware.DownloadManager.cancel', {
                        type : 'emailAttachment',
                        name : name,
                        url  : url
                    }
                );
            });
            download.on('failure', function() {
                func();
                Bus.publish(
                    'com.conjoon.groupware.DownloadManager.failure', {
                        type : 'emailAttachment',
                        name : name,
                        url  : url,
                        text : com.conjoon.Gettext.gettext("Could not download.")
                    }
                );
            });

            download.start();
        }

    };


}();