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
 * $Author: T. Suckow $
 * $Id: EmailGrid.js 1985 2014-07-05 13:00:08Z T. Suckow $
 * $Date: 2014-07-05 15:00:08 +0200 (Sa, 05 Jul 2014) $
 * $Revision: 1985 $
 * $LastChangedDate: 2014-07-05 15:00:08 +0200 (Sa, 05 Jul 2014) $
 * $LastChangedBy: T. Suckow $
 * $URL: http://svn.conjoon.org/trunk/src/corelib/js/source/groupware/email/EmailGrid.js $
 */

/*
 * A component responsible for rendering an error message into a live grid.
 * This component should be used for notifying a user of loadexceptions.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
Ext.defineClass('conjoon.mail.grid.comp.ErrorMessageComp', {

    extend : 'Ext.BoxComponent',

    /**
     * @cfg {String} text The text to render into the error message comp
     */
    text : 'Loading the grid failed.',

    /**
     * @cfg {String} message The text to use in the comp's control which invokes
     * the control action
     */
    controlText : 'Click here to try again.',

    /**
     * @inheritdoc
     * Defaults to 300
     */
    width : 300,

    /**
     * @cfg {String} controlSelector
     */
    controlSelector : 'span.control',

    /**
     * @inheritdoc
     */
    initComponent : function() {

        var me = this;

        me.autoEl = me.buildAutoEl();

        me.addEvents(
            /**
             * @event controlclick
             * Event gets fired if the control of the error message comp
             * is clicked.
             * @param {conjoon.mail.grid.comp.ErrorMessageComp} the comp that
             * triggered this event
             */
            'controlclick'
        );

        if (me.controlSelector) {
            me.on('afterrender', me.onCompAfterrender, me);
        }

        conjoon.mail.grid.comp.ErrorMessageComp.superclass.initComponent.apply(
            me, arguments
        );
    },

    /**
     * @private
     * This method should only be invoked if no BoxComponent is used that supports
     * the click-event. Specifying a path to the control via #controlSelector
     * will make sure that the click-event is bound to this component's element
     */
    onCompAfterrender : function() {

        var me = this;

        me.mon(me.getEl(), 'click', me.onInternalCompClick, me);

    },

    /**
     * @private
     */
    onInternalCompClick : function(evt, target) {
        var me = this;

        if (evt.getTarget(me.controlSelector)) {
            me.onCompClick();
        }
    },

    /**
     *
     */
    onCompClick : function() {
        var me = this;

        me.fireEvent('controlclick', me);
    },

    /**
     * Builds the autoEl for this component.
     *
     * @return {Object}
     */
    buildAutoEl : function() {

        var me = this;

        return {
            tag  : 'div',
            cls  : 'cn-mail-grid-errorComp',
            children : [{
                tag : 'span',
                html : me.text
            }, {
                tag : 'span',
                cls : 'control',
                html : me.controlText
            }]
        };
    },

    /**
     * Tries to center this component in its owning container.
     *
     * @param {Ext.Element} centerTo The container to which this component
     *                               should be aligned to.
     */
    center : function(centerTo) {
        this.getEl().center(centerTo);
    }

});
