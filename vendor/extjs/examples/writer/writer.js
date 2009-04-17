/*
 * Ext JS Library 3.0 RC1
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */

// Create HttpProxy instance.  Notice new configuration parameter "api" here instead of load.  However, you can still use
// the "url" paramater -- All CRUD requests will be directed to your single url instead.
var proxy = new Ext.data.HttpProxy({
	prettyUrls: false,	// <-- prettyUrls for urls of the form /user/update/1 or /user/destroy/32
	api: {
		load : 'load.json',
		create : 'create.json',
		save: 'update.json',
		destroy: 'destroy.json'
	}
});

// Typical JsonReader.  Notice additional meta-data params for defining the core attributes of your json-response
var reader = new Ext.data.JsonReader({
	totalProperty: 'total',
	successProperty: 'success',
	idProperty: 'id',
	root: 'data'
}, [
	{name: 'id'},
	{name: 'email', allowBlank: false},
	{name: 'first', allowBlank: false},
	{name: 'last', allowBlank: false}
]);

// The new DataWriter component.
var writer = new Ext.data.JsonWriter({
	returnJson: true,
	writeAllFields: false
});

// Typical Store collecting the Proxy, Reader and Writer together.
var store = new Ext.data.Store({
	id: 'user',
	root: 'records',
	proxy: proxy,
	reader: reader,
	writer: writer,		// <-- plug a DataWriter into the store just as you would a Reader
	paramsAsHash: true,
	batchSave: false	// <-- true to delay executing create, update, destroy requests until specifically told to do so.
});

// Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var userColumns =  [
    {header: "ID", width: 40, sortable: true, dataIndex: 'id'},
    {header: "Email", width: 100, sortable: true, dataIndex: 'email', editor: new Ext.form.TextField({})},
    {header: "First", width: 50, sortable: true, dataIndex: 'first', editor: new Ext.form.TextField({})},
    {header: "Last", width: 50, sortable: true, dataIndex: 'last', editor: new Ext.form.TextField({})}
];

// load the store immeditately
store.load();

Ext.onReady(function() {
	Ext.QuickTips.init();

	// create user.Form instance (@see UserForm.js)
	var userForm = new App.user.Form({
		renderTo: 'user-form',
		listeners: {
			create : function(fpanel, data) {	// <-- custom "create" event defined in App.user.Form class
				var rec = new userGrid.store.recordType(data);
				userGrid.store.insert(0, rec);
			}
		}
	});

	// create user.Grid instance (@see UserGrid.js)
	var userGrid = new App.user.Grid({
		renderTo: 'user-grid',
		store: store,
		columns : userColumns,
		listeners: {
			rowclick: function(g, index, ev) {
				var rec = g.store.getAt(index);
				userForm.loadRecord(rec);
			},
			destroy : function() {
				userForm.getForm().reset();
			}
		}
	});
});