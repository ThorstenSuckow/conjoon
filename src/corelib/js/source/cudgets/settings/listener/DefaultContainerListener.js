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

Ext.namespace('com.conjoon.cudgets.settings.listener');

/**
 * An  base class that provides the interface for listeners for
 * {com.conjoon.cudgets.settings.Container}
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.cudgets.settings.listener.DefaultContainerListener
 *
 * @constructor
 */
com.conjoon.cudgets.settings.listener.DefaultContainerListener = function() {

};

com.conjoon.cudgets.settings.listener.DefaultContainerListener.prototype = {

    /**
     * @type {com.conjoon.cudgets.settings.Container} settingsContainer The
     * The settings container this listener is bound to.
     */
    settingsContainer : null,

// -------- api

    /**
     * Installs the listeners for the elements found in the settings container.
     *
     * @param {com.conjoon.cudgets.settings.Container} settingsContainer The
     * settings dialog this listener is bound to.
     *
     * @packageprotected
     */
    init : function(settingsContainer)
    {
        if (this.settingsContainer) {
            return;
        }

        this.settingsContainer = settingsContainer;

        var ec = settingsContainer.getEntriesComponent();

        settingsContainer.mon(ec, 'beforeentryselect',   this.onBeforeEntrySelect, this);
        settingsContainer.mon(ec, 'entryselect',         this.onEntrySelect, this);
        settingsContainer.mon(ec, 'entrydeselect',       this.onEntryDeselect, this);

        settingsContainer.mon(
            settingsContainer.getRemoveEntryButton(),
            'click',
            this.onRemoveEntryButtonClick,
            this
        );

        settingsContainer.mon(
            settingsContainer.storeSync.store, 'update', this.onStoreUpdate, this
        );

        settingsContainer.mon(
            settingsContainer.storeSync, 'beforewrite', this.onBeforeWrite, this
        );

        settingsContainer.mon(
            settingsContainer.storeSync, 'write', this.onWrite, this
        );

        settingsContainer.mon(
            settingsContainer.storeSync, 'exception', this.onException, this
        );

    },

// -------- helper

    /**
     * Generates
     *
     */

    /**
     * Returns false if the passed record contains invalid fields.
     *
     * @param {Ext.data.Record} record
     *
     * @protected
     */
    isInvalidField : function(record)
    {
        var settingsContainer = this.settingsContainer;

        var info = settingsContainer.getInvalidFieldAndCard();

        if (info !== null) {
            settingsContainer.showErrorMessage(
                info.card.getErrorMessage(info.field),
                info.field.fieldLabel
                ? com.conjoon.Gettext.gettext("Error") + ": " + info.field.fieldLabel
                : undefined
            );

            settingsContainer.selectEntry(record, true);
            settingsContainer.formContainer.setActiveTab(info.card);

            return false;
        }

    },

    /**
     * Invokes the writeRecord() method from the various cards.
     *
     * @param {Ext.data.Record} record
     */
    saveEntry : function(record)
    {
        this.settingsContainer.saveFormDataToEntry(record);
    },

// ------- listeners

    // ------- listener for the selection events
    /**
     * Listener for the dialogs beforeentryselect-event
     *
     * @param {Ext.Component} component The compoennt that triggered this event
     * @param {Ext.data.Record} record The record that is about to get selected
     */
    onBeforeEntrySelect : function(component, record)
    {
        var settingsContainer = this.settingsContainer;

        // return if the currently selected entry equals to the entry that is about
        // to get selected
        var sel = settingsContainer.getSelectedEntry();

        if (!sel) {
            return;
        }

        if (record.id == sel.id) {
            return false;
        }

        if (this.isInvalidField(sel) ===  false) {
            return false;
        }

        this.saveEntry(sel);

    },

    /**
     * Listener for the dialogs entryselect-event
     *
     * @param {Ext.Component} component The compoennt that triggered this event
     * @param {Ext.data.Record} record The record that is selected
     */
    onEntrySelect : function(component, record)
    {
        var settingsContainer = this.settingsContainer;

        settingsContainer.removeEntryButton.setDisabled(false);
        settingsContainer.mainContainer.getLayout().setActiveItem(settingsContainer.formContainer);

        if (!settingsContainer.formContainer.getActiveTab()) {
            settingsContainer.formContainer.setActiveTab(0);
        }

        settingsContainer.setFormDataFromEntry(record);
    },

    /**
     * Listener for the dialogs entrydeselect-event
     *
     * @param {Ext.Component} component The compoennt that triggered this event
     * @param {Ext.data.Record} record The record that is about to get deselected
     */
    onEntryDeselect  : function(component, record)
    {
        var settingsContainer = this.settingsContainer;

        // check here if the record still is part of the storeSync's store,
        // which may not be the fact if deselect was called during the remove event
        // of the record
        if (record.store != null) {
            if (this.isInvalidField(record) === false) {
                return false;
            }

            this.saveEntry(record);
        }

        settingsContainer.removeEntryButton.setDisabled(true);
        settingsContainer.mainContainer.getLayout().setActiveItem(settingsContainer.introductionCard);
    },

    // ------- listener for the button events
    /**
     * Listener for the removeEntryButton click-event.
     *
     * @param {Ext.Button} button The button that triggered this event
     */
    onRemoveEntryButtonClick : function()
    {
        var settingsContainer = this.settingsContainer;

        var sel = settingsContainer.getSelectedEntry();

        if (!sel) {
            return;
        }

        settingsContainer.removeEntry(sel, settingsContainer.confirmBeforeRemove);
    },

    // ------- listener for storeSyncs store
    /**
     * Listener for the store's update event.
     * Will reset all form values when a record gets rejected.
     *
     * @param {Ext.data.Store} store
     * @param {Ext.data.Record} record
     * @param {String} operation
     */
    onStoreUpdate : function(store, record, operation)
    {
        if (operation ===  Ext.data.Record.REJECT) {
            if (this.settingsContainer.getSelectedEntry() === record) {
                this.settingsContainer.setFormDataFromEntry(record);
            }
        }
    },


    // ------- listener for server requests
    /**
     * This listener gets called before a request to the server is made
     * for updating data. When the write process is batched, i.e. an update
     * and a destroy request is batched into one request, this method is called twice.
     *
     * @param {Ext.data.DataProxy} dataProxy
     * @param {String} action
     * @param {String} records
     * @param {String} options
     * @param {String} arg
     */
    onBeforeWrite : function(dataProxy, action, records, options, arg)
    {
        this.settingsContainer.setServerRequestPending(true, action);
    },

    /**
     * Fires if the server returns 200 after an Ext.data.Api.actions CRUD action.
     * Success or failure of the action is available in the result['successProperty']
     * property. The server-code might set the successProperty to false if a database
     * validation failed, for example.
     * reinstalls the settingsContainer's cards "startedit" event listeners.
     *
     * @param {Ext.data.Store} store
     * @param {String} action
     * @param {Object} result
     * @param {Ext.Direct.Transaction} res,
     * @param {Ext.data.Record/Array} rs
     *
     */
    onWrite : function(store, action, result, res, rs)
    {

        if (result.success === false) {

            var settingsContainer = this.settingsContainer;

            // give errors presedence
            if (result.error) {
                com.conjoon.groupware.ResponseInspector.handleFailure(result.error);
            } else {

                var prop = action == Ext.data.Api.actions.destroy ? 'removed' : 'updated';

                var failed = result.failed;

                var accountNames = [];
                var storeSync    = settingsContainer.storeSync;
                var store        = storeSync.store;
                for (var i = 0, len = failed.length; i < len; i++) {
                    accountNames.push(store.getById(failed[i]).get(storeSync.dataIndex));
                }

                var message = "";
                var title   = "";

                if (prop == 'removed') {
                    message = String.format(
                        com.conjoon.Gettext.ngettext("Could not remove the following account: {0}", "Could not remove the following accounts: <br />{0}", len),
                        accountNames.join("<br />")
                    );

                    title = com.conjoon.Gettext.gettext("Error while removing Twitter accounts.");
                } else {
                    message = String.format(
                        com.conjoon.Gettext.ngettext("Could not update the following account: {0}", "Could not update the following accounts:<br /> {0}", len),
                        accountNames.join("<br />")
                    );

                    title = com.conjoon.Gettext.gettext("Error while updating Twitter accounts.");
                }

                settingsContainer.showErrorMessage(
                    message, title
                );
            }
        }

        var cards = this.settingsContainer.getFormCards();

        for (var i = 0, len = cards.length; i < len; i++) {
            cards[i].installStartEditListener(true);
        }

        this.settingsContainer.setServerRequestPending(false);
    },

    /**
     * Fires if an exception occurs in the Proxy during a remote request. This
     * event is relayed through a corresponding Ext.data.Store.exception, so
     * any Store instance may observe this event. This event can be fired for
     * one of two reasons:
     *    * remote-request failed :
     *      The server did not return status === 200.
     *     * remote-request succeeded :
     *       The remote-request succeeded but the reader could not read the response.
     *       This means the server returned data, but the configured Reader threw an
     *       error while reading the response. In this case, this event will be raised
     *       and the caught error will be passed along into this event.
     *
     * This event fires with two different contexts based upon the 2nd parameter type
     * [remote|response]. The first four parameters are identical between the two
     *  contexts -- only the final two parameters differ.
     * Listeners will be called with the following arguments:
     *
     *    * dataProxy : DataProxy
     *      The proxy that sent the request
     *    * type : String
     *      The value of this parameter will be either 'response' or 'remote'.
     *          o 'response' :
     *            An invalid response from the server was returned: either 404, 500
     *            or the response meta-data does not match that defined in the
     *            DataReader (e.g.: root, idProperty, successProperty).
     *          o 'remote' :
     *            A valid response was returned from the server having
     *            successProperty === false. This response might contain
     *            an error-message sent from the server. For example, the user may have
     *            failed authentication/authorization or a database validation error occurred.
     *    * action : String
     *      Name of the action (see Ext.data.Api.actions.
     *    * options : Object
     *      The options for the action that were specified in the request.
     *    * response : Object
     *      The value of this parameter depends on the value of the type parameter:
     *          o 'response' :
     *            The raw browser response object (e.g.: XMLHttpRequest)
     *          o 'remote' :
     *            The decoded response object sent from the server.
     *    * arg : Mixed
     *      The type and value of this parameter depends on the value of the type parameter:
     *          o 'response' : Error
     *            The JavaScript Error object caught if the configured Reader could not read
     *            the data. If the remote request returns success===false, this
     *            parameter will be null.
     *          o 'remote' : Record/Record[]
     *            This parameter will only exist if the action was a write action
     *            (Ext.data.Api.actions.create|update|destroy).
     *
     * @param {Ext.data.DataProxy} dataProxy
     * @param {String} type
     * @param {String} action
     * @param {Object} options
     * @param {Object} response
     * @param {Mixed} arg
     *
     */
    onException : function(dataProxy, type, action, options, response, arg)
    {
        com.conjoon.groupware.ResponseInspector.handleFailure(response);
        this.settingsContainer.setServerRequestPending(false);
    }



};