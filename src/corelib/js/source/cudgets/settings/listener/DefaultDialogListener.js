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

Ext.namespace('com.conjoon.cudgets.settings.listener');

/**
 * An  base class that provides the interface for listeners for
 * {com.conjoon.cudgets.settings.Dialog}
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.cudgets.settings.listener.DefaultDialogListener
 *
 * @constructor
 */
com.conjoon.cudgets.settings.listener.DefaultDialogListener = function() {

};

com.conjoon.cudgets.settings.listener.DefaultDialogListener.prototype = {

    /**
     * @type {com.conjoon.cudgets.settings.Dialog} dialog The dalog
     * this listener is bound to.
     */
    dialog : null,

    /**
     * @type {Boolean} closeAfterRequest Whether to close the dialog after a
     * request to the server has been made. Set in onOkClick and reset in the
     * various listeners.
     */
    closeAfterSave : false,

    /**
     * @type {com.conjoon.cudgets.direct.BatchedResponseHelper} batchedResponseHelper
     */
    batchedResponseHelper : com.conjoon.cudgets.direct.BatchedResponseHelper,

    /**
     * @type {String} clsId
     */
    clsId : '3e4a7fe4-fb06-4aa2-b0e5-40d824ba794a',


// -------- api

    /**
     * Installs the listeners for the elements found in the dialog.
     *
     * @param {com.conjoon.cudgets.settings.Dialog} dialog The settings dialog
     * this listener is bound to.
     *
     * @packageprotected
     */
    init : function(dialog)
    {
        if (this.dialog) {
            return;
        }

        this.batchedResponses = [];

        this.dialog           = dialog;
        var applyButton       = dialog.getApplyButton();
        var settingsContainer = dialog.getSettingsContainer();

        this.dialog.on('beforeclose', this.onBeforeClose, this);

        dialog.mon(dialog.getOkButton(),     'click',  this.onOkClick,     this);
        dialog.mon(dialog.getCancelButton(), 'click',  this.onCancelClick, this);
        dialog.mon(applyButton,              'click',  this.onApplyClick,  this);

        var cards       = settingsContainer.getFormCards();
        var onStartEdit = this.onStartEdit;
        for (var i = 0, len = cards.length; i < len; i++) {
            settingsContainer.mon(cards[i], 'startedit', onStartEdit, this);
        }

        settingsContainer.mon(
            settingsContainer.storeSync, 'remove' , this.onEntryRemove, this
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

// ------- listeners

    // ------- listener for teh dialogs events
    /**
     * Listener for the dialog's close event. Allows the dialog to be closed
     * only if no server request is currently pending.
     *
     * @param {Ext.Window} dialog The dialog that is about to be closed
     * @return {Boolean} true to close the dialog, otherwise false
     */
    onBeforeClose : function(dialog)
    {
        if (this.dialog.getSettingsContainer().isServerRequestPending()) {
            return false;
        }
    },

    // ------- listener for the settingsContainer's cards.
    /**
     * Listener for the settingsContainer's cards' startedit event.
     *
     * @param {com.conjoon.cudgets.settings.Card} card
     * @param {Ext.form.Field} field
     */
    onStartEdit : function(card, field)
    {
        this.dialog.getApplyButton().setDisabled(false);
    },

    // ------- listener for the settingsContainer's store events
    /**
     * Called when an entry was removed from the settingsContainer's temp store.
     *
     * @param {Ext.data.Store} store
     * @param {Ext.data.Record} record
     * @param {Number} index
     */
    onEntryRemove : function(store, record, index)
    {
        this.dialog.getApplyButton().setDisabled(false);
    },

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
        this.dialog.setControlsDisabled(true, true);
    },

    /**
     * Fires if the server returns 200 after an Ext.data.Api.actions CRUD action.
     * Success or failure of the action is available in the result['successProperty']
     * property. The server-code might set the successProperty to false if a database
     * validation failed, for example.
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
        if (this.closeAfterSave === true) {
           this.batchedResponseHelper.processIfResultValid({
                success : function() {
                    this.dialog.close();
                },
                failure : function() {
                    this.closeAfterSave = false;
                },
                scope   : this,
                result  : result.success,
                id      : this.clsId
            });
        }

        this.dialog.setControlsDisabled(false, true);
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
        this.closeAfterSave = false;
        this.batchedResponseHelper.clearConfigForId(this.clsId);

        this.dialog.setControlsDisabled(false, false);
    },

    // ------- listener for the buttons
    /**
     * Listener for the okButton click event.
     *
     * @param {Ext.Button} button The button that triggered the event.
     * @param {EventObject} e The EventObject associated with the click event
     */
    onOkClick : function(button, e)
    {
        this.closeAfterSave = true;
        if (!this.dialog.getSettingsContainer().saveConfiguration()) {
            this.dialog.close();
        }
    },

    /**
     * Listener for the cancelButton click event.
     *
     * @param {Ext.Button} button The button that triggered the event.
     * @param {EventObject} e The EventObject associated with the click event
     */
    onCancelClick : function(button, e)
    {
        this.dialog.close();
    },

    /**
     * Listener for the applyButton click event.
     *
     * @param {Ext.Button} button The button that triggered the event.
     * @param {EventObject} e The EventObject associated with the click event
     */
    onApplyClick : function(button, e)
    {
        this.dialog.getSettingsContainer().saveConfiguration();
    }
};