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
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */

Ext.namespace('com.conjoon.groupware.email.decorator');

/**
 * A singleton for changing the behaivor of items whos disable state is
 * dependend on the state of {@see com.conjoon.groupware.email.AccountStore}.
 *
 *
 * @class com.conjoon.groupware.email.decorator.AccountActionComp
 * @singleton
 */
com.conjoon.groupware.email.decorator.AccountActionComp = function() {


    var _store = null;

    return {

        /**
         * Decorates a component that is dependent of account related data with
         * overwriting the setDisabled method and adding listeners regarding the
         * storage state of the saccount store.
         * The returned component will be set to disabled if no data is in the account
         * store and vice versa. Additionally, the set disabled method will save the
         * requested disable state an either enable the button when accounts get added
         *
         *
         * @param {Ext.Component} comp
         *
         * @return {Ext.Component}
         */
        decorate : function(comp)
        {
            if (_store === null) {
                _store = com.conjoon.groupware.email.AccountStore.getInstance();
            }

            comp.__setDisabled = comp.setDisabled;

            comp.setDisabled = function(disabled, ignoreState)
            {
                if (ignoreState !== true && _store.getCount() == 0) {
                    comp.__isDisabled = disabled;
                    disabled = true;
                }

                comp.__setDisabled(disabled);
            };

            var onShow = function() {
                if (this.disabled) {
                    return;
                }
                this.setDisabled((_store.getCount() == 0), true);
            };

            var onAdd = function(store, records, index) {
                this.setDisabled(this.__isDisabled);
            };

            var onRemove = function(store, record, index) {
                this.setDisabled(this.__isDisabled);
            };

            comp.on('render', onShow, comp);
            _store.on('add', onAdd, comp);
            _store.on('remove', onRemove, comp);

            comp.on('destroy', function() {
                _store.un('add',    onAdd,    this);
                _store.un('remove', onRemove, this);
            }, comp);

            return comp;
        }

    };

}();