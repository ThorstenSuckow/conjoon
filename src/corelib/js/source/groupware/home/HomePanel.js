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

Ext.namespace('com.conjoon.groupware.home');

/**
 *
 * @class com.conjoon.groupware.home.HomePanel
 */
com.conjoon.groupware.home.HomePanel = Ext.extend(Ext.Panel, {

    initComponent : function()
    {
        Ext.apply(this, {
            title      : com.conjoon.Gettext.gettext("Welcome"),
            cls        : 'com-conjoon-groupware-HomePanel-body',
            closable   : false,
            iconCls    : 'com-conjoon-groupware-HomePanel-icon',
            id         : 'DOM:com.conjoon.groupware.HomePanel',
            autoScroll : true
        });

        com.conjoon.util.Registry.register('com.conjoon.groupware.HomePanel', this, true);

        com.conjoon.groupware.home.HomePanel.superclass.initComponent.call(this);
    }

});