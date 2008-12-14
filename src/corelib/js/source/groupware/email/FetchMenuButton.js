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

Ext.namespace('com.conjoon.groupware.email');

/**
 * A button for fetching latest emails given the configured accounts from
 * com.conjoon.groupware.email.AccountStore
 *
 * @class com.conjoon.groupware.email.FetchMenuButton
 */
com.conjoon.groupware.email.FetchMenuButton = Ext.extend(Ext.Toolbar.SplitButton, {


    _accountItemMap : null,

    _busy : false,

    initComponent : function ()
    {
        this._accountItemMap = {};

        Ext.apply(this, {
            text     : com.conjoon.Gettext.gettext("Get email"),
            cls      : 'x-btn-text-icon',
            iconCls  : 'com-conjoon-groupware-email-FetchMenuButton-icon',
            menu     : this._getMenu(),
            handler  : this._onRetrieveAllClick,
            scope    : this,
            disabled : true
        });

        var l = com.conjoon.groupware.email.Letterman;
        l.on('load',          this._onLettermanLoad, this);
        l.on('beforeload',    this._onLettermanBeforeLoad, this);
        l.on('loadexception', this._onLettermanLoadException, this);

        var store = com.conjoon.groupware.email.AccountStore.getInstance();

        store.on('load',   this._onAccountStoreAdd,    this);
        store.on('remove', this._onAccountStoreRemove, this);
        store.on('add',    this._onAccountStoreAdd,    this);
        store.on('update', this._onAccountStoreUpdate, this);

        this.on('render', function() {
            this.setDisabled((store.getCount() == 0));
            this._onAccountStoreAdd(store, store.getRange());
        }, this);

        com.conjoon.groupware.email.FetchMenuButton.superclass.initComponent.call(this);
    },

// -------- helpers

// -------- listeners

    _onAccountStoreUpdate : function(store, record, operation)
    {
        var id = record.id;
        if (operation === 'commit' && this._accountItemMap[id]) {
            var item = this._accountItemMap[id];
            item.setText(record.get('name'));
        }

    },


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

    _onAccountStoreAdd : function(store, records, options)
    {
        var len = records.length;

        if (len == 0) {
            return;
        }

        var letterman = com.conjoon.groupware.email.Letterman;
        var item, rec;
        for (var i = 0; i < len; i++) {
            if (this._accountItemMap[records[i].id]) {
                continue;
            }
            rec = records[i];
            item = new Ext.menu.Item({
                text    : rec.get('name'),
                handler : letterman.peekIntoInbox.createDelegate(letterman, [rec.id])
            });

            this._accountItemMap[records[i].id] = item;
            this.menu.add(item);
        }

        if (!this._busy) {
            this.setDisabled(false);
        }

    },

    _onRetrieveAllClick : function()
    {
         com.conjoon.groupware.email.Letterman.peekIntoInbox();
    },

    _onLettermanLoad : function()
    {
        this.busy = false;
        this.setDisabled(false);
        this.setIconClass('com-conjoon-groupware-email-FetchMenuButton-icon');
    },

    _onLettermanBeforeLoad : function()
    {
        this.busy = true;
        this.setDisabled(true);
        this.setIconClass('com-conjoon-groupware-email-FetchMenuButton-icon-loading');
    },

    _onLettermanLoadException : function()
    {
        this.busy = false;
        this.setDisabled(false);
        this.setIconClass('com-conjoon-groupware-email-FetchMenuButton-icon');
    },


// -------- builders

    _getMenu : function()
    {
        if (this.menu == null) {

            this.menu = new Ext.menu.Menu({
                items : [
                    new Ext.menu.Item({
                        text    : com.conjoon.Gettext.gettext("All accounts"),
                        handler : this._onRetrieveAllClick,
                        scope   : this
                    }),
                    new Ext.menu.Separator()
                ]
            });
        }

        return this.menu;
    }


});