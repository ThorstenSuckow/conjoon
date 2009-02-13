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

Ext.namespace('com.conjoon.service.twitter');

/**
 * A component for rendering a status update textarea.
 *
 * @class com.conjoon.service.twitter.InputBox
 * @extends Ext.BoxComponent
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.service.twitter.InputBox = Ext.extend(Ext.BoxComponent, {

    /**
     * @cfg {Object} autoEl
     */
    autoEl :  {
        tag      : 'div',
        cls      : 'com-conjoon-service-twitter-InputBox',
        children : [{
            tag : 'textarea',
            cls : 'textArea'
        }, {
            tag  : 'div',
            cls  : 'charCounter',
            html : '140'
        }, {
            tag  : 'div',
            cls  : 'buttonContainer'
        }]
    },

    /**
     * @cfg {Number} inputMaxLength The max length allowed for a status update.
     */
    inputMaxLength : 140,

    /**
     * @cfg {String} buttonTextBusy
     */

    /**
     * @type {Array} _replyStatus The id of the status this message is a reply to as the first
     * value, and the screenName of the author of the referred status as the second value.
     */
    _replyStatus : null,

    /**
     * @type {String} _buttonText
     */
    _buttonText : null,

    /**
     * @type {HtmlElement} _charCounter Native HtmlElement representing the
     * container that renders the remaining characters before inputMaxLength is
     * exceeded.
     * @protected
     */
    _charCounter : null,

    /**
     * @type {HtmlElement} _textArea Native HtmlElement representing the textarea
     * used for typing in the status update.
     * @protected
     */
    _textArea : null,

    /**
     * @type {Ext.Button} _updateButton The button rendered for sending the status
     * update as specified in _textArea's value.
     * @protected
     */
    _updateButton : null,

    /**
     * Inits this component.
     *
     */
    initComponent : function()
    {
        Ext.applyIf(this, {
            buttonTextBusy : com.conjoon.Gettext.gettext("Updating...")
        });

        this._buttonText = this.getUpdateButton().getText();

        com.conjoon.service.twitter.InputBox.superclass.initComponent.call(this);
    },

    /**
     * Overrides parent's implementation by initializing additional elements
     * for this component and attaching listeners.
     */
    afterRender : function()
    {
        com.conjoon.service.twitter.InputBox.superclass.afterRender.call(this);

        var dom = this.el.dom;

        this._charCounter = dom.lastChild.previousSibling;
        this._textArea    = dom.firstChild;

        this._updateButton = this.getUpdateButton();
        this._updateButton.render(dom.lastChild);

        Ext.fly(this._textArea).on({
            keydown  : this._onKeyEvent,
            keypress : this._onKeyEvent,
            keyup    : this._onKeyEvent,
            scope    : this
        });
    },


// -------- public API

    /**
     * Returns the status id/author this message referres to.
     * Returns "null" if the current message does not referr to any
     * status update.
     *
     * @return {Array}
     */
    getReplyStatus : function()
    {
        return this._replyStatus;
    },

    /**
     * Sets the status id/screenName this message referres to.
     * Pass "null" as an argument to reset the replyStatus.
     *
     * @param {Array} status first value is the statusId, second is the
     * referred authors screenName
     */
    setReplyStatus : function(status)
    {
        this._replyStatus = status;
    },

    /**
     * Renders this component or parts of this component busy, e.g.
     * if a transaction is on its way and there is now further input allowed.
     *
     * @param {Boolean} busy true, if the component should be rendered busy,
     * otehrwise false
     */
    setInputBusy : function(busy)
    {
        if (busy) {
            this._textArea.disabled = true;
            this._updateButton.setIconClass('busy');
            this._updateButton.setDisabled(true);
            this._updateButton.setText(this.buttonTextBusy);
        } else {
            this._textArea.disabled = false;
            this._updateButton.setIconClass('default');
            this._updateButton.setDisabled(false);
            this._updateButton.setText(this._buttonText);
        }

    },

    /**
     * Requests input focus for the _textArea element.
     */
    focus : function()
    {
        this._textArea.focus();
    },

    /**
     * Returns the value of _textArea.
     *
     * @return {String}
     */
    getMessage : function()
    {
        return this._textArea.value;
    },

    /**
     * Sets the value for the _textArea.
     *
     * @param {String} message
     */
    setMessage : function(message)
    {
        this._textArea.value = message;

        this.validateMessage();
    },

    /**
     * Validates the value of _textArea and adds additional
     * classes to _charCounter depending on the validity of the
     * value specified.
     * It is important that this method gets called after setReplyStatus
     * and setMessage, as it will compare any referred author and update the
     * replyStatus if necessary.
     */
    validateMessage : function()
    {
        var v      = this._textArea.value.trim();
        var length = v.length;

        this._updateButton.setDisabled(length == 0);
        if (length == 0) {
            this.setReplyStatus(null);
        }

        // make sure we reset _replyStatus even if
        // the current value does not start with a @, which would
        // indicate that we are referring to a status update
        if (v.indexOf('@') == 0) {
            var status = this.getReplyStatus();
            if (status !== null) {
                if (v.substring(1) != status[1] &&
                    v.substring(1, v.indexOf(" ")) != status[1]) {
                    this.setReplyStatus(null);
                }
            }
        } else {
            this.setReplyStatus(null);
        }

        var remaining = this.inputMaxLength - length;

        Ext.fly(this._charCounter).update(remaining);

        if (remaining <= 10) {
            Ext.fly(this._charCounter).removeClass('warning');
            Ext.fly(this._charCounter).addClass('error');
        } else if (remaining <= 20) {
            Ext.fly(this._charCounter).removeClass('error');
            Ext.fly(this._charCounter).addClass('warning');
        } else {
            Ext.fly(this._charCounter).removeClass('error');
            Ext.fly(this._charCounter).removeClass('warning');
        }
    },

    /**
     * Returns the button used for triggering a request to send
     * the status update specified in _textArea.
     *
     * @return {Ext.Button}
     */
    getUpdateButton : function()
    {
        if (!this._updateButton) {
            this._updateButton = this._getUpdateButton();
        }

        return this._updateButton;
    },

// -------- listener

    /**
     * Listens to the keydown/keypress/keyup event of the _textArea.
     * This implementation will immediately call validateMessage() when the
     * event(s) fire.
     */
    _onKeyEvent : function()
    {
        this.validateMessage();
    },

// -------- builder

    /**
     * Returns the update button for this component.
     * Override this to return a custom {Ext.Button}.
     *
     * @return {Ext.Button}
     *
     * @protected
     */
    _getUpdateButton : function()
    {
        return new Ext.Button({
            text     : com.conjoon.Gettext.gettext("Update"),
            cls      : 'x-btn-text-icon button',
            iconCls  : 'default',
            minWidth : 95,
            disabled : true
        });
    }
});