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

Ext.namespace('com.conjoon.groupware.email.options.folderMapping.ui');

/**
 * Builds and layouts the FolderMapping dialog's options container.
 *
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.groupware.email.options.folderMapping.ui.DefaultOptionsContainerUi
 */
com.conjoon.groupware.email.options.folderMapping.ui.DefaultOptionsContainerUi = function(config) {

    config = config || {};

    Ext.apply(this, config);
};

com.conjoon.groupware.email.options.folderMapping.ui.DefaultOptionsContainerUi.prototype = {

    /**
     * @cfg {com.conjoon.groupware.email.options.folderMapping.listener.DefaultOptionsContainerListener}
     * actionListener
     * The actionListener for the dialog this ui class manages. If not provided,
     * defaults to {com.conjoon.groupware.email.options.folderMapping.listener.DefaultOptionsContainerListener}
     */
    actionListener : null,

    /**
     * @type {com.conjoon.groupware.email.options.folderMapping.OptionsContainer} container
     * The container this ui class manages. Gets assigned in the init() method.
     */
    container : null,

    /**
     * Inits the layout of the container.
     * gets called from the initComponent's "initComponent()" method.
     *
     * @param {com.conjoon.groupware.email.options.folderMapping.OptionsContainer} container
     * The container this ui will manage.
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
            this.actionListener = new com.conjoon.groupware.email.options
                                      .folderMapping.listener
                                      .DefaultOptionsContainerListener();
        }

        this.actionListener.init(this.container);
    },

// -------- api



// -------- builders

    /**
     * Layouts this ui's "container".
     *
     * @protected
     */
    buildContainer : function()
    {
        Ext.apply(this.container, {
            layout     : 'border',
            cls        : 'optionsContainer',
            items      : [
                this.container.getFormIntro(),
                new Ext.Container({region : 'center'})
            ]
        });
    },

    /**
     * Builds and returns the options container.
     *
     * @return {com.conjoon.groupware.email.options.folderMapping.OptionsContainer}
     *
     * @protected
     */
    buildFormIntro : function()
    {
        return new com.conjoon.groupware.util.FormIntro({
            margins   : '5 5 5 5',
            height    : 50,
            region    : 'north',
            labelText : com.conjoon.Gettext.gettext("IMAP Folder Mappings"),
            text      : com.conjoon.Gettext.gettext("Choose a folder from the available tree-nodes and map it to any type. You can also add new nodes to the tree.")
        });
    }

};