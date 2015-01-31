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