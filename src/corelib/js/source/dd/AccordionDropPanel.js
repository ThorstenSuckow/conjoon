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

Ext.namespace('com.conjoon.dd');

/**
 * A special implemenation of a {Ext.Panel} that uses {com.conjoon.dd.AccordionDropTarget}
 * and {com.conjoon.layout.ResizableAccordionLayout} for allowing reorganizing and drag/drop.
 *
 * @class com.conjoon.dd.AccordionDropPanel
 * @extends Ext.Panel
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.dd.AccordionDropPanel = Ext.extend(Ext.Panel, {

    /**
     * Inits this component.
     *
     */
    initComponent : function()
    {
        Ext.apply(this, {
            layout : new com.conjoon.layout.ResizableAccordionLayout(
                this.layoutConfig || {}
            )
        });

        this.addEvents(
            /**
             * @event validatedrop
             * @param overEvent|dropEvent
             */
            'validatedrop',
            /**
             * @event beforedragover
             * @param overEvent
             */
            'beforedragover',
            /**
             * @event dragover
             * @param overEvent
             */
            'dragover',
            /**
             * @event beforedrop
             * @param dropEvent
             */
            'beforedrop',
            /**
             * @event drop
             * @param dropEvent
             */
            'drop'
        );

        com.conjoon.dd.AccordionDropPanel.superclass.initComponent.call(this);
    },

    /**
     * Calls parent implementation and creates an instance of
     * {com.conjoon.dd.AccordionDropZone} for this instance.
     *
     */
    initEvents : function()
    {
        com.conjoon.dd.AccordionDropPanel.superclass.initEvents.call(this);
        this.dd = new com.conjoon.dd.AccordionDropTarget(this, this.dropConfig);
    },

    /**
     * Unregisters the dd-config and calls parent implementation.
     *
     */
    beforeDestroy: function()
    {
        if(this.dd){
            this.dd.unreg();
        }
        com.conjoon.dd.AccordionDropPanel.superclass.beforeDestroy.call(this);
    }

});