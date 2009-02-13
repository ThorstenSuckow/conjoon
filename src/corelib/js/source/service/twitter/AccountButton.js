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

Ext.namespace('com.conjoon.service.twitter');

/**
 * A button with a menu item that shows all available accounts as queried by the
 * attached {com.conjoon.service.twitter.data.AccountStore} and a menuItem for
 * requesting cancellation/exiting the current context.
 * Available accounts are shown using {Ext.menu.CheckItem}s, and clicking such a
 * menu item will trigger the "checkchange" event, indicating that another account
 * was selected.
 * The exit/cancel menu item will trigger the "exitclick" event, if clicked.
 *
 * @class com.conjoon.service.twitter.data.TwitterUserStore
 * @extends Ext.data.Store
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.service.twitter.AccountButton = Ext.extend(Ext.Toolbar.Button, {

    /**
     * @cfg {com.conjoon.service.twitter.data.AccountStore} accountStore
     */

    /**
     * @type {Object} _accountItemMap Maps the account records with their
     * corresponding menu items. The index will be the id of the account record,
     * and the value references the rendered menu item.
     * @protected
     */
    _accountItemMap : null,

    /**
     * @type {Ext.menu.Item} _exitMenuItem
     * @protected
     */
    _exitMenuItem : null,

    /**
     * Inits this component.
     *
     */
    initComponent : function ()
    {
        this.addEvents(
            /**
             * @event checkchange
             * @param {com.conjoon.service.twitter.AccountButton} this
             * @param {Ext.menu.CheckItem} checkItem
             * @param {Boolean} checked
             */
            'checkchange',

            /**
             * @event exitclick
             * @param {com.conjoon.service.twitter.AccountButton} this
             * @param {Ext.menu.MenuItem} menuItem
             */
            'exitclick'
        );

        this._accountItemMap = {};

        Ext.apply(this, {
            iconCls  : 'com-conjoon-service-twitter-AccountButton-icon',
            menu     : this._getMenu(),
            disabled : true
        });

        var store = this.accountStore;

        store.on('beforeload', this._onAccountStoreBeforeLoad, this);

        store.on('load',   this._onAccountStoreAdd,    this);
        store.on('remove', this._onAccountStoreRemove, this);
        store.on('add',    this._onAccountStoreAdd,    this);
        store.on('update', this._onAccountStoreUpdate, this);

        this.on('render', function() {
            this.setDisabled((store.getCount() == 0));
            this._onAccountStoreAdd(store, store.getRange());
        }, this);

        com.conjoon.service.twitter.AccountButton.superclass.initComponent.call(this);
    },

// -------- public API

    /**
     * Resets the check status from the given account items.
     *
     * @param {Boolean} suppressEvent true to not fire any event for the
     * checkItem
     */
    resetAccountMenuItemStates : function(suppressEvent)
    {
        for (var i in this._accountItemMap) {
            this._accountItemMap[i].setChecked(false, suppressEvent);
        }
    },

    /**
     * Returns this button's menu exit menu item.
     *
     * @return {Ext.menu.Item}
     */
    getExitMenuItem : function()
    {
        if (!this._exitMenuItem) {
            this._exitMenuItem = this._getExitMenuItem();
        }

        return this._exitMenuItem;
    },

