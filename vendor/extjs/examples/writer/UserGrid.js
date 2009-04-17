/*
 * Ext JS Library 3.0 RC1
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */

Ext.ns('App', 'App.user');
/**
 * App.user.Grid
 * A typical EditorGridPanel extension.
 */
App.user.Grid = Ext.extend(Ext.grid.EditorGridPanel, {
	renderTo: 'user-grid',
	iconCls: 'icon-grid',
	frame: true,
    title: 'Users',
	autoScroll: true,
	height: 300,
	style: 'margin-top: 10px',

	initComponent : function() {

		// typical viewConfig
		this.viewConfig = {
            forceFit: true
        };

		// relay the Store's CRUD events into this grid so these events can be conveniently listened-to in our application-code.
		this.relayEvents(this.store, ['destroy', 'save', 'update']);

		// build toolbars and buttons.
		this.tbar = this.buildTopToolbar();
		this.bbar = this.buildBottomToolbar();
		this.buttons = this.buildUI();

		// super
		App.user.Grid.superclass.initComponent.call(this);
	},

	/**
	 * buildTopToolbar
	 */
	buildTopToolbar : function() {
		return [{
			text: 'Add',
			iconCls: 'silk-add',
			handler: this.onAdd,
			scope: this
		}, '-', {
			text: 'Delete',
			iconCls: 'silk-delete',
			handler: this.onDelete,
			scope: this
		}, '-'];
	},

	/**
	 * buildBottomToolbar
	 */
	buildBottomToolbar : function() {
		return ['<b>@cfg:</b>', '-', {
			text: 'batchSave',
			enableToggle: true,
			tooltip: 'When enabled, Store will save only with user intervention.',
			toggleHandler: function(btn, pressed) {
				this.store.batchSave = pressed;
			},
			scope: this
		}, '-', {
			text: 'writeAllFields',
			enableToggle: true,
			tooltip: 'When enabled, Writer will write all fields, not just those that changed',
			toggleHandler: function(btn, pressed) {
				store.writer.writeAllFields = pressed;
			},
			scope: this
		}, '-'];
	},

	/**
	 * buildUI
	 */
	buildUI : function() {
		return [{
			text: 'Save',
			iconCls: 'icon-save',
			handler: this.onSave,
			scope: this
		}];
	},

	/**
	 * onSave
	 */
	onSave : function(btn, ev) {
		this.store.save();
	},

	/**
	 * onAdd
	 */
	onAdd : function(btn, ev) {
		var u = new this.store.recordType({
	        first : '',
			last: '',
			email : ''
	    });
	    this.stopEditing();
	    this.store.insert(0, u);
	    this.startEditing(0, 1);
	},

	/**
	 * onDelete
	 */
	onDelete : function(btn, ev) {
		var index = this.getSelectionModel().getSelectedCell();
		if (!index) {
			return false;
		}
		var rec = this.store.getAt(index[0]);
		this.store.remove(rec);
	}
});
