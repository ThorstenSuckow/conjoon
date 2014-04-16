/**
 * Ext.ux.layout.flexAccord.DropPanel
 * Copyright (c) 2009-2014, http://www.siteartwork.de
 *
 * Ext.ux.layout.flexAccord.DropPanel is licensed under the terms of the
 *                  GNU Open Source LGPL 3.0
 * license.
 *
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the LGPL as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the LGPL License for more
 * details.
 *
 * You should have received a copy of the GNU LGPL along with
 * this program. If not, see <http://www.gnu.org/licenses/lgpl.html>.
 *
 */

Ext.namespace('Ext.ux.layout.flexAccord');

/**
 * A special implemenation of a {Ext.Panel} that uses {Ext.ux.layout.flexAccord.DropTarget}
 * and {Ext.ux.layout.flexAccord.Layout} for allowing reorganizing and drag/drop.
 *
 * @class Ext.ux.layout.flexAccord.DropPanel
 * @extends Ext.Panel
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
Ext.ux.layout.flexAccord.DropPanel = Ext.extend(Ext.Panel, {

    /**
     * Inits this component.
     *
     */
    initComponent : function()
    {
        Ext.apply(this, {
            layout : new Ext.ux.layout.flexAccord.Layout(
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

        Ext.ux.layout.flexAccord.DropPanel.superclass.initComponent.call(this);
    },

    /**
     * Calls parent implementation and creates an instance of
     * {com.conjoon.dd.AccordionDropZone} for this instance.
     *
     */
    initEvents : function()
    {
        Ext.ux.layout.flexAccord.DropPanel.superclass.initEvents.call(this);
        this.dd = new Ext.ux.layout.flexAccord.DropTarget(this, this.dropConfig);
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
        Ext.ux.layout.flexAccord.DropPanel.superclass.beforeDestroy.call(this);
    }

});