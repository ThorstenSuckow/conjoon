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
 * $Author: T. Suckow $
 * $Id: EmailGrid.js 1985 2014-07-05 13:00:08Z T. Suckow $
 * $Date: 2014-07-05 15:00:08 +0200 (Sa, 05 Jul 2014) $
 * $Revision: 1985 $
 * $LastChangedDate: 2014-07-05 15:00:08 +0200 (Sa, 05 Jul 2014) $
 * $LastChangedBy: T. Suckow $
 * $URL: http://svn.conjoon.org/trunk/src/corelib/js/source/groupware/email/EmailGrid.js $
 */

/*
 * Controller for the mail grid.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
Ext.defineClass('conjoon.mail.grid.comp.controller.GridController', {

    /**
     * @cfg {Object} errorMessageCompConfig
     * A config object that will be applied to the error message comp.
     * Any of the config options available for {conjoon.mail.grid.comp.ErrorMessageComp}
     * can be specified here. This option is ignored if errorMessageComp is specified.
     */
    errorMessageCompConfig : null,

    /**
     * @cfg {com.conjoon.groupware.email.EmailGrid} grid
     * The grid this controller is bound to.
     */
    grid : null,

    /**
     * @param {conjoon.mail.grid.comp.ErrorMessageComp} errorMessageComp
     */
    errorMessageComp : null,

    /**
     * @param {conjoon.mail.grid.comp.listener.GridStoreListener} gridStoreListener
     */
    gridStoreListener : null,

    /**
     * @param {conjoon.mail.grid.comp.listener.ErrorMessageCompListener} gridStoreListener
     */
    errorMessageCompListener : null,

    /**
     * @param {conjoon.mail.grid.comp.listener.GridListener} gridStoreListener
     */
    gridListener : null,

    /**
     * @param {conjoon.mail.grid.comp.listener.GridViewListener} gridViewListener
     */
    gridViewListener : null,

    /**
     * @param {Object} config
     *
     * @throws {cudgets.base.InvalidPropertyException}
     */
    constructor : function(config) {

        var me = this;

        if (!config || !config.grid) {
            throw new cudgets.base.InvalidPropertyException(
                "no valid grid configured for listener."
            );
        }

        if (!(config.grid instanceof com.conjoon.groupware.email.EmailGrid)) {
            throw new cudgets.base.InvalidPropertyException(
                "grid not instance of com.conjoon.groupware.email.EmailGrid"
            );
        }

        Ext.apply(me, config);

        me.initListeners();
    },

    /**
     * @private
     */
    initListeners : function() {

        var me = this;

        me.gridStoreListener = Ext.createInstance(
            'conjoon.mail.grid.comp.listener.GridStoreListener', {
            store          : me.grid.store,
            gridController : me
        });
        me.gridStoreListener.init();

        me.errorMessageCompListener = Ext.createInstance(
            'conjoon.mail.grid.comp.listener.ErrorMessageCompListener', {
            errorMessageComp : me.getErrorMessageComp(),
            gridController : me
        });
        me.errorMessageCompListener.init();

        me.gridListener = Ext.createInstance(
            'conjoon.mail.grid.comp.listener.GridListener', {
            grid           : me.grid,
            gridController : me
        });
        me.gridListener.init();

        me.gridViewListener = Ext.createInstance(
            'conjoon.mail.grid.comp.listener.GridViewListener', {
            view           : me.grid.getView(),
            gridController : me
        });
        me.gridViewListener.init();

    },

// --------- public API

// ---- grid

    /**
     * Returns the grid used by this controller.
     *
     * @return {com.conjoon.groupware.email.EmailGrid}
     */
    getGrid : function() {
        var me = this;

        return me.grid;
    },

    /**
     * Tries to reload the current view by keeping scroll position and
     * selections.
     */
    reloadCurrentGridView : function() {
        var me = this,
            gridPanel = me.getGrid(),
            state     = Ext.state.Manager.get(gridPanel.stateId);

        if (!gridPanel.stateNodeId) {
            gridPanel.view.reset(true);
        }

        gridPanel.reloadFromState(state, gridPanel.stateNodeId);
    },

// ---- grid store


// ---- errorMessageComp
    /**
     * Returns the error message comp for the grid.
     *
     * @return {conjoon.mail.grid.comp.ErrorMessageComp}
     *
     * @throws {cudgets.base.InvalidPropertyException}
     */
    getErrorMessageComp : function() {

        var me = this;

        if (!me.errorMessageComp) {
            if (!me.errorMessageCompConfig || !Ext.isObject(me.errorMessageCompConfig)) {
                throw new cudgets.base.InvalidPropertyException(
                    "errorMessageComp not available, and errorMessageCompConfig " +
                    "does not seem to be properly configured"
                );
            }
            me.errorMessageComp = Ext.createInstance(
                'conjoon.mail.grid.comp.ErrorMessageComp',
                me.errorMessageCompConfig
            );
        }

        return me.errorMessageComp;
    },

    /**
     * Shows the error message comp.
     */
    showErrorMessageComp : function() {

        var me = this,
            gridEl = me.grid.el,
            errorMessageComp = me.getErrorMessageComp();

        if (!errorMessageComp.rendered) {
            errorMessageComp.render(gridEl);
        }

        errorMessageComp.show();
        me.centerErrorMessageComp();
    },

    /**
     * Hides the error message comp.
     */
    hideErrorMessageComp : function() {

        var me = this,
            errorMessageComp = me.getErrorMessageComp();

        if (!errorMessageComp.rendered) {
            return;
        }

        errorMessageComp.hide();
    },

    /**
     * Centers the error message comp.
     */
    centerErrorMessageComp : function() {

        var me = this,
            gridEl = me.grid.el,
            errorMessageComp = me.getErrorMessageComp();

        if (!errorMessageComp.rendered || !errorMessageComp.isVisible()) {
            return;
        }

        errorMessageComp.center(gridEl);
    },

    /**
     * @inheritdoc
     * @private
     */
    destroy : function() {

        var me = this;

        me.gridStoreListener.destroy();
        me.gridStoreListener = null;

        me.gridListener.destroy();
        me.gridListener = null;

        me.gridViewListener.destroy();
        me.gridViewListener = null;

        me.getErrorMessageComp().destroy();
        me.errorMessageComp = null;

        me.errorMessageCompListener.destroy();
        me.errorMessageCompListener = null;

    }

});
