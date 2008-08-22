/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: AccountRecord.js 2 2008-06-21 10:38:49Z T. Suckow $
 * $Date: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $Revision: 2 $
 * $LastChangedDate: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/js/source/groupware/email/AccountRecord.js $
 */

Ext.namespace('de.intrabuild.groupware.email');

/**
 * A button for fetching latest emails given the configured accounts from
 * de.intrabuild.groupware.email.AccountStore
 *
 * @class de.intrabuild.groupware.email.FetchMenuButton
 */
de.intrabuild.groupware.email.FetchMenuButton = Ext.extend(Ext.Toolbar.SplitButton, {


    _accountItemMap : null,

    _busy : false,

    initComponent : function ()
    {
        this._accountItemMap = {};

        Ext.apply(this, {
            text     : de.intrabuild.Gettext.gettext("Retrieve"),
            cls      : 'x-btn-text-icon',
            iconCls  : 'de-intrabuild-groupware-email-FetchMenuButton-icon',
            menu     : this._getMenu(),
            handler  : this._onRetrieveAllClick,
            scope    : this,
            disabled : true
        });

        var l = de.intrabuild.groupware.email.Letterman;
        l.on('load',          this._onLettermanLoad, this);
        l.on('beforeload',    this._onLettermanBeforeLoad, this);
        l.on('loadexception', this._onLettermanLoadException, this);

        var store = de.intrabuild.groupware.email.AccountStore.getInstance();

        store.on('load',   this._onAccountStoreAdd,    this);
        store.on('remove', this._onAccountStoreRemove, this);
        store.on('add',    this._onAccountStoreAdd,    this);
        store.on('update', this._onAccountStoreUpdate, this);

        de.intrabuild.groupware.email.FetchMenuButton.superclass.initComponent.call(this);
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

        var letterman = de.intrabuild.groupware.email.Letterman;
        var item, rec;
        for (var i = 0; i < len; i++) {
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
         de.intrabuild.groupware.email.Letterman.peekIntoInbox();
    },

    _onLettermanLoad : function()
    {
        this.busy = false;
        this.setDisabled(false);
        this.setIconClass('de-intrabuild-groupware-email-FetchMenuButton-icon');
        this.setText(de.intrabuild.Gettext.gettext("Retrieve"));
    },

    _onLettermanBeforeLoad : function()
    {
        this.busy = true;
        this.setDisabled(true);
        this.setIconClass('de-intrabuild-groupware-email-FetchMenuButton-icon-loading');
        this.setText(de.intrabuild.Gettext.gettext("Retrieving..."));
    },

    _onLettermanLoadException : function()
    {
        this.busy = false;
        this.setDisabled(false);
        this.setIconClass('de-intrabuild-groupware-email-FetchMenuButton-icon');
        this.setText(de.intrabuild.Gettext.gettext("Retrieve"));
    },


// -------- builders

    _getMenu : function()
    {
        if (this.menu == null) {

            this.menu = new Ext.menu.Menu({
                items : [
                    new Ext.menu.Item({
                        text    : de.intrabuild.Gettext.gettext("Retrieve all"),
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