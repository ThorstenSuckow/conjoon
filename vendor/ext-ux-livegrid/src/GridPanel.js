/**
 * Ext.ux.grid.livegrid.GridPanel
 * Copyright (c) 2007-2014, http://www.siteartwork.de
 *
 * Ext.ux.grid.livegrid.GridPanel is licensed under the terms of the
 *                  GNU Open Source GPL 3.0
 * license.
 *
 * Commercial use is prohibited. Visit <http://ext-livegrid.com>
 * if you need to obtain a commercial license.
 *
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see <http://www.gnu.org/licenses/gpl.html>.
 *
 */

Ext.namespace('Ext.ux.grid.livegrid');

/**
 * @class Ext.ux.grid.livegrid.GridPanel
 * @extends Ext.grid.GridPanel
 * @constructor
 * @param {Object} config
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
Ext.ux.grid.livegrid.GridPanel = Ext.extend(Ext.grid.GridPanel, {

    initComponent : function()
    {
        if (this.cls) {
            this.cls += ' ext-ux-livegrid';
        } else {
            this.cls = 'ext-ux-livegrid';
        }

        Ext.ux.grid.livegrid.GridPanel.superclass.initComponent.call(this);
    },

    /**
     * Overriden to make sure the events fired from the grid's view and store
     * are considered state events.
     *
     * @inheritdoc
     */
    initStateEvents : function(){

        var me = this;

        Ext.ux.grid.livegrid.GridPanel.superclass.initStateEvents.call(me);

        this.installStateEvents(true);
    },

    /**
     * Removes all state events listener attached to this livegrid.
     * The events removed are only those which are explicitely set
     * for the livegrid's view, store and selectionmodel.
     *
     * @param {Boolean} install true to install the events, otherwise false
     */
    installStateEvents : function(install) {

        var me = this;

        if (install === false) {
            me.mun(me.view, 'buffer', me.saveState, me);
            me.mun(me.view, 'cursormove', me.saveState, me);
            me.mun(me.store, 'remove', me.saveState, me);
            me.mun(me.store, 'add', me.saveState, me);
            me.mun(me.store, 'bulkremove', me.saveState);
            me.mun(me.store, 'clear', me.saveState, me);
            me.mun(me.selModel, 'selectionchange', me.saveState, me);
        } else {
            var opt = {delay : 100};
            me.mon(me.view, 'buffer', me.saveState, me, opt);
            me.mon(me.view, 'cursormove', me.saveState, me, opt);
            me.mon(me.store, 'remove', me.saveState, me, opt);
            me.mon(me.store, 'add', me.saveState, me, opt);
            me.mon(me.store, 'bulkremove', me.saveState, me, opt);
            me.mon(me.store, 'clear', me.saveState, me, opt);
            me.mon(me.selModel, 'selectionchange', me.saveState, me, opt);
        }

    },

    /**
     * A helper frunction that tries to automatically reload the grid
     * given its state information, if available.
     * If state information not available, the grid willsimple reload.
     *
     * @param {Object} state An object with the state information
     *
     * @see applyState/getState
     */
    reloadFromState : function(state) {

        var me = this;

        if (!me.stateId || ! state) {
            me.view.reset(true);
            return;
        }

        me.installStateEvents(false);
        me.applyState.apply(me, arguments);
        me.installStateEvents(true);
        me.view.reset(true);
    },


    /**
     * Overriden to make sure that rowIndex and buffer from the livegrid's view/store
     * are considered when returning states.
     *
     * @inheritdoc
     */
    getState : function() {

        var state = null,
            me = this;

        state = Ext.ux.grid.livegrid.GridPanel.superclass.getState.apply(me);

        Ext.apply(state, {
            bufferRange : me.store.bufferRange,
            rowIndex : me.view.rowIndex,
            lastScrollPos : me.view.lastScrollPos,
            lastRowIndex : me.view.lastRowIndex,
            lastIndex : me.view.lastIndex,
            selections : me.selModel.getState()
        });

        return state;
    },

    /**
     * Overridden to make sure that the store is properly loaded with the state
     * values.
     *
     * @inheritdoc
     */
    applyState : function(state) {

        var me = this,
            selections = state.selections,
            bufferRange = state.bufferRange
                ? Math.max(Math.max(state.bufferRange[0], state.rowIndex), 0)
                : 0,
            conf = {
                rowIndex :  state.rowIndex,
                lastScrollPos : state.lastScrollPos,
                lastRowIndex : state.lastRowIndex,
                lastIndex : state.lastIndex
            };

        var beforeLoadCallback = Ext.createDelegate(
            function(store, options, conf, selections, bufferRange) {

                Ext.apply(options, {
                    forceStart : true,
                    callback : function() {
                        me.selModel.clearSelections(true);

                        me.view.reset(conf);

                        // actually, applyState for the selection model will
                        // work on the first try when the store gets loaded
                        // if loading failed, the selection model
                        // cannot access any records in the store, thus
                        // not selecting any record.
                        // the next time the store is loaded, the state
                        // selections will be gone
                        if (selections) {
                            me.selModel.applyState(selections);
                        }
                    }
                });

                Ext.apply(options.params, {start : bufferRange});

            }, me, [conf, selections, bufferRange], true
        );

        Ext.ux.grid.livegrid.GridPanel.superclass.applyState.apply(me, arguments);

        me.store.on('beforeload', beforeLoadCallback, me, {single : true});

    },

    /**
     * Overriden to make sure the attached store loads only when the
     * grid has been fully rendered if, and only if the store's
     * "autoLoad" property is set to true.
     *
     */
    onRender : function(ct, position)
    {
        Ext.ux.grid.livegrid.GridPanel.superclass.onRender.call(this, ct, position);

        var ds = this.getStore();

        if (ds._autoLoad === true) {
            delete ds._autoLoad;
            ds.load();
        }
    },

    /**
     * Overriden since the original implementation checks for
     * getCount() of the store, not getTotalCount().
     *
     */
    walkCells : function(row, col, step, fn, scope)
    {
        var ds  = this.store;
        var _oF = ds.getCount;

        ds.getCount = ds.getTotalCount;

        var ret = Ext.ux.grid.livegrid.GridPanel.superclass.walkCells.call(this, row, col, step, fn, scope);

        ds.getCount = _oF;

        return ret;
    }

});
