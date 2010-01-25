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
 * OptionsContainer to hold form elemnts and tree panel .
 *
 *
 * @class com.conjoon.groupware.email.options.folderMapping.OptionsContainer
 * @extends Ext.Container
 */
com.conjoon.groupware.email.options.folderMapping.OptionsContainer = Ext.extend(Ext.Container, {

    /**
     * @type {com.conjoon.groupware.email.options.folderMapping.ui.DefaultOptionsContainerUi} ui
     */
    ui : null,

    /**
     * @type {com.conjoon.groupware.email.options.folderMapping.TreePanel}
     * treePanel
     */
    treePanel : null,

    /**
     * @type {com.conjoon.groupware.util.FormIntro} formIntro The form intro for this
     * container
     */
    formIntro : null,

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
                          .ui.DefaultOptionsContainerUi();
        }

        this.ui.init(this);

        com.conjoon.groupware.email.options.folderMapping.OptionsContainer.superclass
        .initComponent.call(this);
    },

// -------- api

    /**
     * Returns the form intro for this conntainer.
     *
     * @return {com.conjoon.groupware.util.FormIntro}
     */
    getFormIntro : function()
    {
        if (!this.formIntro) {
            this.formIntro = this.ui.buildFormIntro();
        }

        return this.formIntro;
    }
});