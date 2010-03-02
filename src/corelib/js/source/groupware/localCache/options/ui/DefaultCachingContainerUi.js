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


Ext.namespace('com.conjoon.groupware.localCache.options.ui');

/**
 * Layouts the cachingContainer for the Local Cache Options Dialog.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.groupware.localCache.options.ui.DefaultCachingContainerUi
 */
com.conjoon.groupware.localCache.options.ui.DefaultCachingContainerUi = function(config) {

    config = config || {};

    Ext.apply(this, config);
};

com.conjoon.groupware.localCache.options.ui.DefaultCachingContainerUi.prototype = {

    /**
     * @cfg {com.conjoon.groupware.localCache.options.listener.DefaultCachingContainerListener}
     * actionListener
     * The actionListener for the cachingContainer this ui class manages.
     * If not provided, defaults to
     * {com.conjoon.groupware.localCache.options.listener.DefaultCachingContainerListener}
     */
    actionListener : null,

    /**
     * @type {com.conjoon.groupware.localCache.options.CachingContainer} container
     * The container this ui class manages. Gets assigned in the init() method.
     */
    container : null,

    /**
     * Inits the layout of the container.
     * Gets called from the initComponent's "initComponent()" method.
     *
     * @param {com.conjoon.groupware.localCache.options.CachingContainer} container
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
            this.actionListener = new com.conjoon.groupware.localCache.options
                                      .listener.DefaultCachingContainerListener();
        }

        this.actionListener.init(this.container);
    },

// -------- builders

    /**
     * Layouts this container.
     *
     * @protected
     */
    buildContainer : function()
    {
        Ext.apply(this.container, {
            cls    : 'cachingContainer',
            //layout : 'fit',
            border : false,
            items  : [
                this.container.getFileSettingsForm(),
                this.container.getTroubleShootingForm()
            ]
        });
    },

    /**
     * Returns the fileSettingsForm for this container.
     *
     * @return {com.conjoon.cudgets.settings.Card}
     */
    buildFileSettingsForm : function()
    {
        return new com.conjoon.cudgets.settings.Card({
            enableStartEditEvent : true,
            border               : false,
            monitorValid         : false,
            cls                  : 'com-conjoon-margin-b-20',
            items                : [
                new com.conjoon.groupware.util.FormIntro({
                    cls    : 'com-conjoon-margin-b-10',
                    labelText : com.conjoon.Gettext.gettext("File Settings"),
                    text      : com.conjoon.Gettext.gettext("Choose the filetypes that should get cached locally.")
                }),
                this.container.getCacheAllCheckbox(),
                this.container.getCacheImagesCheckbox(),
                this.container.getCacheJavascriptCheckbox(),
                new com.conjoon.groupware.util.Clear(),
                this.container.getCacheStylesheetsCheckbox(),
                this.container.getCacheSoundsCheckbox(),
                new com.conjoon.groupware.util.Clear(),
                this.container.getCacheHtmlCheckbox(),
                this.container.getCacheFlashCheckbox(),
                new com.conjoon.groupware.util.Clear()
            ]
        });
    },

    /**
     * Returns the troubleShootingForm for this container.
     *
     * @return {com.conjoon.cudgets.settings.Card}
     */
    buildTroubleShootingForm : function()
    {
        return new com.conjoon.cudgets.settings.Card({
            border : false,
            items  : [
                new com.conjoon.groupware.util.FormIntro({
                    cls       : 'com-conjoon-margin-b-10',
                    labelText : com.conjoon.Gettext.gettext("Troubleshooting")
                }),
                new Ext.BoxComponent({
                    autoEl : {
                        cls  : 'com-conjoon-width-150 com-conjoon-float-left',
                        tag  : 'div',
                        html : com.conjoon.Gettext.gettext("Clear entire cache")
                    }
                }),
                new Ext.Button({
                    text     : com.conjoon.Gettext.gettext("Clear cache"),
                    cls      : 'clearCacheButton',
                    minWidth : 115
                }),
                new com.conjoon.groupware.util.Clear(),
                this.container.getRebuildCacheCheckbox(),
                this.container.getDisableCacheCheckbox(),
                new com.conjoon.groupware.util.Clear()
            ]
        });
    },

    /**
     * Builds the checkbox for the "disable cache" option.
     *
     * @return {Ext.form.Checkbox}
     */
    buildDisableCacheCheckbox : function()
    {
        return new Ext.form.Checkbox({
            boxLabel  : com.conjoon.Gettext.gettext("... and disable cache afterwards"),
            hideLabel : true
        });
    },

    /**
     * Builds the checkbox for the "rebuild cache" option
     *
     * @return {Ext.form.Checkbox}
     */
    buildRebuildCacheCheckbox : function()
    {
        return new Ext.form.Checkbox({
            itemCls   : 'com-conjoon-margin-t-10',
            boxLabel  : com.conjoon.Gettext.gettext("... and rebuild cache afterwards"),
            hideLabel : true
        });
    },

    /**
     * Builds the checkbox for the "Cache all" option.
     *
     * @return {Ext.form.Checkbox}
     */
    buildCacheAllCheckbox : function()
    {
        return new Ext.form.Checkbox({
            boxLabel  : com.conjoon.Gettext.gettext("Cache all"),
            itemCls   : 'com-conjoon-clear',
            hideLabel : true
        });
    },

    /**
     * Returns the checkbox for the "cache images" option.
     *
     * @return {Ext.form.Checkbox}
     */
    buildCacheImagesCheckbox : function()
    {
        return new Ext.form.Checkbox({
            boxLabel  : com.conjoon.Gettext.gettext("Images"),
            itemCls   : 'com-conjoon-float-left com-conjoon-width-125 com-conjoon-margin-l-25',
            hideLabel : true
        });
    },

    /**
     * Returns the checkbox for the "cache javascript" option.
     *
     * @return {Ext.form.Checkbox}
     */
    buildCacheJavascriptCheckbox : function()
    {
        return new Ext.form.Checkbox({
            boxLabel  : com.conjoon.Gettext.gettext("Javascript"),
            itemCls   : 'com-conjoon-float-left com-conjoon-width-125',
            hideLabel : true
        });
    },

    /**
     * Returns the checkbox for the "cache stylesheets" option.
     *
     * @return {Ext.form.Checkbox}
     */
    buildCacheStylesheetsCheckbox : function()
    {
        return new Ext.form.Checkbox({
            boxLabel  : com.conjoon.Gettext.gettext("Stylesheets"),
            itemCls   : 'com-conjoon-float-left com-conjoon-width-125 com-conjoon-margin-l-25',
            hideLabel : true
        });
    },

    /**
     * Returns the checkbox for the "cache sounds" option.
     *
     * @return {Ext.form.Checkbox}
     */
    buildCacheSoundsCheckbox : function()
    {
        return new Ext.form.Checkbox({
            boxLabel  : com.conjoon.Gettext.gettext("Sounds"),
            itemCls   : 'com-conjoon-float-left com-conjoon-width-125',
            hideLabel : true
        });
    },

    /**
     * Returns the checkbox for the "cache HTML" option.
     *
     * @return {Ext.form.Checkbox}
     */
    buildCacheHtmlCheckbox : function()
    {
        return new Ext.form.Checkbox({
            boxLabel  : com.conjoon.Gettext.gettext("HTML"),
            itemCls   : 'com-conjoon-float-left com-conjoon-width-125 com-conjoon-margin-l-25',
            hideLabel : true
        });
    },

    /**
     * Returns the checkbox for the "cache Flash" option.
     *
     * @return {Ext.form.Checkbox}
     */
    buildCacheFlashCheckbox : function()
    {
        return new Ext.form.Checkbox({
            boxLabel  : com.conjoon.Gettext.gettext("Flash"),
            itemCls   : 'com-conjoon-float-left com-conjoon-width-125',
            hideLabel : true
        });
    }

};