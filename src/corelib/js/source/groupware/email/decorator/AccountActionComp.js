/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: AccountStore.js 50 2008-07-29 22:16:55Z T. Suckow $
 * $Date: 2008-07-30 00:16:55 +0200 (Mi, 30 Jul 2008) $
 * $Revision: 50 $
 * $LastChangedDate: 2008-07-30 00:16:55 +0200 (Mi, 30 Jul 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/js/source/groupware/email/AccountStore.js $
 */

Ext.namespace('de.intrabuild.groupware.email.decorator');

/**
 * A singleton for changing the behaivor of items whos disable state is
 * dependend on the state of {@see de.intrabuild.groupware.email.AccountStore}.
 *
 *
 * @class de.intrabuild.groupware.email.decorator.AccountActionComp
 * @singleton
 */
de.intrabuild.groupware.email.decorator.AccountActionComp = function() {


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
                _store = de.intrabuild.groupware.email.AccountStore.getInstance();
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