// -------- listeners

    /**
     * Listens to the beforeload event of the accountStore.
     * This implementation changes the icon of the button to a loading indicator.
     *
     * @param {Ext.data.Store} store The store that fired the beforeload event.
     * @param {Object} options The loading options that were specified
     *
     * @protected
     */
    _onAccountStoreBeforeLoad : function()
    {
        this.setIconClass('com-conjoon-service-twitter-AccountButton-icon-loading');
    },

    /**
     * Listens to the click event of the exit/cancel menu item.
     * This implementation will fire the "exitclick" event.
     *
     * @param {Ext.menu.Item} menuItem
     * @param {Ext.EventObject} eventObject
     *
     * @protected
     */
    _onExitItemClick : function(menuItem, eventObject)
    {
        for (var i in this._accountItemMap) {
            this._accountItemMap[i].setChecked(false, true);
        }

        this.fireEvent('exitclick', this, menuItem);
    },

    /**
     * Listens to the checkchange event of the menu items which represent
     * a TwitterAccount.
     *
     * @param {Ext.menu.CheckItem} checkItem The item that fired the
     * checkchange event.
     * @param {Boolean} checked Whether the item is checked.
     *
     * @protected
     */
    _onCheckChange : function(checkItem, checked)
    {
        this.fireEvent('checkchange', this, checkItem, checked);
    },


    /**
     * Listens to the accountStore's update event.
     * This implementation will change the corresponding menu item's
     * text if a "commit" was specified in "operation".
     *
     * @param {Ext.data.Store} store
     * @param {com.conjoon.service.twitter.data.AccountRecord} record
     * @param {String} operation
     *
     * @protected
     */
    _onAccountStoreUpdate : function(store, record, operation)
    {
        var id = record.id;
        if (operation === 'commit' && this._accountItemMap[id]) {
            var item = this._accountItemMap[id];
            item.setText(record.get('name'));
        }

    },

    /**
     * Listens to the accountStore's remove event.
     * This implementation will remove the corresponding
     * menu item from this store and render it disabled if there are
     * no more records in the store.
     *
     * @param {Ext.data.Store} store
     * @param {com.conjoon.service.twitter.data.AccountRecord} record
     * @param {String} operation
     *
     * @protected
     */
    _onAccountStoreRemove : function(store, record, index)
    {
        var id = record.id;
        if (this._accountItemMap[id]) {
            var item = this._accountItemMap[id];
            this.menu.remove(item);
            this._accountItemMap[id] = null;
            delete this._accountItemMap[id];
        }

        var a = 0;
        for (var i in this._accountItemMap) {
            a++;
        }

        if (a == 0) {
            this.setDisabled(true);
        }

    },

    /**
     * Listens to the accountStore's add event.
     * This implementation will add another menu item representing the
     * currently added record.
     *
     * @param {Ext.data.Store} store
     * @param {com.conjoon.service.twitter.data.AccountRecord} record
     * @param {String} operation
     *
     * @protected
     */
    _onAccountStoreAdd : function(store, records, options)
    {
        var len = records.length;

        this.setIconClass('com-conjoon-service-twitter-AccountButton-icon');
        if (len == 0) {
            return;
        }

        var item, rec;
        for (var i = 0; i < len; i++) {
            if (this._accountItemMap[records[i].id]) {
                continue;
            }
            rec = records[i];
            item = new Ext.menu.CheckItem({
                text    : rec.get('name'),
                group   : 'com-conjoon-service-twitter-AccountButton',
                listeners : {
                    checkchange       : this._onCheckChange,
                    scope             : this
                }
                //handler : letterman.peekIntoInbox.createDelegate(letterman, [rec.id])
            });

            this._accountItemMap[rec.id] = item;
            this.menu.insert(0, item);
        }


        this.setDisabled(false);

    },


// -------- builders

    /**
     * Returns the {Ext.menu.Menu} used for this button.
     * Overwrite this to return a custom implementation of a menu.
     *
     * @return {Ext.menu.Menu}
     *
     * @protected
     */
    _getMenu : function()
    {
        if (this.menu == null) {
            this.menu = new Ext.menu.Menu({
                items : [
                    new Ext.menu.Separator(),
                    this.getExitMenuItem()
                ]
            });
        }

        return this.menu;
    },

    /**
     * Returns the {Ext.menu.Item} used for this button's menu.
     * Overwrite this to return a custom implementation of a menu item.
     *
     * @return {Ext.menu.Item}
     *
     * @protected
     */
    _getExitMenuItem : function()
    {
        return new Ext.menu.Item({
            disabled : true,
            text     : com.conjoon.Gettext.gettext("Exit"),
            handler  : this._onExitItemClick,
            scope    : this
        });
    }


});