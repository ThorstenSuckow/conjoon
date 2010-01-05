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