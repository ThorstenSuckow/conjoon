/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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

Ext.namespace('com.conjoon.groupware.email.options.folderMapping');

/**
 * Dialog shows the user a ListView with names of the current IMAP
 * accounts configured and a TreePanel, where he can map a folder to a
 * specific type.
 *
 *
 * @class com.conjoon.groupware.email.options.folderMapping.Dialog
 * @extends Ext.Window
 */
com.conjoon.groupware.email.options.folderMapping.Dialog = Ext.extend(Ext.Window, {

    /**
     * @cfg {Number} accountId The account id for which a mapping whould be made.
     * If provided, the entry in the ListView with this id will be pre-selected.
     */

    /**
     * @cfg {String} type The type of mapping that should be made. If available
     * and if an accountId was specified, this type will be preselected in the
     * "type" field.
     */

    /**
     * @type {Ext.Button} _okButton Will be available after the initComponent
     * method was called.
     */
    _okButton : null,

    /**
     * @type {Ext.Button} _cancelButton Will be available after the initComponent
     * method was called.
     */
    _cancelButton : null,

    /**
     * @type {Ext.Button} _applyButton Will be available after the initComponent
     * method was called.
     */
    _applyButton : null,

    /**
     * @type {com.conjoon.cudgets.ListView} _listView The listView for the
     * available accounts. Will be available after the initComponent method
     * was called.
     */
    _listView : null,

    /**
     * Inits this component.
     */
    initComponent : function()
    {
        Ext.apply(this, {
            title     : com.conjoon.Gettext.gettext("Folder Mappings"),
            width     : 500,
            height    : 400,
            modal     : true,
            layout    : 'border',
            cls       : 'com-conjoon-groupware-email-options-folderMapping-dialog',
            iconCls   : 'dialogIcon',
            resizable : false,
            items     : this._getItems(),
            buttons   : [
                this._getOkButton(),
                this._getCancelButton(),
                this._getApplyButton()
            ]
        });

        com.conjoon.groupware.email.options.folderMapping.Dialog.superclass
        .initComponent.call(this);
    },

// -------- api


// -------- builders

    /**
     * Returns an array with the components for this dialog as required in
     * "initComponent()". Override for custom implementation.
     *
     * @return {Array}
     *
     * @protected
     */
    _getItems : function()
    {
        return [
            this._getListView(),
            new Ext.Container({
                region : 'center'
            })
        ];
    },

    /**
     * Returns the listView to show the available accounts.
     * Override for custom implementation.
     *
     * @return {com.conjoon.cudgets.ListView}
     *
     * @protected
     */
    _getListView : function()
    {
        var store = new Ext.data.Store();

        var recs = com.conjoon.groupware.email.AccountStore.getInstance().getRange();

        var rec = null;
        for (var i = 0, len = recs.length; i < len; i++) {
            rec = recs[i];
            if (rec.get('protocol').toLowerCase() == 'imap') {
                store.add(rec.copy());
            }
        }

        return new com.conjoon.cudgets.ListView({
            region       : 'west',
            width        : 200,
            cls          : 'listView',
            margins      : '5 5 5 5',
            store        : store,
            multiSelect  : false,
            singleSelect : true,
            emptyText    : com.conjoon.Gettext.gettext("No IMAP accounts available"),
            hideHeaders  : true,
            columns      : [{
                dataIndex : 'name'
            }]
        });
    },

    /**
     * Returns an instance of {Ext.Button} This method is to be called once and may
     * be overriden in subclasses to return custom implementations.
     *
     * @return {Ext.Button}
     *
     * @protected
     */
    _getOkButton : function()
    {
        return new Ext.Button({
            text : com.conjoon.Gettext.gettext("OK")
        });
    },

    /**
     * Returns an instance of {Ext.Button} This method is to be called once and may
     * be overriden in subclasses to return custom implementations.
     *
     * @return {Ext.Button}
     *
     * @protected
     */
    _getCancelButton : function()
    {
        return new Ext.Button({
            text : com.conjoon.Gettext.gettext("Cancel")
        });
    },

    /**
     * Returns an instance of {Ext.Button} This method is to be called once and may
     * be overriden in subclasses to return custom implementations.
     *
     * @return {Ext.Button}
     *
     * @protected
     */
    _getApplyButton : function()
    {
        return new Ext.Button({
            text : com.conjoon.Gettext.gettext("Apply")
        });
    }

});