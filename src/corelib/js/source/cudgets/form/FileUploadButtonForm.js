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
 * A FileUploadForm is a form that's used as a wrapper for a FileUploadButton
 * as found in the examples directory that come with ExtJS 3.1.2.
 * The form will render in any component that can properly size BoxComponents
 * and the uploadButton as passed to the configuration will be the only
 * visible UI component.
 *
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.cudgets.form.FileUploadButtonForm
 * @extends Ext.BoxComponent
 */
com.conjoon.cudgets.form.FileUploadButtonForm = function(config) {

    Ext.apply(this, config);

    com.conjoon.cudgets.form.FileUploadButtonForm.superclass.constructor.call(this);
};


Ext.extend(com.conjoon.cudgets.form.FileUploadButtonForm, Ext.BoxComponent, {

    /**
     * @cfg {Object} uploadButton The uploadButton for this form. Any component
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
        this.fireEvent('fileadd', this, fileName);
    },



// -------- Ext.BoxComponent

    /**
     * Forwards to the uploadButton to remove and properly destroy
     * this component, too.
     */
    onDestroy : function()
    {
        com.conjoon.cudgets.form.FileUploadButtonForm.superclass.onDestroy.call(
            this
        );

        this.uploadButton.destroy();
    },

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

        this.uploadButton.on('fileselected', this.onFileSelected, this);
    },

    /**
     * Calls parent implementation and renders the uploadButton as a child of the
     * form.
     */
    render : function(container, position)
    {
        com.conjoon.cudgets.form.FileUploadButtonForm.superclass.render.call(
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

        return com.conjoon.cudgets.form.FileUploadButtonForm.superclass.disable.call(
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

        return com.conjoon.cudgets.form.FileUploadButtonForm.superclass.enable.call(
            this
        );
    }



});