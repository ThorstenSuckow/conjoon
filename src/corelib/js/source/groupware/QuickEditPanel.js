/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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

Ext.namespace('com.conjoon.groupware');

/**
 * object - an object with configuration
 *              container string or object - the element to render this panel into
 *
 */
com.conjoon.groupware.QuickEditPanel = function(){

    var _onReadyAttached = false;

    var _videoQueue = null;

    var _panel = null;

    var _youtubeControl = null;

    var _playerContainer = null

    var _last = null;

    var getYoutubePanel = function(config)
    {
        config = config || {};

        if (!config.apiKey) {
            throw("No API Key for Youtube Chromeless API provided");
        }

        var playerPanel = new Ext.ux.YoutubePlayer({
            developerKey : config.apiKey,
            playerId     : 'myplayer',
            ratioMode    : 'strict',
            hideMode     : 'offsets',
            bgColor      : "#000000",
            bodyStyle    : 'background-color:#000000;'
        });

        var tyt = Ext.extend(Ext.ux.YoutubePlayer.Control, {
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
                                    com.conjoon.groupware.QuickEditPanel.loadYoutubeVideo(text);
                                  },
                        icon    : msg.QUESTION,
                        cls     :'com-conjoon-msgbox-prompt',
                        width   : 375
                    });
            }
        });

        _youtubeControl =  new tyt({
            player   : playerPanel,
            border   : false,
            id       : 'control',
            style    : 'border:none;'
        });

        _playerContainer = new Ext.Panel({
            title     : 'Ytube',
            layout    : 'fit',
            hideMode  : 'offsets',
            bbar      : _youtubeControl
        });

        com.conjoon.groupware.util.FlashControl.register(playerPanel, _playerContainer);

        return _playerContainer;
    };

    var _playQueue = function() {
        var player = _youtubeControl.player;

        if (player.videoId) {
            player.stopVideo();
            player.clearVideo();
        }

        // this is needed in case the user double clicks a link.
        // the flash movie obviously seems some defer time to init itself,
        // otherwise an empty video_id will be send to the youtube servers which
        // cannot be influenced by the server
        if (_last == _videoQueue) {
            return;
        }

        _last = _videoQueue;

        (function(){
            player.loadVideoById(_videoQueue);
            _last = null;
        }).defer(1000);
    };

    return {

        loadYoutubeVideo : function(url)
        {
            if (!_youtubeControl) {
                return;
            }

            var player = _youtubeControl.player;

            if (_panel.ownerCt.hidden) {
                _panel.ownerCt.setVisible(true);
            }

            _panel.setActiveTab(_playerContainer);

            var id = _youtubeControl._parseVideoId(url);
            if (id) {
                _videoQueue = id;
                if (!player.playerAvailable()) {
                    if (!_onReadyAttached) {
                        player.on('ready', function() {
                            _playQueue();
                        });
                        _onReadyAttached = true;
                    }
                } else {
                    _playQueue();
                }
            }
        },

        getComponent : function(config)
        {
            if (_panel !== null) {
                return _panel;
            }

            var items = [
                com.conjoon.groupware.forms.QuickContactForm.getComponent(),
                com.conjoon.groupware.forms.QuickEmailForm.getComponent()
            ];

            var apiKey = com.conjoon.groupware.Registry.get('/service/youtube/chromeless/api-key');
            if (apiKey) {
                items.push(
                    getYoutubePanel({
                        apiKey : apiKey
                    })
                );
            }

            var initConfig = {
                tabPosition  : 'bottom',
                activeTab    : 0,
                bodyStyle    : 'background:#DFE8F6;',
                resizable    : false,
                collapsed    : false,
                title        : com.conjoon.Gettext.gettext("Quickpanel"),
                height       : 205,
                cls          : 'com-conjoon-groupware-QuickPanel-editPanel',
                iconCls      : 'com-conjoon-groupware-quickpanel-NewIcon',
                headerAsText : true,
                items        : items,
                listeners : {
                    /**
                     * @bug (?) Ext 3.0 Rendering titles in the headers is disabled by default
                     * since headerAstext defaults to false for TabPanels
                     */
                    render : function(p) {
                        p.header.addClass('x-panel-header');
                    },
                    scope : this
                }
            };

            Ext.apply(initConfig, config || {});

            _panel = new Ext.TabPanel(initConfig);


            return _panel;
        }

    };


}();