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

/**
 * Inits the application when used with an iPhone/iPod device.
 *
 */
Ext.onReady(function(){

    Ext.QuickTips.init();

    var preLoader = com.conjoon.util.PreLoader;
    var groupware = com.conjoon.groupware;

    preLoader.addStore(groupware.Registry.getStore());

    preLoader.on('load', function() {
        new com.conjoon.iphone.groupware.Workbench();

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

    groupware.Reception.init(true, {
        loginWindowClass : com.conjoon.iphone.groupware.reception.LoginWindow
    });
    groupware.Reception.onUserLoad(function(){
        com.conjoon.util.PreLoader.load();
    });




 });