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

Ext.namespace('com.conjoon.cudgets');

/**
 * An override for Ext.ListView to use in com.conjoon.cudgets.SettingsContainer.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.cudgets.ListView
 * @extends Ext.ListView
 */
com.conjoon.cudgets.ListView = Ext.extend(Ext.ListView, {

    /**
     * @type {Ext.data.Record} selectedEntry The selected record in the list,
     * or null, if no selection is available.
     */
    selectedEntry : null,

    initComponent : function()
    {
        this.addEvents(
            /**
             * @event entryselect
             * Gets fired when an entry is selected
             *
             * @param {Ext.ListView} listView
             * @param {Ext.data.Record} record The selected record
             */
            'entryselect',
            /**
             * @event entrydeselect
             * Gets fired when an entry is deselected
             *
             * @param {Ext.ListView} listView
             * @param {Ext.data.Record} record The selected record
             */
            'entrydeselect',
            /**
             * @event beforeentryselect
             * Gets fired when an entry is about to be selected. Event listeners
             * should return false to cancel this event.
             *
             * @param {Ext.ListView} listView
             * @param {Ext.data.Record} record The selected record
             */
            'beforeentryselect',
            /**
             * @event beforeentrydeselect
             * Gets fired when an entry is about to be deselected. Event listeners
             * should return false to cancel this event.
             *
             * @param {Ext.ListView} listView
             * @param {Ext.data.Record} record The record that is about to get deselected
             */
            'beforeentrydeselect'
        );


        this.on('selectionchange', this._onSelectionChange, this);
        this.on('beforeselect',    this._onBeforeSelect,    this);
        this.on('beforeclick',     this._onBeforeClick,    this);

        com.conjoon.cudgets.ListView.superclass.initComponent.call(this);
    },

// -------- API

    /**
     * Selects an entry in the list.
      *
     * @param {Ext.data.Record} record The record to select in the list.
     * @param {Boolean} silent True to suspend all events.
     */
    selectEntry : function(record, silent)
    {
        var index = this.store.indexOf(record);

        if (index != -1) {
            if (silent) {
                this.suspendEvents();
            }
            this.select(index, false, silent);
            if (silent) {
                this.selectedEntry = record;
                this.resumeEvents();
            }
        }
    },

    /**
     * Returns the currently selected record in the list, or null,
     * if there is currently no selection.
     *
     * @return {Ext.data.Record}
     */
    getSelectedEntry : function()
    {
        return this.selectedEntry;
    },

// -------- Listeners

    /**
     * Prevents selecting an item if it is already selected and neither
     * ctrl nor shift was pressed.
     *
     * @param {Ext.ListView} listView
     * @param {Number} index
     * @param {HtmlElement} item
     * @param {Object} event
     */
    _onBeforeClick : function(listView, index, item, e)
    {
        var nodeInfo = this.getNode(item);

        if (this.isSelected(nodeInfo) && !e.ctrlKey && !e.shiftKey) {
            return false;
        }

    },

    /**
     * Listener for the seelctionchange event of the list.
     * Translates this event to either the entrydeselect or entryselect event.
     *
     * @param {Ext.ListView} listView
     * @param {Array} selections
     */
    _onSelectionChange : function(listView, selections)
    {
        if (selections.length == 0) {
            var m = this.selectedEntry;
            this.selectedEntry = null;
            this.fireEvent('entrydeselect', this, m);
        } else {
            this.selectedEntry = this.getRecord(selections[0]);
            this.fireEvent('entryselect', this, this.selectedEntry);
        }

    },

    /**
     * Listener for the beforeseelct event of the list. Translates to the
     * beforeentryselect event.
     * Listeners for the beforeentryselect event may return false to
     * cancel this event.
     *
     * @param {Ext.ListView} listView
     * @param {HtmlElement} node
     * @param {Array} selections
     */
    _onBeforeSelect : function(listView, node, selections)
    {
        return this.fireEvent('beforeentryselect', this, this.getRecord(node));
    }


});