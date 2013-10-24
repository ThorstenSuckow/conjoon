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

Ext.namespace('com.conjoon.groupware');

/**
 *
 * @class com.conjoon.groupware.QuickEditPanel
 * @singleton
 */
com.conjoon.groupware.QuickEditPanel = function(){


    var _panel = null;

    return {


        getComponent : function(config)
        {
            if (_panel !== null) {
                return _panel;
            }

            var items = [
                com.conjoon.groupware.forms.QuickContactForm.getComponent(),
                com.conjoon.groupware.forms.QuickEmailForm.getComponent()
            ];

            var youtubeBasePanel = com.conjoon.groupware.service.youtube.ViewBaton.getBasePanel(
                config.workbench
            );
            if (youtubeBasePanel) {
                items.push(youtubeBasePanel);
            }

            var initConfig = {
                tabPosition  : 'bottom',
                activeTab    : 0,
                bodyStyle    : 'background:#DFE8F6;',
                resizable    : false,
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
                },
                // override initEvents to create the dragZone here
                initEvents : function() {
                    Ext.TabPanel.prototype.initEvents.call(this);
                    var m = new Ext.Element(this.footer.dom.lastChild);
                    this._tabDragZone = new com.conjoon.groupware.workbench.dd.TabDragZone(
                        this, m, [
                            com.conjoon.groupware.forms.QuickContactForm.getComponent()
                        ]
                    );

                    this.footer.on('mousedown', this._tabDragZone.callHandleMouseDown, this._tabDragZone);

                }
            };

            Ext.apply(initConfig, config || {});

            _panel = new Ext.TabPanel(initConfig);


            return _panel;
        }

    };


}();
