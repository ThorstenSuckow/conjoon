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

    // shorthands



    var _panel = null;

    var getYoutubePanel = function()
    {
        var playerPanel = new Ext.ux.YoutubePlayer({
            developerKey : "AI39si7YwEMBcpCOO8JzYSjB3WtaS2ODhBN-A4XAqVADfGWyK8-Nr9XwZzr_sdCnsKirffyPDBsvC0z7MdR2u0xeM4zLIDWLIQ",
            playerId     : 'myplayer',
            ratioMode    : 'strict',
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
                                    var id = control._parseVideoId(text);
                                    if (id) {
                                        control.player.stopVideo();
                                        control.player.clearVideo();
                                        control.player.cueVideoById(id);
                                    }
                                  },
                        icon    : msg.QUESTION,
                        cls     :'com-conjoon-msgbox-prompt',
                        width   : 375
                    });
            }
        });

        pControl =  new tyt({
            player   : playerPanel,
            border   : false,
            id       : 'control',
            style    : 'border:none;'
        });

        ;

        var w = new Ext.Panel({
            title     : 'Ytube',
            layout    : 'fit',
            hideMode  : 'visibility',
            items     : [playerPanel],
            bbar      : pControl
        });

        return w;
    };

    return {


        getComponent : function(config)
        {
            if (_panel !== null) {
                return _panel;
            }

            var initConfig = {
                tabPosition : 'bottom',
                activeTab   : 0,
                bodyStyle   : 'background:#DFE8F6;',
                resizable   : false,
                collapsed   : false,
                title       : com.conjoon.Gettext.gettext("Quickpanel"),
                height      : 205,
                cls         : 'com-conjoon-groupware-QuickPanel-editPanel',
                iconCls     : 'com-conjoon-groupware-quickpanel-NewIcon',
                headerAsText  : true,
                items       : [
                    com.conjoon.groupware.forms.QuickContactForm.getComponent(),
                    com.conjoon.groupware.forms.QuickEmailForm.getComponent(),
                    getYoutubePanel()
                ],
                listeners : {
                    /**
                     * @bug (?) Ext 2.2 Rendering titles in the headers is disabled by default
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