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

Ext.namespace('com.conjoon.service.twitter.wizard');

/**
 * A baton for communicating with an actual existing Twitter account wizard.
 * It is recommended to use this baton to make sure that only one instance
 * during runtime is available, instead of instantiating the wizard directly.
 * Information from Twitter's oauth process can be passed to this baton which
 * will then get delegated to the according wizard's methods.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
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