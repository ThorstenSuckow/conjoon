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

Ext.namespace('com.conjoon.groupware.email.view');

/**
 * Extends Ext.ux.grid.GridViewMenuPlugin for adding custom styles and to conform
 * with the column model of the email grid when showing column's header in the menu.
 *
 * @class com.conjoon.groupware.email.view.GridViewMenuPlugin
 * @extends Ext.ux.grid.GridViewMenuPlugin
 */
com.conjoon.groupware.email.view.GridViewMenuPlugin = Ext.extend(Ext.ux.grid.GridViewMenuPlugin, {


    _beforeColMenuShow : function(menu)
    {
        var cm = this._view.cm;
        cm.usePlainHeaders = true;

        com.conjoon.groupware.email.view.GridViewMenuPlugin.superclass._beforeColMenuShow.call(this, menu);

        cm.usePlainHeaders = false;
    }

});