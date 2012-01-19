/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
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
        this.mon(l, 'load',       this._onLettermanLoad, this);
        this.mon(l, 'beforeload', this._onLettermanBeforeLoad, this);
        this.mon(l, 'exception',  this._onLettermanLoadException, this);

        var store = com.conjoon.groupware.email.AccountStore.getInstance();

        this.mon(store, 'load',   this._onAccountStoreAdd,    this);
        this.mon(store, 'remove', this._onAccountStoreRemove, this);
        this.mon(store, 'add',    this._onAccountStoreAdd,    this);
        this.mon(store, 'update', this._onAccountStoreUpdate, this);

        this.on('render', function() {
            this.setDisabled((store.getCount() == 0));
            this._onAccountStoreAdd(store, store.getRange());
        }, this, {single : true});

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
    _onLettermanLoadException : function(proxy, type, action, options, response, arg)
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