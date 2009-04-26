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

Ext.namespace('com.conjoon.groupware.workbench');

/**
`*
 *
 * @class com.conjoon.groupware.workbench.PanelDragSource
 * @extends Ext.Panel.DD
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.groupware.workbench.PanelDragSource = function() {

    return {

        getConfig : function()
        {
            return {
                insertProxy: true,

                b4StartDrag: function(x, y)
                {
                    var psibl = this.panel.previousSibling();
                    if (psibl && psibl.splitEl) {
                        psibl.splitEl.setVisible(false);
                    }
                    this.panel._wasExpanded = !this.panel.collapsed;
                    this.panel.ownerCt.getLayout().collapse(this.panel, true);
                    this.proxy.show();
                 },

                startDrag : function(x, y)
                {
                    this.dragData.dragSourceContainer = this.dragData.panel.ownerCt;
                },

                endDrag : function(e)
                {
                    var psibl = this.panel.previousSibling();
                    if (psibl && psibl.splitEl) {
                        psibl.splitEl.setVisible(true);
                    }
                    var workbench = com.conjoon.util.Registry.get('com.conjoon.groupware.Workbench')

                    workbench.checkIfCollapsible(this.dragData.dragSourceContainer);

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