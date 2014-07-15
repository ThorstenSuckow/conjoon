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
 * $Author: T. Suckow $
 * $Id: FolderMenuListener.js 1985 2014-07-05 13:00:08Z T. Suckow $
 * $Date: 2014-07-05 15:00:08 +0200 (Sa, 05 Jul 2014) $
 * $Revision: 1985 $
 * $LastChangedDate: 2014-07-05 15:00:08 +0200 (Sa, 05 Jul 2014) $
 * $LastChangedBy: T. Suckow $
 * $URL: http://svn.conjoon.org/trunk/src/corelib/js/source/app/mail/folder/comp/listener/FolderMenuListener.js $
 */


/**
 * Default listener for the mail grid's view events
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
Ext.defineClass('conjoon.mail.grid.comp.listener.GridViewListener', {

    /**
     * @type {Boolean}
     */
    isInit : false,

    /**
     * @type {Ext.ux.grid.livegrid.GridView}
     */
    view : null,

    /**
     * @type {conjoon.mail.grid.comp.controller.GridController} gridController
     */
    gridController : null,

    /**
     * Creates a new instance.
     *
     * @throws {cudgets.base.InvalidPropertyException}
     */
    constructor : function(config) {

        var me = this;

        if (!config || !config.view) {
            throw new cudgets.base.InvalidPropertyException(
                "no valid view configured for listener."
            );
        }

        if (!config || !config.gridController) {
            throw new cudgets.base.InvalidPropertyException(
                "no valid gridController configured for listener."
            );
        }

        if (!(config.gridController instanceof conjoon.mail.grid.comp.controller.GridController)) {
            throw new cudgets.base.InvalidPropertyException(
                "gridController not instance of conjoon.mail.grid.comp.controller.GridController"
            );
        }

        if (!(config.view instanceof Ext.ux.grid.livegrid.GridView)) {
            throw new cudgets.base.InvalidPropertyException(
                "grid not instance of Ext.ux.grid.livegrid.GridPanel"
            );
        }

        Ext.apply(me, config);
    },

    /**
     * Attaches this listener to the events of the menu.
     */
    init : function() {

        var me = this;

        if (me.isInit) {
            return;
        }

        me.isInit = true;

        me.installEvents();
    },


    /**
     * @private
     */
    installEvents : function(install){

        var me = this;

        if (install !== false) {
            me.view.on('beforebuffer', me.onBeforeBuffer, me);
            me.view.on('reset', me.onReset, me);
        } else {
            me.view.un('beforebuffer', me.onBeforeBuffer, me);
            me.view.un('reset', me.onReset, me);
        }

    },

// listeners --------

    /**
     * Listener for the grid view's beforebuffer event
     */
    onBeforeBuffer : function()
    {
        var me = this,
            gridController = me.gridController;

        gridController.hideErrorMessageComp();
    },


    /**
     * Listener for the grid view's reset event
     */
    onReset : function(view, forceReload)
    {
        if (forceReload !== true) {
            return;
        }

        var me = this,
            gridController = me.gridController;

        gridController.hideErrorMessageComp();
    },

// -------- inherit

    /**
     * @private
     */
    destroy : function() {
        this.installEvents(false);
    }

});
