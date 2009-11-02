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

Ext.namespace('com.conjoon.cudgets.data');

/**
 * An base class that provides functionality for encapsulating a store for making
 * changes to its entries. Changes will not be made directly to this store, but instead
 * to a temporarily created store that mirrors the entries. Changes will be merged
 * once syncData() has been called.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.cudgets.dialog.settings.SyncStore
 *
 * @param {Ext.data.Store} store The store which data should be mirrored to work on.
 * @constructor
 */
com.conjoon.cudgets.data.StoreSync = function(config) {
    Ext.apply(this, config);


    /**
     * @event beforewrite
     * Fires before the store sends the requested changes to the server
     * fo processing
     */




    com.conjoon.cudgets.data.StoreSync.superclass.constructor.call(this);
};

Ext.extend(com.conjoon.cudgets.data.StoreSync, Ext.util.Observable, {
    /**
     * @cfg {String} dataIndex The name of the field that should be used to visually
     * represent the entries in the store (such as a name field)
     */
    /**
     * @cfg {Ext.data.Store} orgStore The store to mirror.
     */

    /**
     * @cfg {Object} api An Ext.Direct-API configuration with the following properties:
     * update, destroy
     */

    /**
     * @type {Ext.data.Store} store The temp store that will be created to mirror
     * the data in orgStore.
     */
    store : null,

    /**
     * @type {Array} deletedRecords An array with records that were removed from store.
     */
    deletedRecords : null,


// -------- api

    /**
     * Inits the store with the data from orgStore.
     *
     * @param {Ext.Component} component The component the StoreSync is bound to,
     * if any.
     *
     * @packageprotected
     */
    init : function(component)
    {
        this.deletedRecords = [];

        var idProperty = this.orgStore.reader.meta.id
                         ? this.orgStore.reader.meta.id
                         : this.orgStore.reader.meta.idproperty;

        var rootProperty = this.orgStore.reader.meta.root
                         ? this.orgStore.reader.meta.root
                         : this.orgStore.reader.meta.rootProperty;

        var successProperty = this.orgStore.reader.meta.successProperty;

        if (!successProperty) {
            throw(
                'No "successProperty" defined for orgStore in '
                +'com.conjoon.cudgets.data.StoreSync.init()'
            );
        }

        var recordType = this.orgStore.reader.recordType;

        this.store = new com.conjoon.cudgets.data.Store({
            autoDestroy          : true,
            pruneModifiedRecords : true,
            storeId              : Ext.id(),
            autoSave             : false,
            autoLoad             : false,
            proxy                : new com.conjoon.cudgets.data.DirectProxy({
                api : this.api
            }),
            writer : new Ext.data.JsonWriter({
                encode  : false,
                listful : false
            }),
            reader : new com.conjoon.cudgets.data.JsonReader({
                 id              : idProperty,
                 root            : rootProperty,
                 successProperty : successProperty
            }, recordType)
        });

        this.relayEvents(this.store, ['remove', 'beforewrite', 'exception', 'write']);


        var records = this.orgStore.getRange();
        for (var i = 0, len = records.length; i < len; i++) {
            this.store.add(records[i].copy());
        }

        this.orgStore.on('add', this.onOrgStoreAdd, this);

        if (component) {
            component.on('destroy', this.destroy, this);
        }

    },

    /**
     * Removes the specified record from the temp store and updates the
     * deletedRecords property with the record.
     * Additionally, the record gets unbinded from the temp store and all its
     * changes since the last commit are rejected.
     *
     *
     * @param {Ext.data.Record} record The record to remove
     */
    remove : function(record)
    {
        if (this.store.indexOf(record) > -1) {
            record.join(null);
            record.reject(true);
            this.store.remove(record);
            this.deletedRecords.push(record);
        }
    },

    /**
     * Saves all changes made to the records in the store given the api configuration.
     * Changes will only be committed to the org store if no error occured and all
     * data manipulation was successfull.
     *
     */
    save : function()
    {
        this.store.save();
    },

    /**
     *
     */
    destroy : function()
    {
        this.orgStore.un('add', this.onOrgStoreAdd, this);
    },

    /**
     * Callback for the orgStore's add event. Will add a copy of the newly added
     * record to the store.
     *
     * @param {Ext.dataStore} store
     * @param {Array|Ext.data.Record} records
     * @param {Number} index
     */
    onOrgStoreAdd : function(store, records, index)
    {
        for (var i = 0, len = records.length; i < len; i++) {
            this.store.add(records[i].copy());
        }
    }

});