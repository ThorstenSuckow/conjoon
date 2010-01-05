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

Ext.namespace('com.conjoon.groupware.workbench');

/**
 * This is a private class used internally by the workbench's center panel. It
 * allows for autoscroll when a tab gets dragged over a tab-scroller.
 *
 * @class com.conjoon.groupware.workbench.ClickRepeater
 * @extends Ext.util.ClickRepeater
 */
com.conjoon.groupware.workbench.ClickRepeater = Ext.extend(Ext.util.ClickRepeater, {

    /**
     * @type {Boolean} down Set to true if the scrolling was initiated by a
     * click operation.
     */
    down : false,

    /**
     * @type {Boolean} over Set to true if the scrolling was initiated by a
     * drag operation.
     */
    over : false,

    /**
     * @type {Ext.util.ClickRepeater} _superclass Shortcut
     * for the parent class of this class.
     */
    _superclass : Ext.util.ClickRepeater.prototype,

    /**
     * Overrides parent implementation by adding a "mouseover" listener.
     * Additionally, the dragZone's "afterRepair"-method of the component
     * specified in "scope" will be sequenced to call "handleMouseUp()" if
     * there is currently a scroll-timer defined that was initiated using a
     * drag operation.
     *
     */
    enable: function()
    {
        if(this.disabled){
            this.el.on('mouseover', this.onMouseOver, this);
            var dz = this.scope._tabDragZone;
            dz.afterRepair = dz.afterRepair.createSequence(
                function() {
                    if (this.over) {
                        this.handleMouseUp();
                    }
            }, this);
        }

        this._superclass.enable.call(this);
    },

    /**
     * Listener for the "mouseover" event for the element specified for
     * this repeater.
     * If there is currently a scroll processing, the method will do nothing and
     * return. Otherwise, it will first check if there is currently a scroll
     * processing for the opposite scroller and end in. Then the scroll-timer
     * for this isntance is created.
     */
    onMouseOver : function()
    {
        if (!this.scope._tabDragZone.dragging || this.over || this.down) {
            return;
        }

        if (this.scope.leftRepeater == this) {
            if (this.scope.rightRepeater.over) {
                this.scope.rightRepeater.over = false;
                this.scope.rightRepeater.handleMouseUp();
            }
        } else if (this.scope.rightRepeater == this) {
            if (this.scope.leftRepeater.over) {
                this.scope.leftRepeater.over = false;
                this.scope.leftRepeater.handleMouseUp();
            }
        }

        this.over = true;
        this.el.on("mouseout",      this.handleMouseUp, this);
        Ext.getDoc().on("mouseup",  this.handleMouseUp, this);
        this.timer = this.click.defer(this.delay || this.interval, this);
    },

    /**
     * Overrides parent implementation by first checking if there is currently any
     * scroll process going on, and return if this is the case. Otehrwise, the
     * parent's implementation is being called.
     *
     */
    handleMouseDown : function(){
        if (this.over || this.down) {
            return;
        }
        this.down = true;

        this._superclass.handleMouseDown.call(this);
    },

    /**
     * Overrides parent implementation by setting the "over"/"down" property of
     * this instance to "false".
     *
     */
    handleMouseReturn : function()
    {
        this._superclass.handleMouseReturn.call(this);
        this.down = this.over = false;
    },

    /**
     * Overrides parent implementation by first checking if there is currently a
     * drag process for this instance is being processed, initiated by a drag, and return
     * if that is the case.
     * Otherwise, parent's implementation is being called.
     *
     */
    handleMouseUp : function()
    {
        if (this.scope._tabDragZone.dragging && this.over) {
            return;
        }

        this.el.un("mouseout", this.handleMouseUp, this);
        this.down = this.over = false;
        this._superclass.handleMouseUp.call(this);
    }
});