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
 * Layouts the introductionContainer for the Local Cache Options Dialog.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.groupware.localCache.options.ui.DefaultIntroductionContainerUi
 */
com.conjoon.groupware.localCache.options.ui.DefaultIntroductionContainerUi = function(config) {

    config = config || {};

    Ext.apply(this, config);
};

com.conjoon.groupware.localCache.options.ui.DefaultIntroductionContainerUi.prototype = {

    /**
     * @cfg {com.conjoon.groupware.localCache.options.listener.DefaultIntroductionContainerListener}
     * actionListener
     * The actionListener for the inroductionContainer this ui class manages.
     * If not provided, defaults to
     * {com.conjoon.groupware.localCache.options.listener.DefaultIntroductionContainerListener}
     */
    actionListener : null,

    /**
     * @type {com.conjoon.groupware.localCache.options.IntroductionContainer} container
     * The container this ui class manages. Gets assigned in the init() method.
     */
    container : null,

    /**
     * Inits the layout of the container.
     * Gets called from the initComponent's "initComponent()" method.
     *
     * @param {com.conjoon.groupware.localCache.options.IntroductionContainer} container
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
                                      .listener.DefaultIntroductionContainerListener();
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
            cls    : 'introductionContainer',
            //layout : 'fit',
            border : false,
            items  : [
                this.container.getFormIntro(),
                this.container.getAvailableComponent()
            ]
        });
    },

    /**
     * Returns the formIntro for this container.
     *
     * @return {com.conjoon.groupware.util.FormIntro}
     */
    buildFormIntro : function()
    {
        return new com.conjoon.groupware.util.FormIntro({
            justify    : true,
            labelText  : com.conjoon.Gettext.gettext("Local application cache"),
            text       : String.format(
                com.conjoon.Gettext.gettext("The local application cache allows you to store static file resources on your computer which are needed to run {0} and usually loaded from the server. By creating a local cache, you can speed up loading the application, since static file resources are served right from your local computer.<br /> Please adjust your cache settings using the various options you can find in the other tabs."),
                com.conjoon.groupware.Registry.get('/base/conjoon/name')
            ),
            imageClass : 'formIntroImage'
        });
    },

    /**
     * Returns the element that indicates thatLocal Cache is installed and
     * available.
     *
     * @param {Mixed} type The type of application cache that is available,
     * can be com.conjoon.cudgets.localCache.Api.type.NONE or any other type as
     * returned by the localCache API
     *
     * @return {Ext.BoxComponent}
     */
    buildAvailableComponent : function(type)
    {
        var cls  = 'availableComponent';
        var html = "";

        switch (type) {

            case com.conjoon.cudgets.localCache.Api.type.NONE:
                cls  = 'notAvailableComponent';
                html = com.conjoon.Gettext.gettext("An Application Cache based on the HTML 5 specifications is not available.");
            break;

            default:
                html = String.format(
                    com.conjoon.Gettext.gettext("An Application Cache is available. The adapter used for caching operations is \"{0}\""),
                    type
                );
            break;

        }

        return new Ext.BoxComponent({
            autoEl : {
                tag  : 'div',
                cls  : cls,
                html : html
            }
        });
    }
};