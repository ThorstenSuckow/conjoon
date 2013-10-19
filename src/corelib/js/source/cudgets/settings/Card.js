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

Ext.namespace('com.conjoon.cudgets.settings');

/**
 * A specific {@link Ext.FormPanel} that can be used as a card in a
 * {@link com.conjoon.cudgets.settings.Container}-component.
 *
 * @class com.conjoon.cudgets.settings.Card
 * @extends Ext.FormPanel
 *
 * @constructor
 * @param {Object} config The config object
 * @abstract
 */
com.conjoon.cudgets.settings.Card = Ext.extend(Ext.FormPanel, {

    /**
     * @cfg {Object} errorMessages Configures error messages for the
     * specified field names
     */

    /**
     * @type {Boolean} startEditInstalled true if the listeners that trigger the
     * the startedit event have been installed, otherwise false.
     */
    startEditInstalled : false,

    /**
     * Inits this component.
     */
    initComponent : function()
    {
        this.addEvents(
            /**
             * @event startedit
             * This event gets fired when editing started on any of the containing form
             * elements. Notice that the "enableStartEditEvent" has to be set to true in order
             * to activate this event.
             * In order for this event to get fired properly, make sure you have the
             * "enableKeyEvents" property activated on textfields/textareas.
             * The event listener get automatically uninstalled after the first event was detected
             * that triggers this event.
             * The event gets installed for the first time the card is shown. Thus, in the lifetime
             * of this card, the event should only fire once. If you want to reuse the event later on,
             * programmatically call "installStartEditListener".
             * @param {com.conjoon.cudgets.settings.Card} card The card that fired the event.
             * @param {Ext.Form.Field} field The field that triggered this event
             */
            'startedit'
        );


        if (!this.errorMessages) {
            this.errorMessages = {};
        }


        com.conjoon.cudgets.settings.Card.superclass.initComponent.call(this);
    },

// -------- overrides

    /**
     * Overrides parent implementation since we allow to add any element
     * in this component which must not be neccessarily be a form-element.
     * So before a call to "isValid()" is about to be made, this implementation
     * checks first if the specific item sitting in this component has a method "isValid" - if it
     * does not exists, it will be added on the fly.
     */
    bindHandler : function()
    {
        this.form.items.each(function(f){
            if(!f.isValid){
                f.isValid = Ext.emptyFn;
            }
        });

        return com.conjoon.cudgets.settings.Card.superclass.bindHandler.call(this);
    },

    /**
     * Overrides parent implementation. This is needed because in case
     * this method uses "monitorValid=true", the method "startMonitoring" must
     * not be called, until the "show"-event of this card fires.
     */
    initEvents : function()
    {
        var old = this.monitorValid;
        this.monitorValid = false;
        com.conjoon.cudgets.settings.Card.superclass.initEvents.call(this);
        this.monitorValid = old;

        this.on('show', this.onCardShow, this);
        this.on('hide', this.onCardHide, this);

        if (this.enableStartEditEvent) {
            this.on('afterrender', function() {
                this.installStartEditListener();
            }, this, {single : true});
        }
    },

// -------- listener

    /**
     * Stops monitoring the form elements in this component when the
     * 'hide'-event gets fired.
     */
    onCardHide : function()
    {
        this.stopMonitoring();
    },

    /**
     * Starts monitoring the form elements in this component when the
     * 'show'-event gets fired.
     *
     */
    onCardShow : function()
    {
        if (this.monitorValid) {
            this.startMonitoring();
        }
    },

// -------- API

    /**
     * Installs the listeners for this form items to be able to fire the
     * startedit event.
     *
     * @param {Boolean} install false to uninstall all previously added listener,
     * otherwise listener get installed.
     */
    installStartEditListener : function(install)
    {
        if (install === false && !this.startEditInstalled) {
            return;
        }

        if (!this.enableStartEditEvent || (this.startEditInstalled && install !== false)) {
            return;
        }

        if (install === false) {
            this.form.items.each(function(field) {
                this.mun(field, 'select',   this.processStartEditTrigger, this);
                this.mun(field, 'keydown',  this.processStartEditTrigger, this);
                this.mun(field, 'keyup',    this.processStartEditTrigger, this);
                this.mun(field, 'keypress', this.processStartEditTrigger, this);
                this.mun(field, 'check',    this.processStartEditTrigger, this);
            }, this);
        } else {
            this.form.items.each(function(field) {
                switch (true) {
                    // combobox
                    case (field instanceof Ext.form.ComboBox):
                        this.mon(field, 'select', this.processStartEditTrigger, this);
                    case (field.enableKeyEvents === true && field.editable === true):
                        this.mon(field, 'keydown',  this.processStartEditTrigger, this);
                        this.mon(field, 'keyup',    this.processStartEditTrigger, this);
                        this.mon(field, 'keypress', this.processStartEditTrigger, this);
                    break;

                    // textfields
                    case (field.enableKeyEvents === true):
                    case (field instanceof Ext.form.TextArea):
                    case (field instanceof Ext.form.TextField):
                        this.mon(field, 'keydown',  this.processStartEditTrigger, this);
                        this.mon(field, 'keyup',    this.processStartEditTrigger, this);
                        this.mon(field, 'keypress', this.processStartEditTrigger, this);
                    break;

                    // checkbox/radio
                    case (field instanceof Ext.form.Radio):
                    case (field instanceof Ext.form.Checkbox):
                        this.mon(field, 'check', this.processStartEditTrigger, this);
                    break;
                }
            }, this);
        }
        this.startEditInstalled = install === false ? false : true;
    },

    /**
     * Listens to various events from the configured fields in this panel and
     * fires the startedit event.
     *
     * @param {Ext.form.Field} field The field that triggered a specific event which in turn
     * fires the startedit event.
     */
    processStartEditTrigger : function(field)
    {
        this.fireEvent('startedit', this, field);
        this.installStartEditListener(false);
    },

    /**
     * Checks if all form fields in this panel are valid. Returns the first
     * field found that was not valid, or null, if all fields are valid.
     *
     * @return {Ext.form.Field}
     */
    getInvalidField : function()
    {
        var valid = null;
        this.form.items.each(function(f){
            if(!f.isValid(true)){
                valid = f;
                return false;
            }
        });

        return valid;
    },

    /**
     * Returns an error message for the specified field.
     *
     * @param {Ext.form.Field} field
     *
     * @return {String}
     */
    getErrorMessage : function(field)
    {
        return this.errorMessages[field.getName()];
    },

    /**
     * Sets the specified record for this card.
     * Subclasses are advised to implement appropriate logic, such as filling in formFields
     * when this method gets called. The default implementation looks up the form fields based
     * on the record's field names and fills the with the found value.
     *
     * @param {Ext.data.Record}
     */
    setRecord : function(record)
    {
        var fields = record.fields;

        this.form.items.each(function(f){
            var name  = f.getName();
            if (!fields.containsKey(name)) {
                throw('com.conjoon.cudgets.settings.Card.setRecord() was not overriden in subclass and could not find the field named "'+name+'" in the passed record\'s field list.');
            }
            f.setValue(record.get(name));
        });
    },

    /**
     * Writes the values from the form fields on this card into the specified record.
     * By default, this method tries to map the form-names to the record's fields and
     * write the data according to this mapping. This only works if
     *
     * @param {Ext.data.Record}
     */
    writeRecord : function(record)
    {
        var fields = record.fields;

        this.form.items.each(function(f){
            var value = f.getValue()
            var name  = f.getName();
            if (!fields.containsKey(name)) {
                throw('com.conjoon.cudgets.settings.Card.writeRecord() was not overriden in subclass and could not find the field named "'+name+'" in the passed record\'s field list.');
            }
            record.set(name, value);
        });
    }

});
