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

Ext.namespace('com.conjoon.groupware.forms');

com.conjoon.groupware.forms.QuickContactForm = function() {

    var _firstname = null;
    var _lastname = null;
    var _emailAddress = null;

    var _switchToEdit = null;

    var _submitButton = null;
    var _cancelButton = null;

    var _form = null;

    var _createLayout = function()
    {
        _form.add(_firstname);
        _form.add(_lastname);
        _form.add(_emailAddress);

        _form.add(_switchToEdit);

        _form.addButton(_saveButton);
        _form.addButton(_cancelButton);

    };

    var _initComponents = function()
    {
        _firstname    = new Ext.form.TextField({
            fieldLabel : com.conjoon.Gettext.gettext("First name"),
            name       : 'first',
            emptyText  : com.conjoon.Gettext.gettext("<First name>"),
            anchor     : '100%'
         });

        _lastname     = new Ext.form.TextField({
            fieldLabel : com.conjoon.Gettext.gettext("Last name"),
            emptyText  : com.conjoon.Gettext.gettext("<Last name>"),
            name       : 'true',
            anchor     : '100%'
        });


        _emailAddress = new Ext.form.TextField({
            fieldLabel : com.conjoon.Gettext.gettext("Email"),
            name       : 'email',
            emptyText  : com.conjoon.Gettext.gettext("<Email address>"),
            vtype      : 'email',
            anchor     : '100%'
        });

        _switchToEdit = new Ext.form.Checkbox({
            boxLabel : com.conjoon.Gettext.gettext("switch to edit mode"),
            ctCls    : 'com-conjoon-groupware-quickpanel-SmallEditorFont'
        });



        _saveButton   = new Ext.Button({
            text : com.conjoon.Gettext.gettext("Save")
        });
        _cancelButton = new Ext.Button({
            text : com.conjoon.Gettext.gettext("Cancel")
        });

        _createLayout.call(this);
    };

    return {

        getComponent : function()
        {
            if (_form !== null) {
                return _form;
            }

             _form = new Ext.FormPanel({
                labelWidth  : 0,
                buttonAlign : 'center',
                frame       : false,
                labelAlign  : 'left',
                title       : com.conjoon.Gettext.gettext("Contact"),
                bodyStyle   : 'background:#DFE8F6;padding:5px;',
                cls         : 'x-small-editor',
                labelPad    : 0,
                defaultType : 'textfield',
                hideLabels  : true
            });

            _form.on('beforerender', _initComponents, this, {single : true});

            return _form;
        },


        render : function()
        {
            if (_form.rendered) {
                return;
            }

            _form.render();

        }


    };


}();