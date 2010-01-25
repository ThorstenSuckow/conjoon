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
 * Builds and layouts the FolderMapping dialog's settings container.
 *
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.groupware.email.options.folderMapping.ui
 */
com.conjoon.groupware.email.options.folderMapping.ui.DefaultSettingsContainerUi = function(config) {

    config = config || {};

    Ext.apply(this, config);
};

com.conjoon.groupware.email.options.folderMapping.ui.DefaultSettingsContainerUi.prototype = {

    /**
     * @cfg {com.conjoon.groupware.email.options.folderMapping.listener.DefaultSettingsContainerListener}
     * actionListener
     * The actionListener for the dialog this ui class manages. If not provided,
     * defaults to {com.conjoon.groupware.email.options.folderMapping.listener.DefaultSettingsContainerListener}
     */
    actionListener : null,

    /**
     * @type {com.conjoon.groupware.email.options.folderMapping.SettingsContainer} container
     * The container this ui class manages. Gets assigned in the init() method.
     */
    container : null,

    /**
     * Inits the layout of the container.
     * gets called from the initComponent's "initComponent()" method.
     *
     * @param {com.conjoon.groupware.email.options.folderMapping.SettingsContainer} container
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
                                      .DefaultSettingsContainerListener();
        }

        this.actionListener.init(this.container);
    },

// -------- api

    /**
     * Sets the active item of the container's layout to
     * the introduction card.     *
     */
    showIntroductionCard : function()
    {
        var container = this.container;

        container.getLayout().setActiveItem(
            container.getIntroductionCard()
        );
    },

    /**
     * Sets the active item of the container's layout to
     * the options container.
     */
    showOptionsContainer : function()
    {
        var container = this.container;

        container.getLayout().setActiveItem(
            container.getOptionsContainer()
        );
    },

// -------- builders

    /**
     * Layouts this ui's "container".
     *
     * @protected
     */
    buildContainer : function()
    {
        Ext.apply(this.container, {
            layout     : 'card',
            activeItem : 0,
            items      : [
                this.container.getIntroductionCard(),
                this.container.getOptionsContainer()
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
    buildOptionsContainer : function()
    {
        return new com.conjoon.groupware.email.options
                   .folderMapping.OptionsContainer();
    },

    /**
     * Builds and returns the "introductionCard" for this container.
     *
     * @return {Box.Element}
     */
    buildIntroductionCard : function()
    {
        return new Ext.BoxComponent({
            autoEl : {
                tag  : 'div',
                cls  : 'introCard',
                cn   : [{
                    tag  : 'div',
                    html : com.conjoon.Gettext.gettext("IMAP Folder Mappings"),
                    cls  : 'headerLabel'
                },{
                    tag  : 'div',
                    cls  : 'com-conjoon-margin-t-5',
                    html : com.conjoon.Gettext.gettext("Please choose from the list of existing IMAP accounts to map folder types to existing folders.")
                }]
            }
        });
    }

};