/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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
            case 'file':
                fileName = options.fileName;
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