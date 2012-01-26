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

Ext.namespace('com.conjoon.groupware.reception');

/**
 * @class com.conjoon.groupware.reception.StateIndicator
 * @extends Ext.Component
 * A simple utility class representing a container for showing login processing states in a
 * {@see com.conjoon.groupware.reception.LoginWindow}
 * @constructor
 * @param {Object} config The configuration options.
 */
com.conjoon.groupware.reception.StateIndicator = Ext.extend(Ext.BoxComponent, {

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
     * @param {Ext.Element} _imageContainer
     * A container for showing larger images, such as a progess bar. The container is
     * rendered beneath the message container.
     */
    _imageContainer : null,

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
        image   : [],
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
                cls      : 'com-conjoon-groupware-reception-LoginWindow-stateIndicator',
                children : [{
                    tag : 'div',
                    cls : 'com-conjoon-groupware-reception-LoginWindow-stateIndicator-messageCont'
                }, {
                    tag : 'div',
                    cls : 'com-conjoon-groupware-reception-LoginWindow-stateIndicator-imageCont'
                }]
            }
        });

        this.on('render', this._onRender, this, {single : true});

        com.conjoon.groupware.reception.StateIndicator.superclass.initComponent.call(this);
    },

// -------- API

    setImageClass : function(cls)
    {
        this._resetContainerClass('image', cls);
    },

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
            case 'image':
                this._imageContainer.removeClass(this._appliedClasses['image']);
                this._appliedClasses['image'] = [];
                this._appliedClasses['image'].push(cls);
                this._imageContainer.addClass(cls);
            break;
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
        this._imageContainer   = new Ext.Element(this.el.dom.lastChild) ;
    }

});