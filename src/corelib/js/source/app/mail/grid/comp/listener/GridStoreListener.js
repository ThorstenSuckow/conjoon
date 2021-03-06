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
 * $Id: FolderMenuListener.js 1985 2014-07-05 13:00:08Z T. Suckow $
 * $Date: 2014-07-05 15:00:08 +0200 (Sa, 05 Jul 2014) $
 * $Revision: 1985 $
 * $LastChangedDate: 2014-07-05 15:00:08 +0200 (Sa, 05 Jul 2014) $
 * $LastChangedBy: T. Suckow $
 * $URL: http://svn.conjoon.org/trunk/src/corelib/js/source/app/mail/folder/comp/listener/FolderMenuListener.js $
 */


/**
 * Default listener for the mail grid store's events
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
Ext.defineClass('conjoon.mail.grid.comp.listener.GridStoreListener', {

    /**
     * @type {Boolean}
     */
    isInit : false,

    /**
     * @type {Ext.ux.grid.livegrid.Store}
     */
    store : null,

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

        if (!config || !config.store) {
            throw new cudgets.base.InvalidPropertyException(
                "no valid store configured for listener."
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

        if (!(config.store instanceof Ext.ux.grid.livegrid.Store)) {
            throw new cudgets.base.InvalidPropertyException(
                "store not instance of Ext.ux.grid.livegrid.Store"
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
            me.store.on('exception', me.onStoreException, me);
            me.store.on('beforeload', me.onStoreBeforeLoad, me);
        } else {
            me.store.un('exception', me.onStoreException, me);
            me.store.un('beforeload', me.onStoreBeforeLoad, me);
        }

    },

// listeners --------

    /**
     *
     * @param {Ext.data.Proxy} proxy The proxy that sent the request
     * @param {String} type The value of this parameter will be either 'response'
     * or 'remote'.
     *  - 'response': An invalid response from the server was returned: either 404,
     *                500 or the response meta-data does not match that defined in
     *                the DataReader (e.g.: root, idProperty, successProperty).
     *   - 'remote':  A valid response was returned from the server having
     *                successProperty === false. This response might contain an
     *                error-message sent from the server. For example, the user may have
     *                failed authentication/authorization or a database validation error
     *                occurred.
     * @param {String} action Name of the action (see Ext.data.Api.actions)
     * @param {Object} options The options for the action that were specified in the
     * request.
     * @param {Object} response The value of this parameter depends on the value of the
     * type parameter:
     *   - 'response': The raw browser response object (e.g.: XMLHttpRequest)
     *   - 'remote': The decoded response object sent from the server.
     * @param {Mixed} arg The type and value of this parameter depends on the value of
     * the type parameter:
     *   - 'response': Error The JavaScript Error object caught if the configured Reader
     *                 could not read the data. If the remote request returns
     *                 success===false, this parameter will be null.
     *   - 'remote': Record/Record[] This parameter will only exist if the action was a
     *               write action (Ext.data.Api.actions.create|update|destroy).
     *
     */
    onStoreException : function(proxy, type, action, options, response, arg)
    {
        var me = this,
            gridController = me.gridController;

        gridController.showErrorMessageComp();

        com.conjoon.groupware.ResponseInspector.handleFailure(response, {
            onLogin: {
                fn : function(){
                    this.gridController.reloadCurrentGridView();
                },
                scope : this
            },
            log : true,
            show : false
        });
    },

    /**
     * Listener for the store's beforeload event.
     *
     */
    onStoreBeforeLoad : function() {
        var me = this;

        me.gridController.hideErrorMessageComp();
    },

// -------- inherit

    /**
     * @private
     */
    destroy : function() {
        this.installEvents(false);
    }

});
