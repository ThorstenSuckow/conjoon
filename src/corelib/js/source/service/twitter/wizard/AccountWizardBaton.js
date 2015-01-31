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

Ext.namespace('com.conjoon.service.twitter.wizard');

/**
 * A baton for communicating with an actual existing Twitter account wizard.
 * It is recommended to use this baton to make sure that only one instance
 * during runtime is available, instead of instantiating the wizard directly.
 * Information from Twitter's oauth process can be passed to this baton which
 * will then get delegated to the according wizard's methods.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.service.twitter.wizard.AccountWizard
 * @extends Ext.ux.Wiz
 */
com.conjoon.service.twitter.wizard.AccountWizardBaton = function(){

    var _instance = null;

    var _destroy = function() {
        _instance = null;
    };


    return {

        /**
         * Creates a new wizard dialog or shows an existing one.
         *
         * @param {Object} options A config object with additional
         * configuration information for the show process. Valid
         * options are
         * - defer The number of ms the show process gets deferred. Usefull if
         *         the wizard whould be opened on top of a dialog which inits the
         *         call to show() during rendering
         */
        show : function(options)
        {
            if (!_instance) {
                _instance = new com.conjoon.service.twitter.wizard.AccountWizard();
                _instance.on('close', _destroy);
            }
            if (options && options.defer) {
                _instance.show.defer(options.defer, _instance);
            } else {
                _instance.show();
            }
        },

        /**
         * Method for telling the wizard that the account has been authorized.
         * The baton will delegate the necessary method calls to the wizard to
         * skip to it's finish card once the oauth process from Twitter has ended.
         * This will only happen if there is a current instance of the wizard
         * available.
         *
         * @param {Object} accountData An object with the data of the authorized
         *account
         */
        setData : function(accountData)
        {
            if (!_instance) {
                return;
            }
            _instance.accountCard.applyDataFromOauth(accountData);
        }

    };

}();