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
 * Layouts the settingsContainer for the Local Cache Options Dialog.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.groupware.localCache.options.ui.DefaultsettingsContainerUi
 */
com.conjoon.groupware.localCache.options.ui.DefaultSettingsContainerUi = function(config) {

    config = config || {};

    Ext.apply(this, config);
};

com.conjoon.groupware.localCache.options.ui.DefaultSettingsContainerUi.prototype = {

    /**
     * @cfg {com.conjoon.groupware.localCache.options.listener.DefaultSettingsContainerListener}
     * actionListener
     * The actionListener for the settingsContainer this ui class manages.
     * If not provided, defaults to
     * {com.conjoon.groupware.localCache.options.listener.DefaultSettingsContainerListener}
     */
    actionListener : null,

    /**
     * @type {com.conjoon.groupware.localCache.options.SettingsContainer} container
     * The container this ui class manages. Gets assigned in the init() method.
     */
    container : null,

    /**
     * Inits the layout of the container.
     * Gets called from the initComponent's "initComponent()" method.
     *
     * @param {com.conjoon.groupware.localCache.options.SettingsContainer} container
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
                                      .listener.DefaultSettingsContainerListener();
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
        var cacheAvailable = com.conjoon.cudgets.localCache.Api.isCacheAvailable();

        Ext.apply(this.container, {
            cls    : 'settingsContainer',
            layout : 'fit',
            items  : [
                new Ext.TabPanel({
                    activeItem : 0,
                    items      : [
                        new Ext.Panel({
                            layout : 'fit',
                            title  : com.conjoon.Gettext.gettext("Introduction"),
                            items  : [
                                this.container.getIntroductionContainer()
                            ]
                        }),
                        new Ext.Panel({
                            layout : 'fit',
                            title  : com.conjoon.Gettext.gettext("Caching"),
                            items  : [
                                this.container.getCachingContainer()
                            ],
                            disabled : !cacheAvailable,
                        })
                    ]
                })
            ]
        });
    },

    /**
     * Builds the introductionContainer.
     *
     * @return {com.conjoon.groupware.localCache.options.IntroductionContainer]
     */
    buildIntroductionContainer : function()
    {
        return new com.conjoon.groupware.localCache.options.IntroductionContainer({
            settingsContainer : this.container
        });
    },

    /**
     * Builds the cachingContainer.
     *
     * @return {com.conjoon.groupware.localCache.options.CachingContainer]
     */
    buildCachingContainer : function()
    {
        return new com.conjoon.groupware.localCache.options.CachingContainer({
            settingsContainer : this.container
        });
    },

    /**
     * Masks this container, showing the given message.
     *
     * @param {String} message
     */
    maskContainer : function(message)
    {
        this.container.el.mask(message, 'x-mask-loading');
    },

    /**
     * Unsmasks this container.
     *
     */
    unmaskContainer : function()
    {
        this.container.el.unmask();
    }

};