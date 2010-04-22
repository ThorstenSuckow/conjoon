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

Ext.namespace('com.conjoon.cudgets.form');

/**
 * This class represents a button which wraps an input type "file", thus allowing
 * a user to choose files from a local file system. The component wraps a form
 * so references to the chosen file can be used later on for uploading.
 *
 *
 * @class com.conjoon.cudgets.form.HtmlFileChooserButton
 * @extends com.conjoon.cudgets.form.FileChooserButton
 *
 * @abstract
 */
com.conjoon.cudgets.form.HtmlFileChooserButton = Ext.extend(com.conjoon.cudgets.form.FileChooserButton, {

    /**
     * @type {Object} uploadButton The uploadButton for this form. Any component
     * is possible as long as it represents a html input element with its
     * attribute "type" set to "file". Take a look at the example folder that
     * comes with the ExtJS library, as you will find a FileUploadField there
     * which purpose is to render an Ext.Button on top of the html element.
     * The object needs to fire the "fileselected" event whenever the value of
     * the underlying input field changes, i.e. a new file was selected.
     */
    uploadButton : null,

    /**
     * @type {HtmlElement} form The Form HtmlElement
     */
    form : null,

    /**
     * @cfg {Object} buttonCfg An object with the following properties:
     *  buttonText
     *  iconCls
     */
    buttonCfg : null,

    /**
     * @type {Object} autoEl The autoEl for this BoxComponent which wraps
     * around uploadButton
     */
    autoEl : {
        tag      : 'div',
        cls      : 'com-conjoon-cudgets-form-FileUploadButtonForm',
        children : [{
            tag      : 'form',
            encoding : 'multipart/form-data',
            enctype  : 'multipart/form-data',
            method   : 'POST'
        }]
    },

// -------- listeners

    /**
     * Listener for the uploadButton's "fileselected" event.
     *
     * @param {Object} fileUploadButton The button thazt triggered the event
     * @parsam {String} fileName
     *
     * @protected
     */
    onFileSelected : function(fileUploadButton, fileName)
    {
        this.fireEvent('fileselected', this, fileName);
    },

// -------- Ext.BoxComponent

    /**
     * Inits this component.
     */
    initComponent : function()
    {
        this.addEvents(
            /**
             * Triggered when a file was added via the upoloadButton
             * @event add
             * @param this
             * @param fileName
             */
            'fileadd'
        );

        this.buttonCfg = this.buttonCfg || {};

        this.buttonCfg = Ext.applyIf(this.buttonCfg || {}, {
            text    : "Choose File..."
        });

        this.uploadButton = this.createUploadButton();

        this.uploadButton.on('fileselected', this.onFileSelected, this);

        com.conjoon.cudgets.form.HtmlFileChooserButton.superclass.initComponent.call(this);
    },

    /**
     * Forwards to the uploadButton to remove and properly destroy
     * this component, too.
     */
    onDestroy : function()
    {
        com.conjoon.cudgets.form.HtmlFileChooserButton.superclass.onDestroy.call(
            this
        );

        this.uploadButton.destroy();
    },


    /**
     * Calls parent implementation and renders the uploadButton as a child of the
     * form.
     */
    render : function(container, position)
    {
        com.conjoon.cudgets.form.HtmlFileChooserButton.superclass.render.call(
            this, container, position
        );

        this.form = this.el.dom.firstChild;

        this.uploadButton.render(this.form);

        return this;
    },

    /**
     * Overrides parent implementation by forwarding state setting to
     * the uploadButton.
     *
     * @return {com.conjoon.cudgets.form.FileUploadButtonForm} this
     */
    disable : function(silent)
    {
        this.uploadButton.disable(silent);

        return com.conjoon.cudgets.form.HtmlFileChooserButton.superclass.disable.call(
            this, silent
        );
    },

    /**
     * Overrides parent implementation by forwarding state setting to
     * the uploadButton.
     *
     * @return {com.conjoon.cudgets.form.FileUploadButtonForm} this
     */
    enable : function()
    {
        this.uploadButton.enable();

        return com.conjoon.cudgets.form.HtmlFileChooserButton.superclass.enable.call(
            this
        );
    },

    /**
     * Creates the UploadButton.
     * Based on the Upload Form Field as found in Ext JS examples 3.1.2
     */
    createUploadButton : function()
    {
        var buttonCfg = this.buttonCfg;

        return new Ext.form.TextField({
            enableKeyEvents : false,
            initComponent : function() {
                this.addEvents('fileselected');
                Ext.form.TextField.prototype.initComponent.call(this);
            },
            onRender : function(ct, position) {
                Ext.form.TextField.prototype.onRender.call(this, ct, position);

                    this.wrap = this.el.wrap({
                        cls:'x-form-field-wrap '
                            +'com-conjoon-cudgets-form-HtmlFileChooserButton'
                            +' wrap'
                    });
                    this.el.addClass('text');
                    this.el.dom.removeAttribute('name');
                    this.createFileInput();

                    this.button = new Ext.Button(Ext.apply(buttonCfg, {
                        renderTo: this.wrap,
                        cls: 'btn'
                             + ((buttonCfg.iconCls && buttonCfg.text)
                                ? ' x-btn-text-icon'
                                : (buttonCfg.iconCls ? ' x-btn-icon' : ''))
                    }));

                    this.el.hide();
                    this.wrap.setWidth(this.button.getEl().getWidth());
                    this.fileInput.setWidth(this.button.getEl().getWidth());

                    this.bindListeners();
                    this.resizeEl = this.positionEl = this.wrap;
            },
            createFileInput : function() {
                this.fileInput = this.wrap.createChild({
                    id   : this.getFileInputId(),
                    name : this.getId(),
                    cls  : 'input',
                    tag  : 'input',
                    type : 'file',
                    size : 1
                });
            },
            bindListeners: function() {

                this.wrap.on({
                    scope     : this,
                    mousemove : function(e) {
                        e.preventDefault();
                        var fi = this.fileInput;
                        var xy = e.getXY();
                        var x = xy[0]-Math.round(fi.getWidth()/2);
                        var y = xy[1]-Math.round(fi.getHeight()/2);
                        fi.setXY([x, y])
                    }
                });

                this.fileInput.on({
                    scope: this,
                    mouseenter: function() {
                        this.button.addClass(['x-btn-over','x-btn-focus'])
                    },
                    mouseleave: function() {
                        this.button.removeClass(['x-btn-over','x-btn-focus','x-btn-click'])
                    },
                    mousedown: function() {
                        this.button.addClass('x-btn-click')
                    },
                    mouseup: function() {
                        this.fileInput.setXY(this.wrap.getXY());
                        this.button.removeClass(['x-btn-over','x-btn-focus','x-btn-click'])
                    },
                    change: function() {
                        var v = this.fileInput.dom.value;
                        this.setValue(v);
                        this.fireEvent('fileselected', this, v);
                    }
                });
            },
            reset : function() {
                this.fileInput.remove();
                this.createFileInput();
                this.bindListeners();
                Ext.form.TextField.prototype.reset.call(this);
            },
            getFileInputId: function() {
                return this.id + '-file';
            },
            onResize : function(w, h) {
                Ext.form.TextField.prototype.onResize.call(this, w, h);

                this.wrap.setWidth(w);
            },
            onDestroy: function() {
                Ext.form.TextField.prototype.onDestroy.call(this);
                Ext.destroy(this.fileInput, this.button, this.wrap);
            },
            onDisable: function() {
                Ext.form.TextField.prototype.onDisable.call(this);
                this.doDisable(true);
            },
            onEnable: function() {
                Ext.form.TextField.prototype.onEnable.call(this);
                this.doDisable(false);

            },
            doDisable: function(disabled) {
                this.fileInput.dom.disabled = disabled;
                this.button.setDisabled(disabled);
            },
            preFocus : Ext.emptyFn,
            alignErrorIcon : function() {
                this.errorIcon.alignTo(this.wrap, 'tl-tr', [2, 0]);
            }

        }) // ^^

    },


// -------- com.conjoon.cudgets.form.FileChooserButton

    /**
     * Tells whether this button supports selecting multiple files at once.
     *
     * @return {Boolean} true if this button supports selecting multiple
     * files at once, otherwise false.
     *
     * @abstract
     */
    isMultipleFilesSupported : function()
    {
        return false;
    }

});