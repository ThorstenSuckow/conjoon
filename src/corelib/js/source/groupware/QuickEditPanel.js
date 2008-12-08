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

    //var com.conjoon.groupware.QuickContactForm;
    //var com.conjoon.groupware.QuickEmailForm;


    // shorthands
    var _quickContactForm = com.conjoon.groupware.forms.QuickContactForm;
    var _quickEmailForm   = com.conjoon.groupware.forms.QuickEmailForm;


    var _panel = null;

    var getYoutubePanel = function()
    {
        var playerPanel = Ext.ux.YoutubePlayer.createPanel({
            developerKey : "AI39si7YwEMBcpCOO8JzYSjB3WtaS2ODhBN-A4XAqVADfGWyK8-Nr9XwZzr_sdCnsKirffyPDBsvC0z7MdR2u0xeM4zLIDWLIQ",
            playerId     : 'myplayer',
            border       : false,
            ratioMode    : 'strict',
            autoScroll   : false,
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
            title        : 'Ytube',
            layout       : 'fit',
            hideMode     : 'offsets',
            items        : [playerPanel],
            bbar         : pControl,
            listeners   : {
                'resize'         : function(){this.bottomToolbar.fireEvent('resize')}
            }});

        return w;
    };

    var _createLayout = function()
    {
        _panel.add(_quickContactForm.getComponent());
        _panel.add(_quickEmailForm.getComponent());
        _panel.add(getYoutubePanel());
    };

    var _installListeners = function()
    {
        _panel.on('beforerender', _createLayout, this);
    };

    var _initComponents = function()
    {
        _installListeners.call(this);
        _createLayout.call(this);
    };

    return {


        getComponent : function()
        {
            if (_panel !== null) {
                return _panel;
            }

            _panel = new Ext.TabPanel({
                         tabPosition:'bottom',
                         activeTab:0, // NEEDS TO BE CALLED WHEN COMPONENT IS INITIALIZED
                         border:false,
                         region:'center',
                         height:180,
                         bodyStyle:'background:#DFE8F6;'
                     });

            _panel.on('beforerender', _initComponents, this);

            return _panel;
        },

        render : function()
        {
            if (_panel.rendered) {
                return;
            }

            _panel.render();


                       /* new Ext.TabPanel( {
                                        tabPosition:'bottom',
                                        activeTab:0,
                                        border:false,
                                        region:'center',
                                        height:180,
                                        bodyStyle:'background:#DFE8F6;',
                                        items:[
                                            new Ext.FormPanel({
                                                labelWidth: 0,
                                                frame:false,
                                                labelAlign:'left',
                                                title: 'Kontakt',
                                                width: 220,
                                                bodyStyle:'background:#DFE8F6;padding:5px;',
                                                cls: 'x-small-editor',
                                                labelPad: 0,
                                                defaultType: 'textfield',
                                                hideLabels:true,
                                                defaults: {
                                                    width: 210
                                                },
                                                layoutConfig: {
                                                    labelSeparator: ''
                                                },
                                                items: [{
                                                        fieldLabel: 'Vorname',
                                                        name: 'first',
                                                        emptyText:'<Vorname>',
                                                        allowBlank: false
                                                    },{
                                                        fieldLabel: 'Nachname',
                                                        emptyText:'<Nachname>',
                                                        name: 'last'
                                                    },{
                                                        fieldLabel: 'Email',
                                                        name: 'email',
                                                        emptyText:'<Email-Adresse>',
                                                        vtype:'email'
                                                    }, new Ext.form.Checkbox({
                                                        boxLabel: 'zum Bearbeiten wechseln',
                                                        ctCls: 'com-conjoon-groupware-quickpanel-SmallEditorFont'
                                                    })
                                                ],
                                                buttons: [{
                                                        text: 'Save'
                                                    },{
                                                        text: 'Cancel'
                                                    }]
                                            }),
                                            new Ext.FormPanel({
                                                labelWidth: 0,
                                                frame:false,
                                                labelAlign:'left',
                                                title: 'Email',
                                                width: 220,
                                                bodyStyle:'background:#DFE8F6;padding:5px',
                                                cls: 'x-small-editor',
                                                labelPad: 0,
                                                defaultType: 'textfield',
                                                hideLabels:true,
                                                defaults: {
                                                    // applied to each contained item
                                                    width: 210
                                                },
                                                layoutConfig: {
                                                    // layout-specific configs go here
                                                    labelSeparator: ':'
                                                },
                                                items: [{
                                                        fieldLabel: 'An',
                                                        name: 'first',
                                                        emptyText: '<Empfaenger>',
                                                        allowBlank: false
                                                    },{
                                                        fieldLabel: 'Betreff',
                                                        emptyText: '<Betreff>',
                                                        name: 'last'
                                                    },
                                                    new Ext.form.TextArea({
                                                        emptyText: '<Nachricht>',
                                                        fieldLabel:'Nachricht',
                                                        height:50
                                                    })
                                                ],
                                                buttons: [
                                                    {
                                                        text: 'Save'
                                                    },{
                                                        text: 'Cancel'
                                                }]
                                            })
                                        ]
                                 });*/
        }

    };

    this.initComponent();

}();