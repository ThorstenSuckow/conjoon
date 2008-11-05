/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: EmailGrid.js 276 2008-11-01 22:48:51Z T. Suckow $
 * $Date: 2008-11-01 23:48:51 +0100 (Sa, 01 Nov 2008) $
 * $Revision: 276 $
 * $LastChangedDate: 2008-11-01 23:48:51 +0100 (Sa, 01 Nov 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild_rep/trunk/src/corelib/js/source/groupware/email/EmailGrid.js $
 */

Ext.namespace('de.intrabuild.groupware.email.view');

/**
 * Extends Ext.ux.grid.GridViewMenuPlugin for adding custom styles and to conform
 * with the column model of the email grid when showing column's header in the menu.
 *
 * @class de.intrabuild.groupware.email.view.GridViewMenuPlugin
 * @extends Ext.ux.grid.GridViewMenuPlugin
 */
de.intrabuild.groupware.email.view.GridViewMenuPlugin = Ext.extend(Ext.ux.grid.GridViewMenuPlugin, {


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