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
 * Card-layout' ContainerDialog to show introductionCard and TreePanel with additional
 * options.
 *
 *
 * @class com.conjoon.groupware.email.options.folderMapping.SettingsContainer
 * @extends Ext.Container
 */
com.conjoon.groupware.email.options.folderMapping.SettingsContainer = Ext.extend(Ext.Container, {

    /**
     * @type {com.conjoon.groupware.email.options.folderMapping.ui.DefaultSettingsContainerUi} ui
     */
    ui : null,

    /**
     * @type {Ext.BoxElement}
     * introductionCard
     */
    introductionCard : null,

    /**
     * @type {com.conjoon.groupware.email.options.folderMapping.OptionsContainer}
     * optionsContainer
     */
    optionsContainer : null,

    /**
     * Inits this component.
     */
    initComponent : function()
    {
        if (!this.ui) {
            this.ui = new com.conjoon.groupware.email.options.folderMapping
                          .ui.DefaultSettingsContainerUi();
        }

        this.ui.init(this);

        com.conjoon.groupware.email.options.folderMapping.SettingsContainer.superclass
        .initComponent.call(this);
    },

// -------- api

    /**
     * Shows the introduction card.
     *
     */
    showIntroductionCard : function()
    {
        this.ui.showIntroductionCard();
    },

    /**
     * Shows the option container.
     */
    showOptionsContainer : function()
    {
        this.ui.showOptionsContainer();
    },

    /**
     * Returns the introduction card for this dialog.
     *
     * @return {com.conjoon.groupware.email.options.folderMapping.IntroductionCard}
     */
    getIntroductionCard : function()
    {
        if (!this.introductionCard) {
            this.introductionCard = this.ui.buildIntroductionCard();
        }

        return this.introductionCard;
    },

    /**
     * Returns the options container.
     *
     * @return {com.conjoon.groupware.email.options.folderMapping.OptionsContainer}
     */
    getOptionsContainer : function()
    {
        if (!this.optionsContainer) {
            this.optionsContainer = this.ui.buildOptionsContainer();
        }

        return this.optionsContainer;
    }
});