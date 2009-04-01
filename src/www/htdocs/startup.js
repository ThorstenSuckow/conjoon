/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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

    Ext.QuickTips.init();

    Ext.getBody().on('contextmenu', function(e){
        var t = e.getTarget().tagName;
        if (t != 'INPUT' && t != 'TEXTAREA') {
            e.stopEvent();
        }
    });

    var preLoader = com.conjoon.util.PreLoader;
    var groupware = com.conjoon.groupware;

    preLoader.addStore(groupware.email.AccountStore.getInstance());
    preLoader.addStore(groupware.feeds.AccountStore.getInstance());
    preLoader.addStore(groupware.Registry.getStore());
    preLoader.addStore(groupware.feeds.FeedStore.getInstance(), {
        ignoreLoadException : true,
        loadAfterStore      : groupware.feeds.AccountStore.getInstance()
    });

    preLoader.on('load', function() {
        groupware.email.Letterman.wakeup();

        com.conjoon.util.Registry.register(
            'com.conjoon.groupware.Workbench',
            new groupware.Workbench()
        );

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

        }).defer(1);
    });

    groupware.Reception.init(true);
    groupware.Reception.onUserLoad(function(){
        com.conjoon.util.PreLoader.load();
    });





 });