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

com.conjoon.groupware.NotificationManager = function(){

    var gettext = com.conjoon.Gettext.gettext;

    var _subscribe = function()
    {
        Ext.ux.util.MessageBus.subscribe(
            'com.conjoon.groupware.feeds.AccountStore.update',
            _accountStoreUpdated
        );

        com.conjoon.groupware.DownloadManager.on(
            'request',
            _downloadRequested
        );
    };

    var _downloadRequested = function(download, type, options)
    {
        var fileName = "";

        switch (type) {
            case 'emailAttachment':
                fileName = options.attachmentName;
            break;
        }

        new Ext.ux.ToastWindow({
                title   : gettext("Download"),
                width   : 250,
                delay   : 4000,
                html    : String.format(
                    com.conjoon.Gettext.gettext("The file \"{0}\" was put into the download queue."),
                    fileName
                )
            }).show(document);
    };

    var _accountStoreUpdated = function(subject, message)
    {
        var oldRequestTimeout = message.oldRequestTimeout / 1000;
        var requestTimeout    = message.requestTimeout / 1000 ;

        var maxExecutionTime = com.conjoon.groupware.Registry.get(
            '/server/php/max_execution_time'
        );

        if (maxExecutionTime <= requestTimeout) {

            var text = String.format(
                gettext("The computed overall request timeout of {0} seconds exceeds PHP's <i>max_execution_time</i>-setting of {1} seconds."),
                requestTimeout,
                maxExecutionTime
            );

            new Ext.ux.ToastWindow({
                title   : gettext("Warning"),
                width   : 250,
                delay   : 4000,
                html    : text
            }).show(document);
        }
    };

    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.ready',
        _subscribe
    );

    return {

    };

}();