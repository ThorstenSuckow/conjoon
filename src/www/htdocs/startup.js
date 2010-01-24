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

Ext.onReady(function(){

    var preLoader           = com.conjoon.util.PreLoader
    var groupware           = com.conjoon.groupware;
    var emailAccountStore   = groupware.email.AccountStore.getInstance();
    var feedsAccountStore   = groupware.feeds.AccountStore.getInstance();
    var registryStore       = groupware.Registry.getStore();
    var mappingStore        = groupware.email.options.folderMapping.data.Store.getInstance();
    var feedsFeedStore      = groupware.feeds.FeedStore.getInstance();
    var reception           = groupware.Reception
    var twitterAccountStore = com.conjoon.service.twitter.data.AccountStore.getInstance();

    var loadingCont = document.getElementById(
        'com.conjoon.groupware.Startup.loadingCont'
    );

    var loadingInd = null;

    var groupedStores = {
        emailStores : 2
    };
    var _load = function(store) {
        var id = store.storeId;

        if (id === emailAccountStore.storeId || id === mappingStore.storeId) {
            id = 'emailStores';
        }

        if (groupedStores[id] && (--groupedStores[id]) > 0) {
            return;
        }

        _updateIndicator(id);
    };

    var loadingFailed = false;
    var failMessages  = "";

    var _showErrorMessage = function(systemMessage)
    {
        var p = document.getElementById('com-conjoon-groupware-SplashScreen-errorPanel');

        if (!p) {
            return;
        }

        p.style.display = 'block';

        var noticeP  = document.getElementById('com-conjoon-groupware-SplashScreen-error-notice');
        var messageP = document.getElementById('com-conjoon-groupware-SplashScreen-error-message');

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

        if (id === emailAccountStore.storeId || id === mappingStore.storeId) {
            id = 'emailStores';
        }

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
        div.innerHTML = div.innerHTML + '&nbsp;' + com.conjoon.Gettext.gettext("Failed :(");
    };

    var _updateIndicator = function(id) {
        var div = document.getElementById(id);
        if (!div) {
            return;
        }

        if (id == 'emailStores') {
            if (groupedStores[id] > 0) {
                return;
            }
        }

        Ext.fly(div).addClass('done');
        div.innerHTML = div.innerHTML + '&nbsp;' + com.conjoon.Gettext.gettext("Done!");
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
        loadingCont.appendChild(cn);
    };

    var _beforeLoad = function(store) {

        var msg = "";
        var id  = store.storeId;

        switch (store) {
            case twitterAccountStore:
                msg = com.conjoon.Gettext.gettext("Loading Twitter accounts...");
            break;

            case mappingStore:
            case emailAccountStore:
                msg = com.conjoon.Gettext.gettext("Loading Email accounts...");
                id  = 'emailStores';
            break;

            case feedsAccountStore:
                msg = com.conjoon.Gettext.gettext("Loading Feed accounts...");
            break;

            case registryStore:
                msg = com.conjoon.Gettext.gettext("Loading Registry...");
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
    preLoader.addStore(mappingStore);
    preLoader.addStore(feedsAccountStore);
    preLoader.addStore(registryStore);
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

        if (groupware.Registry.get('/client/system/sfx/enabled')) {
            com.conjoon.groupware.SystemSoundManager.initDriver();
         }

        (function(){
            Ext.fly(document.getElementById('DOM:com.conjoon.groupware.Startup')).fadeOut({
                endOpacity : 0, //can be any value between 0 and 1 (e.g. .5)
                easing     : 'easeOut',
                duration   : .5,
                remove     : true,
                useDisplay : false
            });

            com.conjoon.SystemMessageManager.setContext(groupware.Registry.get(
                '/client/environment/device'
            ));

            Ext.ux.util.MessageBus.publish('com.conjoon.groupware.ready');

        }).defer(100);
    });

    reception.init(true);
    reception.onUserLoad(function(){
        preLoader.load();
    });

});