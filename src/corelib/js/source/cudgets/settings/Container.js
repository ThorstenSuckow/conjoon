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

Ext.namespace('com.conjoon.cudgets.settings');

/**
 * A base class that provides the skeleton for a container that holds components
 * for editing records.
 * An options container consists of a panel that shows the store's the container
 * is bound to (and which are about to get edited). Those nodes may have child nodes
 * which itself represent a set of extended options.
 *
 * The proposed ui layout is like this:
 *
 * +-----+-------------------------+
 * |     |  +-------------------+  |
 * |  1  |  |                   |  |
 * |     |  |                   |  |
 * |     |  |         3         |  |
 * +-----+  |                   |  |
 * |  2  |  |                   |  |
 * |     |  +-------------------+  |
 * +-----+-------------------------+
 * |                  4            |
 * +-------------------------------+
 *
 *
 * 1: The "data" panel
 * 2: Add/Remove/etc. buttons
 * 3: Card panel for showing options based on the selection in 1
 * 4: OK/Cancel/Apply button
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.cudgets.settings.Container
 * @extends Ext.Container
 * @constructor
 * @abstract
 */
com.conjoon.cudgets.settings.Container = Ext.extend(Ext.Container, {

    /**
     * @type {Ext.Button} addEntryButton
     */
    addEntryButton : null,

    /**
     * @type {Ext.Button} removeEntryButton
     */
    removeEntryButton : null,

    /**
     * @type {Ext.Component} entriesComponent A container that holds the entries
     * which are editable. This component must be of type com.conjoon.cudgets.ListView
     * or com.conjoon.cudgets.TreePanel.
     */
    entriesComponent : null,

    /**
     * @type {Boolean} serverRequestType if a server request is currently busy,
     * this var will hold either "update" for an update operation, "destroy" for
     * a "destroy" operation or "updateAndDestroy" for a batched "update"/"destroy"
     * operation. Equals to "null" If no request is currently pending.
     */
    serverRequestType : null,

    /**
     * @cfg {com.conjoon.cudgets.settings.ui.DefaultContainerUi} ui
     * The ui for this container. If not provided, defaults to
     * {com.conjoon.cudgets.settings.ui.DefaultContainerUi}
     */
    ui : null,

    /**
     * @cfg  {com.conjoon.cudgets.data.StoreSync} storeSync
     * The StoreSync that is responsible for merging changes between the
     * original store and the temporary store which is used to work on
     */
    storeSync : null,

    /**
     * @type {Ext.Component} introductionCard The card which is shown if currently
     * no entries in entriesComponent are selected.
     */
    introductionCard : null,

    /**
     * @type {Array} formCards An array with {Ext.FormPanel}s that provide form
     * fields for editing the currently selected record.
     */
    formCards : null,

    /**
     * @type {Ext.Container} formContainer The container that holds the forms.
     */
    formContainer : null,

    /**
     * @type {Ext.Container} mainContainer The container that holds the introduction card
     * and the formContainer.
     */
    mainContainer : null,

    /**
     * @cfg {Boolean} confirmBeforeRemove true to prompt the user before removing an entry,
     * otherwise false. Defaults to true
     */
    confirmBeforeRemove : true,


// -------- overrides

    /**
     * Inits this component.
     *
     */
    initComponent : function()
    {
        if (!this.ui) {
            this.ui = new com.conjoon.cudgets.settings.ui.DefaultContainerUi();
        }

        this.storeSync.init(this);

        this.ui.init(this);

        com.conjoon.cudgets.settings.Container.superclass.initComponent.call(this);
    },



// -------- api

    /**
     * Shows a confirm dialog with the specified message. If no message was specified,
     * a generic error message is shown.
     *
     * @param {String} message
     * @param {String} title
     * @param {String} options
     */
    showConfirmDialog : function(message, title, options)
    {
        this.ui.buildConfirmDialog(message, title, options);
    },

    /**
     * Shows a dialog with the specified message. If no message was specified,
     * a generic error message is shown.
     *
     * @param {String} message
     * @param {String} title
     */
    showErrorMessage : function(message, title)
    {
        this.ui.buildErrorMessage(message, title);
    },

    /**
     * Returns all records available in entriesComponent.
     *
     * @return {Array}
     */
    getAllEntries : function()
    {
        return this.storeSync.store.getRange();
    },

    /**
     * Selects the in entriesComponent.
     *
     * @param {Ext.data.Record} record
     * @param {Boolean} silent true to suspend all Events related to selecting
     * the record in entriesComponent
     */
    selectEntry : function(record, silent)
    {
        this.entriesComponent.selectEntry(record, silent);
    },

    /**
     * Removes an entry from the store.
     * If "confirm" is set to true, a confirm dialog is shown beforehand so the suer can
     * confirm whether to remove the record or not.
     *
     * @param {Ext.data.Record} record The record to remove
     * @param {Boolean} confirm Whether to show a confirm dialog before removing or not
     */
    removeEntry : function(record, confirm)
    {
        if (confirm) {
            this.ui.buildConfirmRemoveDialog(record, {
                fn : function(buttonString) {
                    if (buttonString == 'yes') {
                        this.removeEntry(record, false);
                    }
                },
                scope : this
            });

            return;
        }
        this.storeSync.remove(record);
    },


    /**
     * Returns the currently selected record from entriesComponent, or
     * null if there is currently no selection.
     *
     * @return {Ext.data.Record}
     */
    getSelectedEntry : function()
    {
        return this.entriesComponent.getSelectedEntry();
    },

    /**
     * Saves the configuration, if any changes where made. This will invoke
     * a request to the server, sending information about delected and updated
     * records.
     * This method will do nothing if the currently selected record (if any) could
     * not be saved due to errors.
     *
     * @return {Boolean} This method will return true if any outstanding changes
     * have to be send to the server, otherwise false.
     */
    saveConfiguration : function()
    {
        var rec = this.getSelectedEntry();

        if (rec) {
            var info = this.getInvalidFieldAndCard();

            if (info !== null) {
                this.showErrorMessage(
                    info.card.getErrorMessage(info.field),
                    info.field.fieldLabel
                    ? com.conjoon.Gettext.gettext("Error") + ": " + info.field.fieldLabel
                    : undefined
                );

                this.formContainer.setActiveTab(info.card);

                return;
            }

            this.saveFormDataToEntry(rec);
        }

        return this.storeSync.save();
    },

    /**
     * Returns an object with information about the first invalid field found.
     * This method returns null if there is currently no record selected or if
     * there was no invalid field found.
     *
     *
     * @return {Object} And object with the following properties:
     *  - field The invalid field
     *  - card  The card where the invalid field was found.
     * or null if all fields were valid or no record was currently selected
     */
    getInvalidFieldAndCard : function()
    {
        if (!this.getSelectedEntry()) {
            return null;
        }

        var cards = this.getFormCards();
        for (var i = 0, len = cards.length; i < len; i++) {
            field = cards[i].getInvalidField();
            if (field !== null) {
                return {
                    field : field,
                    card  : cards[i]
                };
            }
        }

        return null;
    },

    /**
     * Sets the configured cards' fields to the data of teh passed record.
     *
     * @param {Ext.data.Record} record
     */
    setFormDataFromEntry : function(record)
    {
        var cards = this.getFormCards();

        for (var i = 0, len = cards.length; i < len; i++) {
            cards[i].setRecord(record);
        }
    },

    /**
     * Writes the field data out of the form cards to the currently selected record.
     * If no record is currently selected, this method will do nothing.
     *
     */
    saveFormDataToSelectedEntry : function()
    {
        var rec = this.getSelectedEntry();

        if (!rec) {
            return;
        }

        this.saveFormDataToEntry(rec);
    },

    /**
     * Saves the form data to the passed record.
     * This method does not validate the form fields beforehand.
     *
     * @param {Ext.data.Record} record The record to rite the form data to.
     */
    saveFormDataToEntry : function(record)
    {
        if (!record) {
            throw(
                'Critical error: record passed to '
                +'com.conjoon.cudgets.settings.Container.saveFormDataToRecord() '
                +'was not defined.'
            );
        };

        var cards = this.getFormCards();

        for (var i = 0, len = cards.length; i < len; i++) {
            cards[i].writeRecord(record);
        }
    },

    /**
     * Returns true if there is currently a read/write process going on,
     * otherwise false.
     *
     * @return {Boolean}
     */
    isServerRequestPending : function()
    {
        return this.serverRequestType !== null;
    },

    /**
     * Notifies this container that the StoreSync is currently busy updating
     * data.
     * If "busy" is set to false, teh cards startedit listener will be installed
     * again, allowing for another cycle of listening to changes in the cards'
     * fields.
     *
     * @param {Boolean} busy
     * @param {String} action The action that invoked the pending state, i.e.
     * either 'update' or 'destroy'
     *
     */
    setServerRequestPending : function(busy, action)
    {
        if (!busy) {
            this.ui.hideLoadMask();
            this.serverRequestType = null;
            return;
        }

        var actions = Ext.data.Api.actions;

        switch (action) {
            case actions.update:
                if (this.serverRequestType == actions.destroy) {
                    this.serverRequestType = 'updateAndDestroy';
                } else {
                    this.serverRequestType = action;
                }
            break;

            case actions.destroy:
                if (this.serverRequestType == actions.update) {
                    this.serverRequestType = 'updateAndDestroy';
                } else {
                    this.serverRequestType = action;
                }
            break;

            default:
                throw(
                    'Unexpected action "'+action+'" in '
                    +'com.conjoon.cudgets.settings.Container.setServerRequestPending()'
                );
            break;
        }

        this.ui.showLoadMaskForWriteOperation(this.serverRequestType);
    },


// -------- component getters

    /**
     * Returns the main container for this container.
     * This container holds the introduction card and the formContainer.
     *
     * @return {Ext.Component}
     */
    getMainContainer : function()
    {
        if (!this.mainContainer) {
            this.mainContainer = this.ui.buildMainContainer();
        }

        return this.mainContainer;
    },

    /**
     * Returns the introduction Panel for this container.
     * This card will be shown if currently no entries in entriesComponent
     * are selected.
     *
     * @return {Ext.Component}
     */
    getIntroductionCard : function()
    {
        if (!this.introductionCard) {
            this.introductionCard = this.ui.buildIntroductionCard();
        }

        return this.introductionCard;
    },

    /**
     * Returns an array with all formPanels used to edit a currently selected
     * record.
     *
     * @return {Array}
     */
    getFormCards : function()
    {
        if (!this.formCards) {
            this.formCards = this.ui.buildFormCards();
        }

        return this.formCards;
    },

    /**
     * Returns the container that holds the form cards.
     *
     * @return {Array}
     */
    getFormContainer : function()
    {
        if (!this.formContainer) {
            this.formContainer = this.ui.buildFormContainer();
        }

        return this.formContainer;
    },

    /**
     * Returns the addEntryButton for this dialog.
     *
     * @return {Ext.Button}
     */
    getAddEntryButton : function()
    {
        if (!this.addEntryButton) {
            this.addEntryButton = this.ui.buildAddEntryButton();
        }

        return this.addEntryButton;
    },

    /**
     * Returns the removeEntryButton for this dialog.
     *
     * @return {Ext.Button}
     */
    getRemoveEntryButton : function()
    {
        if (!this.removeEntryButton) {
            this.removeEntryButton = this.ui.buildRemoveEntryButton();
        }

        return this.removeEntryButton;
    },

    /**
     * returns the {Ext.Component} that renders configurable entries into
     * the panel.
     *
     * @return {Ext.Component}
     *
     * @see buildEntriesComponent
     */
    getEntriesComponent : function()
    {
        if (!this.entriesComponent) {
            this.entriesComponent = this.ui.buildEntriesComponent();
        }

        return this.entriesComponent;
    },

    /**
     * Returns an array of additional buttons to work with selected entries
     * of entriesComponent. Defaults to the buttons "addEntryButton" and
     * "removeEntryButton". Return an empty array to return no buttons.
     *
     * @return {Ext.Component}
     *
     * @see buildEntriesComponent
     */
    getEntriesButtons : function()
    {
        var me = this,
            items = [];


        items = this.getAddEntryButton()
                ? items.concat(this.getAddEntryButton())
                : items;

        items = this.getRemoveEntryButton()
                ? items.concat(this.getRemoveEntryButton())
                : items;

        return items;
    }




});
