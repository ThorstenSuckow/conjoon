/**
 * conjoon
 * (c) 2007-2015 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */

Ext.namespace('com.conjoon.cudgets.settings.ui');

/**
 * Builds and layouts the SettingsContainer's layout and its components.
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.cudgets.settings.ui.DefaultContainerUi
 */
com.conjoon.cudgets.settings.ui.DefaultContainerUi = function(config) {

    config = config || {};

    Ext.apply(this, config);
};

com.conjoon.cudgets.settings.ui.DefaultContainerUi.prototype = {

    /**
     * @cfg {com.conjoon.cudgets.settings.listener.DefaultContainerListener} actionListener
     * The actionListener for the container this ui class manages. If not provided,
     * defaults to {com.conjoon.cudgets.settings.listener.DefaultContainerListener}
     */
    actionListener : null,

    /**
     * @cfg {Array} additionalMessageValues
     * An array of additional strings that are used for formatting the removeMsg
     */
    additionalMessageValues : null,

    /**
     * @cfg {String} removeMsg The message to show if confirmBeforeRemove is set to true and the user
     * must confirm removing an entry.
     */
    removeMsg : com.conjoon.Gettext.gettext("Do you really want to remove the entry \"{0}\"?"),

    /**
     * @cfg {String} removeTitle The title for the dialog if confirmBeforeRemove is set to true and the user
     * must confirm removing an entry.
     */
    removeTitle : com.conjoon.Gettext.gettext("Confirm deleting \"{0}\""),

    /**
     * @cfg {String} errorMsg The message to show if not all fields validated and no custom message was submitted
     * to showErrorMessage.
     */
    errorMsg : com.conjoon.Gettext.gettext("Please fill in all form fields."),

    /**
     * @cfg {String} errorTitle The default title for the error dialog.
     */
    errorTitle : com.conjoon.Gettext.gettext("Error"),

    /**
     * @cfg {Number} entryContainerHeight The height of the entryContainer which holds the
     * list of records which can be edited. Defaults to 240.
     */
    entryContainerHeight : 240,

    /**
     * @cfg {String} updateMsg The default title to show in a load mask when an
     * update request is invoked.
     */
    updateMsg : com.conjoon.Gettext.gettext("Updating..."),

    /**
     * @cfg {String} destroyMsg The default title to show in a load mask when a
     * destroy request is invoked.
     */
    destroyMsg : com.conjoon.Gettext.gettext("Removing..."),

    /**
     * @sfg {String} emptyText The default text to show in the entries list
     * if there is no data available. Defaults to "no data".
     */
    emptyText : 'no data',

    /**
     * @cfg {String} updateAndDestroyMsg The default title to show in a load mask when
     * a batched request is invoked, i.e. a request that updates and destroys records.
     * destroy request is invoked.
     */
    updateAndDestroyMsg : com.conjoon.Gettext.gettext("Updating/Removing..."),

    /**
     * @type {Ext.Container} container The container this ui class manages. Gets assigned in the init()
     * method.
     */
    container : null,

    /**
     * Inits the layout of the container.
     * gets called from the initComponent's "initComponent()" method.
     *
     * @param {Ext.Container} container The container this ui will manage.
     */
    init : function(container)
    {
        if (this.container) {
            return;
        }

        this.container = container;

        this.buildContainer();
        this.installListeners();
    },

    /**
     *
     * @protected
     */
    installListeners : function()
    {
        if (!this.actionListener) {
            this.actionListener = new com.conjoon.cudgets.settings.listener.DefaultContainerListener();
        }

        this.actionListener.init(this.container);
    },

    /**
     *
     * @protected
     */
    buildContainer : function()
    {
        var westItems = [
            this.container.getEntriesComponent()
        ];

        westItems = westItems.concat(this.container.getEntriesButtons());

        Ext.apply(this.container, {
            layout : 'border',
            cls    : 'com-conjoon-cudgets-SettingsContainer',
            items  : [
                new Ext.Container({
                    region    : 'west',
                    cls       : 'entriesPanel',
                    width     : 150,
                    border    : false,
                    margins   :'5 5 5 5',
                    items     : westItems
                }),
                this.container.getMainContainer()
            ]
        });
    },

    /**
     * returns the container for the introduction card and the forms.
     *
     * @return {Ext.Container}
     */
    buildMainContainer : function()
    {
        return new Ext.Container({
            region         : 'center',
            margins        :'5 5 5 0',
            layout         : 'card',
            activeItem     : 0,
            items          : [
                this.container.getIntroductionCard(),
                this.container.getFormContainer()
            ]
        });

    },

    /**
     * Builds the container that holds the settingsContainer's
     * introduction and form cards.
     *
     * @return {Ext.Container}
     */
    buildFormContainer : function()
    {
        return new Ext.TabPanel({
            deferredRender : false,
            cls            : 'formContainer',
            // do not set the active item, otherwise the "monitorValid"
            // property of the attached settingCards will invoke
            // validating the formFields immediately
            //activeItem     : 0,
            items          : this.container.getFormCards()
        });
    },

    /**
     * Builds the component that is responsible for showing configurable
     * entries.
     * The default implementation will return a {com.conjoon.cudgets.ListView} component.
     *
     * Any component that gets returned using this builder is advised to translate
     * beforeselect, deselect and select events to the appropriate "beforeentryselect",
     * "entrydeselect" and "entryselect".
     * For an override, see {com.conjoon.cudgets.ListView}, which implements all needed
     * events and API methods.     *
     *
     * Override for custom implementation.
     *
     * @protected
     */
    buildEntriesComponent : function()
    {
        return new com.conjoon.cudgets.ListView({
            store        : this.container.storeSync.store,
            multiSelect  : false,
            singleSelect : true,
            cls          : 'listView',
            height       : this.entryContainerHeight,
            emptyText    : this.emptyText,
            hideHeaders  : true,
            columns: [{
                dataIndex : this.container.storeSync.dataIndex
            }]
        });
    },

    /**
     * Builds the default addEntryButton.
     *
     * Override for custom implementation.
     *
     * @protected
     */
    buildAddEntryButton : function()
    {
        return new Ext.Button({
            text     : com.conjoon.Gettext.gettext("Add..."),
            minWidth : 150,
            cls      : 'com-conjoon-margin-b-5'
        });
    },

    /**
     * Builds the default removeEntryButton.
     *
     * Override for custom implementation.
     *
     * @protected
     */
    buildRemoveEntryButton : function()
    {
        return new Ext.Button({
            text     : com.conjoon.Gettext.gettext("Remove..."),
            minWidth : 150,
            disabled : true
        });
    },

    /**
     * Returns the card that is shown when no record is currently being
     * selected.
     *
     * @return {Ext.Component}
     */
    buildIntroductionCard : function()
    {
        return new Ext.BoxComponent({
            autoEl : {
                tag  : 'div',
                html : 'No entry selected'
            }
        });
    },

    /**
     * Returns an array with {com.conjoon.cudgets.settings.Card}s used to edit the
     * currently selected record.
     *
     * @return {Array}
     */
    buildFormCards : function()
    {
        return [
            new com.conjoon.cudgets.settings.Card({
                title : "Form 1"
            }),
            new com.conjoon.cudgets.settings.Card({
                title : "Form 2"
            }),
        ]
    },

// -------- dialogs

    /**
     * Builds and shows the dialog to show an error message.
     *
     * @param {String} message
     * @param {String} title
     */
    buildErrorMessage : function(message, title)
    {
        if (!message) {
            message = this.errorMsg;
        }

        if (!title) {
            title = this.errorTitle;
        }

        com.conjoon.SystemMessageManager.error(new com.conjoon.SystemMessage({
            title : title,
            text  : message,
            type  : com.conjoon.SystemMessage.TYPE_ERROR
        }));
    },

    /**
     * Builds and shows the dialog to show an error message.
     *
     * @param {Ext.data.Record} record
     * @param {Object} options
     */
    buildConfirmRemoveDialog : function(record, options)
    {
        var container = this.container;

        var value = record.get(container.storeSync.dataIndex);

        container.showConfirmDialog(
            String.format.apply(String.prototype,[this.removeMsg, value].concat(
                (this.additionalMessageValues ? this.additionalMessageValues : []))
            ),
            String.format(this.removeTitle, value),
            options
        );
    },

    /**
     * Builds a confirm dialog.
     *
     * @param {String} message
     * @param {String} title
     * @param {Object} options
     */
    buildConfirmDialog : function(message, title, options)
    {
        com.conjoon.SystemMessageManager.confirm(new com.conjoon.SystemMessage({
            title : title,
            text  : message,
            type  : com.conjoon.SystemMessage.TYPE_CONFIRM
        }), options);
    },

// -------- masks

    /**
     * Hides any loadmask that was created for this container.
     *
     */
    hideLoadMask : function()
    {
        this.container.el.unmask();
    },

    /**
     * Visually represents a pending server request for a update operation.
     *
     * @param {Boolean} show Whether to sho or hide the loadmask
     * @param {String} action The type of the write operation the load mask
     * should visually represent, either 'update', 'destroy' or 'updateAndDestroy'
     */
    showLoadMaskForWriteOperation : function(type)
    {
        var msg = this.updateMsg;

        if (type == 'destroy') {
            msg = this.destroyMsg;
        } else if (type == 'updateAndDestroy') {
            msg = this.updateAndDestroyMsg;
        }

        this.container.el.mask(msg, 'x-mask-loading');
    }

};