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

    Ext.getBody().on('contextmenu', function(e){var t = e.getTarget().tagName; if (t != 'INPUT' && t != 'TEXTAREA'){e.stopEvent();}});

    com.conjoon.util.PreLoader.addStore(com.conjoon.groupware.email.AccountStore.getInstance());
    com.conjoon.util.PreLoader.addStore(com.conjoon.groupware.feeds.AccountStore.getInstance());
    com.conjoon.util.PreLoader.addStore(com.conjoon.groupware.Registry.getStore());
    com.conjoon.util.PreLoader.addStore(com.conjoon.groupware.feeds.FeedStore.getInstance(), true);

    com.conjoon.util.PreLoader.on('load', function() {
        com.conjoon.groupware.email.Letterman.wakeup();
        new com.conjoon.groupware.Workbench();

        (function(){
            Ext.fly(document.getElementById('DOM:com.conjoon.groupware.Startup')).fadeOut({
                endOpacity : 0, //can be any value between 0 and 1 (e.g. .5)
                easing     : 'easeOut',
                duration   : .5,
                remove     : true,
                useDisplay : false
            });
        }).defer(1);
    });

    com.conjoon.groupware.Reception.init(true);
    com.conjoon.groupware.Reception.onUserLoad(function(){
        com.conjoon.util.PreLoader.load();
    });





 });