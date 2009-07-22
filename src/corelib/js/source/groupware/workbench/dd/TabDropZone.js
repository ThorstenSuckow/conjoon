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

Ext.namespace('com.conjoon.groupware.workbench.dd');

/**
 * An implementation of {Ext.dd.DropZone} to use for the workbench content's
 * panel header.
 *
 * @class com.conjoon.groupware.workbench.dd.TabDropZone
 * @extends Ext.dd.DropZone
 */
com.conjoon.groupware.workbench.dd.TabDropZone = function(tabPanel, header, notDroppables){
    this.tabPanel = tabPanel;
    this.header   = header;

    this.proxyTop = Ext.DomHelper.append(document.body, {
        cls:"com-conjoon-groupware-workbench-drop-ind-top", html:"&#160;"
    }, true);
    this.proxyBottom = Ext.DomHelper.append(document.body, {
        cls:"com-conjoon-groupware-workbench-drop-ind-bottom", html:"&#160;"
    }, true);
    this.proxyTop.hide = this.proxyBottom.hide = function(){
        this.setLeftTop(-100,-100);
        this.setStyle("visibility", "hidden");
    };
    this.ddGroup = "workbench";
    com.conjoon.groupware.workbench.dd.TabDropZone.superclass.constructor.call(this, header);
    this.notDroppables = notDroppables || [];
};

Ext.extend(com.conjoon.groupware.workbench.dd.TabDropZone, Ext.dd.DropZone, {

    /**
     * @type {Array} proxyOffsets
     */
    proxyOffsets : [-4, -9],

    /**
     * @type {Function} flx Shortcut for Ext.Element.fly
     */
    fly          : Ext.Element.fly,

    /**
     * Gets the {Ext.Panel} represented by the passed DOM element which points
     * to the clickable tab element.
     *
     * @param {HTMLElement} tabEl
     *
     * @return {Object} an object with the following properties:
     * - {Ext.Panel} panel: The panel represented by the tabEl
     * - {Number} pos: The position of the panel within the TabPanel
     */
    getPanelInfoForTabEl : function(tabEl)
    {
        var items = this.tabPanel.items;
        var item  = null;

        for (var i = 0, len = items.length; i < len; i++) {
            var item = items.get(i);
            if (this.tabPanel.getTabEl(item) == tabEl) {
                return {
                    panel : item,
                    pos   : items.indexOf(item)
                }
            }
        }

        return null;
    },

    /**
     * Return the DOM element for the panels tab if found.
     *
     * @return {HtmlElement}
     */
    getTargetFromEvent : function(e)
    {
        var t = Ext.lib.Event.getTarget(e);
        var h = Ext.fly(t).findParent('li', 5);
        return h;
    },

    /**
     * Renders the position indicators to the header, marking the spot
     * where the currently dragged tab could be dropped.
     */
    positionIndicator : function(h, n, e)
    {
        var x = Ext.lib.Event.getPageX(e);
        var r = Ext.lib.Dom.getRegion(n);
         var px, pt, py = r.top + this.proxyOffsets[1];
        if((r.right - x) <= (r.right-r.left)/2){
            px = r.right;
            pt = "after";
        }else{
            px = r.left-2;
            pt = "before";
        }

        px +=  this.proxyOffsets[0];
        this.proxyTop.setLeftTop(px, py);
        this.proxyTop.show();
        if(!this.bottomOffset){
            this.bottomOffset = this.header.getHeight();
        }
        this.proxyBottom.setLeftTop(px, py+this.proxyTop.dom.offsetHeight+this.bottomOffset);
        this.proxyBottom.show();
        return pt;
    },

    /**
     *
     * @see positionIndicator
     */
    onNodeEnter : function(n, dd, e, data)
    {
        if(data.tab != n){
            this.positionIndicator(data.tab, n, e);
        }
    },

    /**
     * Called when the dropZone recognizes a drag over a target
     * and checks first if the target is within "notDroppables", and voids
     * if that is the case, otherwise indicates that a drop is allowed.
     *
     * @return {Boolean}
     */
    onNodeOver : function(n, dd, e, data)
    {
        var result = false;
        if(data.tab != n) {
            result = this.positionIndicator(data.tab, n, e);

            if (result) {
                var info = this.getPanelInfoForTabEl(n);
                for (var i = 0, len = this.notDroppables.length; i < len; i++) {
                    if (this.notDroppables[i].panel == info.panel) {
                        if (this.notDroppables[i].pos.indexOf(result) != -1) {
                            result = false;
                            break;
                        }
                    }
                }
            }
        }
        if(!result){
            this.proxyTop.hide();
            this.proxyBottom.hide();
        }
        return result ? this.dropAllowed : this.dropNotAllowed;
    },

    /**
     * Hides the position indicators.
     */
    onNodeOut : function(n, dd, e, data)
    {
        this.proxyTop.hide();
        this.proxyBottom.hide();
    },

    /**
     * Called when a drop has occured. Calculates the proper
     * position where to insert the dropped tab and fires the
     * "validatedrop"/"beforedrop" event for the content panel before
     * the drop is executed. Return false to cancel the drop.
     * After a successful drop, the "drop" event for the content's panel is
     * fired.
     */
    onNodeDrop : function(n, dd, e, data)
    {
        var h = data.tab;

        if(h != n){
            var x  = Ext.lib.Event.getPageX(e);
            var r  = Ext.lib.Dom.getRegion(n);
            var pt = (r.right - x) <= ((r.right-r.left)/2) ? "after" : "before";

            var orgPos = data.pos;
            var info   = this.getPanelInfoForTabEl(n);

            if (!info) {
                return false;
            }

            var pos = info.pos;

            if(pt == "before" && orgPos < pos){
                pos--;
            } else if(pt == "after" && orgPos > pos){
                pos++;
            }

            if(this.tabPanel.fireEvent('validatedrop', data) !== false &&
               this.tabPanel.fireEvent('beforedrop', data) !== false){

                this.tabPanel.remove(data.panel, false);
                this.tabPanel.insert(pos, data.panel);
                this.tabPanel.setActiveTab(data.panel);

                this.tabPanel.fireEvent('drop', data);

                return true;
            }
        }
        return false;
    }
});