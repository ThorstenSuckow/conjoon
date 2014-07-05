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


// we are setting the adapter here. FF shows some strange behavior
// (such as not updating files) if we set the adapter in the update-
// ready method...
com.conjoon.cudgets.localCache.Api.setAdapter(
    new com.conjoon.groupware.localCache.Html5Adapter()
);


Ext.onReady(function(){

    com.conjoon.groupware.Registry = new com.conjoon.cudgets.direct.Registry({
        directProvider : com.conjoon.defaultProvider.registry
    });

    Ext.state.Manager.setProvider(new com.conjoon.cudgets.state.RegistryProvider({
        path     : '/client/system/state/',
        registry : com.conjoon.groupware.Registry
    }));

    var preLoader           = com.conjoon.util.PreLoader,
        groupware           = com.conjoon.groupware,
        emailAccountStore   = groupware.email.AccountStore.getInstance(),
        feedsAccountStore   = groupware.feeds.AccountStore.getInstance(),
        feedsFeedStore      = groupware.feeds.FeedStore.getInstance(),
        reception           = groupware.Reception,
        twitterAccountStore = com.conjoon.service.twitter.data.AccountStore.getInstance(),
        loadingCont         = document.getElementById(
                                  'cn_startup_splash_body_loadingCont'
                              ),
        loadingInd          = null,
        _load               = function(store) {
            var id = store.storeId;
            _updateIndicator(id);
        },
        loadingFailed = false,
        failMessages  = "";

    var _showErrorMessage = function(systemMessage)
    {
        var p = document.getElementById('cn_startup_splash_errorPanel');

        if (!p) {
            return;
        }

        Ext.fly(p).slideIn('t', {
           duration : 0.2
        });

        var noticeP  = document.getElementById('cn_startup_splash_errorPanel_errorNotice'),
           messageP = document.getElementById('cn_startup_splash_errorPanel_errorMessage');

        if (!loadingFailed) {
            Ext.fly(noticeP).update(
                com.conjoon.Gettext.gettext("Loading the application failed")
            );
            loadingFailed = true;
        }

        failMessages += systemMessage.text+"<br />--- <br />";

        Ext.fly(messageP).update(failMessages);
    };

    var _loadException = function(store, response, options) {

        var id = store.storeId;

        var config = preLoader.getStoreConfig(store);
        if (config && config.ignoreLoadException !== true) {
            var sm = com.conjoon.groupware.ResponseInspector.generateMessage(response, options);
            _showErrorMessage(sm);
        }
        _updateFailIndicator(id);
    };

    var _updateFailIndicator = function(id) {
        var div = document.getElementById(id);
        if (!div) {
            return;
        }
        Ext.fly(div).addClass('fail');
        div.innerHTML = div.innerHTML + '&nbsp;' + com.conjoon.Gettext.gettext("failed");
    };

    var _updateIndicator = function(id) {

        var div = document.getElementById(id);
        if (!div) {
            return;
        }

        Ext.fly(div).addClass('done');
        div.innerHTML = div.innerHTML + '&nbsp;' + com.conjoon.Gettext.gettext("done");
    };

    var _appendIndicator = function(msg, id) {
        if (document.getElementById(id)) {
            return;
        }
        if (!loadingInd) {
            loadingInd = document.createElement('div');
            loadingInd.className = 'loading';
        }

        var cn       = loadingInd.cloneNode(true);
        cn.innerHTML = msg;
        cn.id        = id;
        cn.style.display = "none";

        loadingCont.appendChild(cn);

        Ext.fly(cn).fadeIn({
            endOpacity : 1,
            duration   : 0.5
        });
    };

    var _beforeLoad = function(store) {

        var msg = "";
        var id  = (store.storeId ? store.storeId : store);

        switch (store) {
            case twitterAccountStore:
                msg = com.conjoon.Gettext.gettext("Loading Twitter accounts...");
            break;

            case emailAccountStore:
                msg = com.conjoon.Gettext.gettext("Loading Email accounts...");
            break;

            case feedsAccountStore:
                msg = com.conjoon.Gettext.gettext("Loading Feed accounts...");
            break;

            case 'registry':
                msg = com.conjoon.Gettext.gettext("Loading Registry...");
                id  = 'registry';
            break;

            case feedsFeedStore:
                msg = com.conjoon.Gettext.gettext("Loading Feeds...");
            break;
        }

        _appendIndicator(msg, id);
    };

    // add listeners
    preLoader.on('beforestoreload',    _beforeLoad);
    preLoader.on('storeload',          _load);
    preLoader.on('storeloadexception', _loadException);

    reception.onBeforeUserLoad(function() {
        _appendIndicator(
            com.conjoon.Gettext.gettext("Loading User..."),
            'reception-id'
        );
    });
    reception.onUserLoad(function(){
        _updateIndicator('reception-id');
    });
    reception.onUserLoadFailure(function(response){
        var sm = com.conjoon.groupware.ResponseInspector.generateMessage(response);
        _showErrorMessage(sm);
        _updateFailIndicator('reception-id');
    });

    Ext.QuickTips.init();

    Ext.getBody().on('contextmenu', function(e){
        var t = e.getTarget().tagName;
        if (t != 'INPUT' && t != 'TEXTAREA') {
            e.stopEvent();
        }
    });

    preLoader.addStore(emailAccountStore);
    preLoader.addStore(feedsAccountStore);
    preLoader.addStore(twitterAccountStore, {
        ignoreLoadException : true
    });
    preLoader.addStore(feedsFeedStore, {
        ignoreLoadException : true,
        loadAfterStore      : feedsAccountStore
    });

    preLoader.on('load', function() {

        preLoader.un('beforestoreload',    _beforeLoad);
        preLoader.un('storeload',          _load);
        preLoader.un('storeloadexception', _loadException);

        reception.removeAllListeners();

        groupware.email.Letterman.wakeup();

        com.conjoon.util.Registry.register(
            'com.conjoon.groupware.Workbench',
            new groupware.Workbench()
        );

        // this part is responsible for playing the startup sound
        // we have to check if the driver is available - we will
        // then play the sound if the com.conjoon.groupware.ready
        // message was already broadcasted. If, however, the
        // startmeup function is called before this message got
        // broadcasted, the subscriber for this message will take
        // care of playing the sound. We have to decouple both events
        // with starting up the workbench since starting the workbench
        // would fail if flash is not enabled/available on the client's
        // system. Added anon fn to refactor this code later on
        (function(){
            var _played         = false;
            var _workbenchReady = false;

            var startmeup = function(){
                if (_played || !_workbenchReady) {
                    return;
                }
                _played = true;
                com.conjoon.groupware.SystemSoundManager.getDriver().play('startup')
            };

            Ext.ux.util.MessageBus.subscribe('com.conjoon.groupware.ready',
                function() {
                    _workbenchReady = true;
                    if (_played) {
                        return;
                    }
                    var ssm = com.conjoon.groupware.SystemSoundManager;
                    if (ssm.isDriverReady()) {
                        _played = true;
                        ssm.getDriver().play('startup');
                    }
                }
            );

            if (groupware.Registry.get('/client/system/sfx/enabled')) {
                var ssm = com.conjoon.groupware.SystemSoundManager;
                ssm.onLoad({fn : startmeup});
                ssm.initDriver();
            }
        })();

        (function(){

            Ext.fly(document
                    .getElementById('cn_startup_splash_body'))
                .fadeOut({
                    endOpacity : 0, //can be any value between 0 and 1 (e.g. .5)
                    easing     : 'easeOut',
                    duration   : 1.5,
                    remove     : true,
                    useDisplay : false,
                    callback : function() {
                        Ext.ux.util.MessageBus.publish('com.conjoon.groupware.ready')
                        Ext.fly(document
                                .getElementById('cn_startup_splash'))
                            .fadeOut({
                                endOpacity : 0, //can be any value between 0 and 1 (e.g. .5)
                                easing     : 'easeOut',
                                duration   : 2,
                                remove     : true,
                                useDisplay : false
                            })}
                });

            com.conjoon.SystemMessageManager.setContext(
                groupware.Registry.get(
                    '/client/environment/device'
                )
            );
        }).defer(1000);

    });

    reception.init(true);
    reception.onUserLoad(function(){

        var user = reception.getUser(),
            greetText = user.lastLogin
                        ? com.conjoon.Gettext.gettext("Welcome back, %s").replace(/\%s/g, user.firstname)
                        : com.conjoon.Gettext.gettext("Welcome, %s").replace(/\%s/g, user.firstname);


        document.getElementById('cn_startup_splash_body_greetingCont').innerHTML = greetText;
        com.conjoon.groupware.Registry.beforeLoad({
            fn : function() {
                _beforeLoad('registry');
            }
        });

        Ext.fly(document
                .getElementById('cn_startup_splash_body'))
            .fadeIn({
                endOpacity : 1,
                duration   : 1.5,
                callback : function() {
                    com.conjoon.groupware.Registry.load({
                        fn : function() {
                            _updateIndicator('registry');
                        }
                    });
                    preLoader.load();
                }
            });
    });

});
