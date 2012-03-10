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


Ext.namespace('com.conjoon.groupware.service.youtube');

/**
 * A singleton for managing actions related to watching youtube videos.
 *
 *
 * @class com.conjoon.groupware.service.youtube.ViewBaton
 * @singleton
 */
com.conjoon.groupware.service.youtube.ViewBaton = function() {

    /**
     * @type {Ext.ux.YoutubePlayer} player
     */
    var player;

    /**
     * @type {Ext.Panel} basePanel
     */
    var basePanel;

    /**
     * @type {com.conjoon.groupware.service.youtube.FeaturePanel} featurePanel
     */
    var featurePanel;

    /**
     * @type {Ext.ux.YoutubePlayer.Control} control
     */
    var control;

    /**
     * @type {Ext.ux.util.FlashControl} flashControl
     */
    var flashControl;

    /**
     * Builds a new {Ext.ux.YoutubePlayer}
     *
     * @return {Ext.ux.YoutubePlayer}
     */
    var buildPlayer = function()
    {
        return new Ext.ux.YoutubePlayer({
            playerId     : 'myplayer',
            ratioMode    : 'stretch',
            hideMode     : 'offsets',
            bgColor      : "#000000",
            bodyStyle    : 'background-color:#000000;'
        });
    };

    /**
     * Builds up a new control for the player used by this singleton.
     *
     * @return {Ext.ux.YoutubePlayer.Control}
     */
    var buildControl = function()
    {
        return new Ext.ux.YoutubePlayer.Control({
            player   : player,
            border   : false,
            id       : 'control',
            style    : 'border:none;',
            _onEject : function() {
                var control = this;
                var msg   = Ext.MessageBox;
                    msg.show({
                        prompt : true,
                        title   : com.conjoon.Gettext.gettext("Load video"),
                        msg     : com.conjoon.Gettext.gettext("Please submit the id or the full url of the youtube video you want to load."),
                        buttons : msg.OKCANCEL,
                        fn      : function(btn, text){
                                    if (btn != 'ok') {
                                       return;
                                    }
                                    com.conjoon.groupware.service.youtube.VideoDirector.loadVideo(text);
                                  },
                        icon    : msg.QUESTION,
                        cls     :'com-conjoon-msgbox-prompt',
                        width   : 375
                    });
            },
            _onError : function(errorCode, playerPanel, player) {
                playerPanel.stopVideo();

                com.conjoon.SystemMessageManager.error(new com.conjoon.SystemMessage({
                    title : com.conjoon.Gettext.gettext("Error"),
                    text  : String.format(
                        com.conjoon.Gettext.gettext("The video you requested could not be played. Error code \"{0}\"."),
                        errorCode
                    )
                }));
            }
        });
    };

    /**
     * Returns the Ext.Panel that holds the player and its control.
     *
     * @return {Ext.Panel}
     */
    var buildBasePanel = function()
    {
       return new Ext.Panel({
            title     : 'Ytube',
            hideMode  : 'offsets',
            cls       : 'youtube',
            layout    : 'fit',
            bbar      : control
        });
    };

    /**
     * Returns the com.conjoon.groupware.service.youtube.FeaturePanel that
     * is used to be rendered as a contentPanel's tab and holding the basePanel
     * to view FlashVideos..
     *
     * @return {com.conjoon.groupware.service.youtube.FeaturePanel}
     */
    var buildFeaturePanel = function()
    {
        return new com.conjoon.groupware.service.youtube.FeaturePanel({
            listeners : {
                close : function() {
                    var pl = this.getPlayer();

                    var qec = com.conjoon.groupware.QuickEditPanel.getComponent();
                    if (pl && (!qec.isVisible() || !qec.ownerCt.isVisible())) {
                        if (pl) {
                            switch (pl.getPlayerState()) {
                                case 'playing':
                                case 'buffering':
                                    pl.pauseVideo();
                                    break;
                                default:
                                    pl.on('stateChange', function(state, player){
                                        pl.stopVideo();
                                        pl.clearVideo();
                                    },pl, {single : true});
                                    break;
                            }
                        }
                    }

                    this.showInQuickPanel();
                },
                scope : com.conjoon.groupware.service.youtube.ViewBaton
            }

        });
    };

    /**
     * Returns an instance of {Ext.ux.util.FlashControl} used by this class.
     *
     * @return {Ext.ux.utilFlashControl}
     */
    var buildFlashControl = function()
    {
        return new Ext.ux.util.FlashControl({
            flashComponent    : player,
            container         : basePanel,
            getListenerConfig : function() {

                var featurePanel = com.conjoon.groupware.service.youtube.ViewBaton.getFeaturePanel();
                var quickPanel   = com.conjoon.groupware.QuickEditPanel.getComponent();

                var westPanel = com.conjoon.util.Registry.get(
                    'com.conjoon.groupware.Workbench'
                ).getWestPanel();
                var eastPanel = com.conjoon.util.Registry.get(
                    'com.conjoon.groupware.Workbench'
                ).getEastPanel();

                var itemsWest = westPanel.items.items;
                var itemsEast = eastPanel.items.items;

                return {
                    activate : {
                        items  : [this.container, featurePanel],
                        fn     : function(){
                            var ct = this.container.ownerCt;
                            while(ct) {
                                if (ct.hidden === true) {
                                    this.hideFlashComponent();
                                    return;
                                }
                                ct = ct.ownerCt;
                            }
                            this.showFlashComponent();
                        },
                        scope  : this,
                        strict : true
                    },
                    deactivate : {
                        items  : [this.container, featurePanel],
                        fn     : this.flashComponent.hide,
                        scope  : this.flashComponent,
                        strict : true
                    },
                    drop : {
                        items  : [eastPanel, westPanel],
                        fn     : 'refreshListeners',
                        strict : false
                    },
                    show : {
                        items  : [quickPanel, eastPanel, westPanel],
                        fn     : this.flashComponent.show,
                        scope  : this.flashComponent,
                        strict : true
                    },
                    hide : {
                        items  : [quickPanel, eastPanel, westPanel],
                        fn     : this.flashComponent.hide,
                        scope  : this.flashComponent,
                        strict : true
                    },
                    afterlayout : {
                        items  : [eastPanel, westPanel],
                        fn     : 'afterContainerLayout',
                        strict : true
                    },
                    beforeexpand : {
                        items  : itemsEast.concat(itemsWest),
                        fn     : this.flashComponent.hide,
                        scope  : this.flashComponent,
                        strict : [eastPanel, westPanel]
                    },
                    expand : {
                        items  : itemsEast.concat(itemsWest),
                        fn     : this.flashComponent.show,
                        scope  : this.flashComponent,
                        strict : [eastPanel, westPanel]
                    },
                    beforecollapse : {
                        items  : itemsEast.concat(itemsWest),
                        fn     : this.flashComponent.hide,
                        scope  : this.flashComponent,
                        strict : [eastPanel, westPanel]
                    },
                    collapse : {
                        items  : itemsEast.concat(itemsWest),
                        fn     : function(component) {
                            if (component != this.container.ownerCt) {
                                this.flashComponent.show();
                            }
                        },
                        strict : [eastPanel, westPanel]
                    }
                };
            }
        });
    };

    return {

        /**
         * Returns the YoutubePlayer used by this baton.
         * If no api-key for the youtube chromeless api was found in the registry,
         * null will be returned.
         *
         * @return {Ext.ux.YoutubePlayer}
         */
        getPlayer : function()
        {
            if (player) {
                return player;
            }

            player = buildPlayer();

            return player;
        },

        /**
         * Returns the control that is bound to the player used by this
         * baton. If no api key was specified for the player, null
         * will be returned.
         *
         * @return {Ext.ux.YoutubePlayer}
         */
        getControl : function()
        {
            if (control) {
                return control;
            }

            this.getPlayer();

            if (!player) {
                return null;
            }

            control = buildControl();

            return control;
        },

        /**
         * Returns the {Ext.ux.util.FlashControl} that mediates between a container
         * and player. Returns null if no api key for the Youtube Chromeless API was
         * specified, or if the basePanel for the player and its control was not
         * built yet.
         *
         * @return {Ext.ux.util.FlashControl}
         */
        getFlashControl : function()
        {
            if (flashControl) {
                return flashControl;
            }

            if (!basePanel) {
                return null;
            }

            flashControl = buildFlashControl();

            return flashControl;
        },

        /**
         * Returns true if the player's basePanel was already rendered and
         * can display the player.
         *
         * @return {Boolean}
         */
        playerAvailable : function()
        {
            return basePanel != null && basePanel.rendered;
        },

        /**
         * Returns the base Ext.Panel that holds the player and the control
         * per default. Retunrs null if no api key was specified for the
         * Youtube Chromeless API.
         * This method does automaically build the flashControl if its not available
         * yet.
         *
         * @return {Ext.Panel}
         */
        getBasePanel : function()
        {
            if (basePanel) {
                return basePanel;
            }

            this.getControl();

            if (control == null) {
                return null;
            }

            basePanel = buildBasePanel();
            this.getFlashControl();

            return basePanel;
        },

        /**
         * Returns the featurePanel if available.
         *
         * @return {com.conjoon.groupware.service.youtube.FeaturePanel}
         */
        getFeaturePanel : function()
        {
            return featurePanel;
        },

        /**
         * Attempts to remove the basePanel from the
         * {com.conjoon.groupware.QuickEditPanel} container and add it to the
         * FeatureTab to the contentPanel. The feature tab will be build if not already
         * available and inserted at the specified position in the contentPanel.
         *
         */
        showInFeaturePanel : function(position)
        {
            if (!featurePanel) {
                featurePanel = buildFeaturePanel();
            }

            var contentPanel = com.conjoon.util.Registry.get(
                'com.conjoon.groupware.ContentPanel'
            );

            if (com.conjoon.groupware.QuickEditPanel.getComponent().findById(basePanel.getId())) {
                basePanel.ownerCt.remove(basePanel, false);
                featurePanel.add(basePanel);
                basePanel.setVisible(true);
            }

            flashControl.refreshListeners();

            if (!Ext.isNumber(position)) {
                contentPanel.add(featurePanel);
            } else {
                contentPanel.insert(position, featurePanel);
            }
            contentPanel.setActiveTab(featurePanel);
        },

        /**
         * Attempts to remove the basePanel from the featurePanel
         * container and add it to the QuickEditPanel. This is the listener for the
         * featurePanels 'close' operation. The feature panel will get destroyed, thus
         * we will reset the featurePanel variable to null.
         *
         * @return {com.conjoon.groupware.service.youtube.FeaturePanel}
         */
        showInQuickPanel : function(position)
        {
            var quickPanel = com.conjoon.groupware.QuickEditPanel.getComponent();

            if (featurePanel && basePanel.ownerCt == featurePanel) {
                basePanel.ownerCt.remove(basePanel, false);
                quickPanel.add(basePanel);
            }

            featurePanel = null;

            flashControl.refreshListeners();

            quickPanel.setActiveTab(basePanel);
        },

        /**
         * Attempts to activate either the quickpanel or the featurepanel,
         * depending on the ownerCt of the basePanel, for showing the video.
         *
         * @return {Boolean} false if the player could not be shown, otherwise
         * true
         */
        showPlayer : function()
        {
            this.getBasePanel();

            if (!basePanel) {
                return false;
            }
            if (featurePanel && featurePanel.findById(basePanel.getId())) {
                featurePanel.ownerCt.setActiveTab(featurePanel);
                return true;
            }

            var quickPanel = com.conjoon.groupware.QuickEditPanel.getComponent();

            if (quickPanel && quickPanel.findById(basePanel.getId())) {
                // check whether any of the basePanel's ownerct is hidden
                var ct = basePanel;
                while (ct) {
                    if ((ct.ownerCt && ct.ownerCt.hidden === true) ||ct.collapsed === true) {
                        this.showInFeaturePanel();
                        return true;
                    }
                    ct = ct.ownerCt;
                }

                quickPanel.setActiveTab(basePanel);
            }

            return true;
        }


    };


}();