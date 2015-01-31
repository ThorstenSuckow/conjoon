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
 * $Id: StateIndicator.js 1457 2012-10-28 18:55:17Z T. Suckow $
 * $Date: 2012-10-28 19:55:17 +0100 (So, 28 Okt 2012) $
 * $Revision: 1457 $
 * $LastChangedDate: 2012-10-28 19:55:17 +0100 (So, 28 Okt 2012) $
 * $LastChangedBy: T. Suckow $
 * $URL: http://svn.conjoon.org/trunk/src/corelib/js/source/groupware/reception/StateIndicator.js $
 */

/**
 * @class conjoon.reception.comp.StateIndicator
 * @extends Ext.Component
 * A simple utility class representing a container for showing login processing states in a
 * {@see conjoon.reception.comp.LoginContainer}
 * @constructor
 * @param {Object} config The configuration options.
 */
Ext.defineClass('conjoon.reception.comp.StateIndicator', {

    extend : 'Ext.BoxComponent',

    /**
     * @cfg {String} errorCls
     * The default class for displaying error messages.
     */
    errorCls : 'error',

    /**
     * @cfg {String} messageCls
     * Teh default class for displaying messages.
     */
    messageCls : 'message',

    /**
     * @param {Ext.Element} _messageContainer
     * A container for displaying messages.
     */
    _messageContainer : null,

    /**
     * @param {Object}
     * An object containing all classes that have been added to the image- and
     * message elements
     */
    _appliedClasses : {
        message : []
    },

    /**
     * Inits this component.
     */
    initComponent : function()
    {
        Ext.apply(this, {
            autoEl : {
                tag      : 'div',
                cls      : 'cn-reception-stateIndicator',
                children : [{
                    tag : 'div',
                    cls : 'messageCont'
                }]
            }
        });

        this.on('render', this._onRender, this, {single : true});

        conjoon.reception.comp.StateIndicator.superclass.initComponent.call(this);
    },

// -------- API

    setErrorMessage : function(message)
    {
        this._resetContainerClass('message', this.errorCls);
        this._messageContainer.update(message);
    },

    setMessage : function(message, cls)
    {
        this._resetContainerClass('message', (cls ? cls : this.messageCls));
        this._messageContainer.update(message);
    },

// -------- helper
    /**
     * Resets either the message or image container to only use its default classes.
     */
    _resetContainerClass : function(type, cls)
    {
        switch (type) {
            case 'message':
                this._messageContainer.removeClass(this._appliedClasses['message']);
                this._appliedClasses['message'] = [];
                this._appliedClasses['message'].push(cls);
                this._messageContainer.addClass(cls);
            break;
        }

    },

// -------- listeners

    /**
     * Listener for he "render" event.
     *
     */
    _onRender : function()
    {
        this._messageContainer = new Ext.Element(this.el.dom.firstChild);
    }

});