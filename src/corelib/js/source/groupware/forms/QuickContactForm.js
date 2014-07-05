/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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
            text     : com.conjoon.Gettext.gettext("Save"),
            disabled : true
        });
        _cancelButton = new Ext.Button({
            text     : com.conjoon.Gettext.gettext("Cancel"),
            disabled : true
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