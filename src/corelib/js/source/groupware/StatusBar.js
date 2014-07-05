/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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

com.conjoon.groupware.StatusBar = function(){

    /**
     * Since ext 3.0, Ext.StatusBar was removed from the repository.
     * This method will assign the methods "setStatus" and "clearStatus"
     * to the _statusBar property.
     * The code is taken from the Ext 2.2.1 branch and modified slighty.
     */
    var _hookMethods = function() {

        _statusBar.setStatus = function(o) {

            o = o || {};

            if(typeof o == 'string'){
                o = {text:o};
            }
            if(o.text !== undefined){
                _activeThreadId++;
                _statusItem.setText(o.text);
            }

            if(o.clear){
                var c        = o.clear,
                    wait     = _autoClear,
                    defaults = {useDefaults: true, anim: true};

                if(typeof c == 'object'){
                    c = Ext.applyIf(c, defaults);
                    if(c.wait){
                        wait = c.wait;
                    }
                }else if(typeof c == 'number'){
                    wait = c;
                    c = defaults;
                }else if(typeof c == 'boolean'){
                    c = defaults;
                }

                c.threadId = _activeThreadId;
                this.clearStatus.defer(wait, this, [c]);
            }
            return this;
        };

        _statusBar.clearStatus = function(o){
            o = o || {};

            if(o.threadId && o.threadId !== _activeThreadId){
                // this means the current call was made internally, but a newer
                // thread has set a message since this call was deferred.  Since
                // we don't want to overwrite a newer message just ignore.
                return this;
            }

            _statusItem.el.fadeOut({
                remove     : false,
                useDisplay : true,
                scope      : this,
                callback   : function(){
                    this.setStatus({
                        text : _defaultText
                    });
                    _statusItem.el.show();
                }
            });

            return this;
        };

    };

    var _messageBroadcaster = Ext.ux.util.MessageBus;

    var _autoClear = 5000;

    var _statusBar = null;

    var _defaultText = "";

    var _activeThreadId = 0;

    var _statusItem = null;

    var _progressBar = null;

    var _activeRequestCount = 0;

    var _connectionInfo = null;

    var _downloadInfo = null;

    var _onBeforeRequest = function()
    {
        if (_activeRequestCount == 0) {
            _showProgressBar.defer(500);
        }
        _activeRequestCount++;
    };

    var _showProgressBar = function() {
        if (_activeRequestCount > 0) {
            _progressBar.show();
            _progressBar.wait({interval : 50, increment : 50});
        }
    };

    var _resetProgressBar = function() {
        if (_activeRequestCount <= 0) {
            _progressBar.reset(true);
        }
    };

    var _onRequestException = function()
    {
        _onRequestComplete();
    };

    var _onRequestAbort = function()
    {
        _onRequestComplete();
    };

    var _onRequestComplete = function()
    {
        _activeRequestCount = Math.max(0, --_activeRequestCount);

        if (_activeRequestCount <= 0) {
            _progressBar.clearTimer();
            _progressBar.updateProgress(1, "", true);
            _resetProgressBar.defer(500);
        }
    };

    var _activeDloads = 0;

    var _dloadAbort = function()
    {
        _activeDloads--;

        if (_activeDloads <= 0) {
            _activeDloads = 0;
            _downloadInfo.addClass('inActive');
        }

    };

    var _dloadStart = function()
    {
        _downloadInfo.removeClass('inActive');
        _activeDloads++;
    };

    var _transceive = function(subject, message)
    {
        switch (subject) {

            case 'conjoon.mail.MailFolder.beforeproxynodeload':
                _statusBar.setStatus({
                    text : com.conjoon.Gettext.gettext("Synchronizing mail folder...")
                });
                break;
            case 'conjoon.mail.MailFolder.proxynodeloadfailure':
                _statusBar.setStatus({
                    text : com.conjoon.Gettext.gettext("Synchronizing failed."),
                    clear : true
                });
                break;
            case 'conjoon.mail.MailFolder.proxynodeload':
                _statusBar.setStatus({
                    text  : com.conjoon.Gettext.gettext("Mail folder synchronized!"),
                    clear : true
                });
                break;

            case 'com.conjoon.groupware.email.Letterman.beforeload':
                _statusBar.setStatus({
                    text : com.conjoon.Gettext.gettext("Checking for new emails...")
                });
            break;

            case 'com.conjoon.groupware.email.Letterman.load':
                var total = message.total;
                var text  = com.conjoon.Gettext.gettext("No new emails");

                if (total > 0) {
                    text = String.format(
                        com.conjoon.Gettext.ngettext("One new email", "{0} new emails", total),
                        total
                    );
                }
                _statusBar.setStatus({
                    text  : text,
                    clear : true
                });
            break;

            case 'com.conjoon.groupware.email.Letterman.loadexception':
                _statusBar.setStatus({
                    text  : com.conjoon.Gettext.gettext("Error while trying to receive new emails"),
                    clear : true
                });
            break;

        }

    };

    var _subscribe = function()
    {
        var DownloadManager = com.conjoon.groupware.DownloadManager;
        DownloadManager.on('success', _dloadAbort);
        DownloadManager.on('failure', _dloadAbort);
        DownloadManager.on('error',   _dloadAbort);
        DownloadManager.on('request', _dloadStart);
        DownloadManager.on('cancel',  _dloadAbort);

        DownloadManager.on('success', _onRequestComplete);
        DownloadManager.on('failure', _onRequestComplete);
        DownloadManager.on('error',   _onRequestComplete);
        DownloadManager.on('request', _onBeforeRequest);
        DownloadManager.on('cancel',  _onRequestComplete);

        _messageBroadcaster.subscribe('com.conjoon.groupware.email.Letterman.beforeload', _transceive);
        _messageBroadcaster.subscribe('conjoon.mail.MailFolder.beforeproxynodeload', _transceive);
        _messageBroadcaster.subscribe('conjoon.mail.MailFolder.proxynodeload', _transceive);
        _messageBroadcaster.subscribe('com.conjoon.groupware.email.Letterman.load', _transceive);
        _messageBroadcaster.subscribe('com.conjoon.groupware.email.Letterman.loadexception', _transceive);

        var eao = Ext.Ajax;
        eao.on('beforerequest',    _onBeforeRequest);
        eao.on('requestcomplete',  _onRequestComplete);
        eao.on('requestexception', _onRequestException);
        _messageBroadcaster.subscribe('ext.lib.ajax.abort', _onRequestAbort);
    };

    return {

        getStatusBar : function(initConf)
        {
            if (_statusBar === null) {

                initConf = initConf || {};

                var pconf = {
                    cls     : 'com-conjoon-groupware-ProgressBar',
                    width   : 80,
                    hidden  : true
                };
                if (Ext.isIE) {
                    pconf.style = 'margin-top:1px;';
                }

                _progressBar = new Ext.ProgressBar(pconf);

                var t = document.createElement('div');
                t.id  = Ext.id();
                t.innerHTML = '&#160;';
                t.className = "com-conjoon-groupware-statusbar-ConnectionInfo";
                _connectionInfo = new Ext.Toolbar.Item(t);

                t = document.createElement('div');
                t.id  = Ext.id();
                t.innerHTML = '&#160;';
                t.className = "downloadInfo inActive";
                _downloadInfo = new Ext.Toolbar.Item(t);

                _defaultText = com.conjoon.Gettext.gettext("Ready");

                _statusItem = new Ext.Toolbar.TextItem({
                    text : _defaultText
                });

                var statusBarConf = Ext.apply({
                    border      : false,
                    id          : 'com.conjoon.groupware.StatusBar',
                    cls         : 'com-conjoon-groupware-StatusBar',
                    items       : [
                        _statusItem,
                        '->',
                        new Ext.Toolbar.Separator(),
                        new Ext.Toolbar.Spacer(),
                        _progressBar,
                        new Ext.Toolbar.Separator(),
                        new Ext.Toolbar.Spacer(),
                        _downloadInfo,
                        new Ext.Toolbar.Spacer(),
                        new Ext.Toolbar.Separator(),
                        new Ext.Toolbar.Spacer(),
                        _connectionInfo,
                        new Ext.Toolbar.Spacer()
                    ]
                }, initConf);

                _statusBar = new Ext.Toolbar(statusBarConf);

                _hookMethods();

                _statusBar.afterRender = _statusBar.afterRender.createSequence(
                    function() {
                        _connectionInfo.disable();
                    }
                );

                _subscribe();
            }



            return _statusBar;
        }

    };

}();
