/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
