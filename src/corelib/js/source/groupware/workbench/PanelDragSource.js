/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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

Ext.namespace('com.conjoon.groupware.workbench');

/**
`*
 *
 * @class com.conjoon.groupware.workbench.PanelDragSource
 * @extends Ext.Panel.DD
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
com.conjoon.groupware.workbench.PanelDragSource = function() {

    return {

        getConfig : function()
        {
            return {

                insertProxy: true,

                startDrag : function(x, y)
                {
                    this.dragData.dragSourceContainer = this.dragData.panel.ownerCt;
                },

                endDrag : function(e)
                {
                    var workbench = com.conjoon.util.Registry.get('com.conjoon.groupware.Workbench')

                    workbench.checkIfCollapsible(workbench.getEastPanel());
                    workbench.checkIfCollapsible(workbench.getWestPanel());

                    this.proxy.hide();
                    this.panel.saveState();
                },

                onDrag : function(e)
                {
                    var workbench = com.conjoon.util.Registry.get('com.conjoon.groupware.Workbench');

                    var west = workbench.getWestPanel();
                    var east = workbench.getEastPanel();

                    if (west.hidden && e.getPageX() >= 0 && e.getPageX() <= 50) {
                        west.setVisible(true);
                        this.DDM.refreshCache(this.groups);
                    } else if (east.hidden && e.getPageX() <= workbench.getSize().width && e.getPageX() >= workbench.getSize().width - 50) {
                        east.setVisible(true);
                        this.DDM.refreshCache(this.groups);
                    }


                }
            };

        }

    }

}();