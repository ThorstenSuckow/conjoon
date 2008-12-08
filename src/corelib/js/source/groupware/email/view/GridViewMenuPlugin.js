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


    _beforeColMenuShow : function()
    {
        var cm = this.cm,  colCount = cm.getColumnCount();
        this.colMenu.removeAll();
        for(var i = 0; i < colCount; i++){
            if(cm.config[i].fixed !== true && cm.config[i].hideable !== false){
                this.colMenu.add(new Ext.menu.CheckItem({
                    id          : "col-"+cm.getColumnId(i),
                    text        : cm.getColumnHeader(i, true),
                    checked     : !cm.isHidden(i),
                    hideOnClick : false,
                    disabled    : cm.config[i].hideable === false
                }));
            }
        }
    }

});