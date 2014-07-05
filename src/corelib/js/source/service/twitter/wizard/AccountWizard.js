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

Ext.namespace('com.conjoon.service.twitter.wizard');

/**
 * Do not create instances of this class directly, instead, use the singleton
 * com.conjoon.service.twitter.wizard.AccountWizardBaton, since functionality
 * in this implementation will open new browser windows, which will then refer
 * to methods in this class. The browser window is unaware of instances of this
 * class and needs to communicate with the baton to get things working right.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.service.twitter.wizard.AccountWizard
 * @extends Ext.ux.Wiz
 */
com.conjoon.service.twitter.wizard.AccountWizard = Ext.extend(Ext.ux.Wiz, {

    accountCard : null,

    /**
     * Inits this component.
     */
    initComponent : function()
    {
        this.accountCard = new com.conjoon.service.twitter.wizard.AccountCard();

        Ext.apply(this, {
            cards : [
                new Ext.ux.Wiz.Card({
                    title        : com.conjoon.Gettext.gettext("Welcome"),
                    header       : false,
                    border       : false,
                    monitorValid : false,
                    items        : [{
                        border    : false,
                        bodyStyle : 'background-color:#F6F6F6;',
                        html      : '<div style="margin-top:20px;">'
                                    +com.conjoon.Gettext.gettext("This wizard will guide you through the steps necessary to import an existing Twitter account.<br />Once this account is imported, you can tweet right away!")
                                    +'</div>'
                    }]
                }),
                this.accountCard,
                new com.conjoon.service.twitter.wizard.FinishCard()
            ],
            cls          : 'com-conjoon-service-twitter-wizard-AccountWizard',
            title        : com.conjoon.Gettext.gettext("Twitter account assistant"),
            headerConfig : {
                title : com.conjoon.Gettext.gettext("Import an existing Twitter account")
            }
        });

        com.conjoon.service.twitter.wizard.AccountWizard.superclass.initComponent.call(this);
    },

    /**
     * Callback for the "finish" button. Collects all form values and sends them
     * to the server to create a new twitter account.
     */
    onFinish : function(data)
    {
        var values = {};
        var formValues = {};
        for (var i = 0, len = this.cards.length; i < len; i++) {
            formValues = this.cards[i].form.getValues(false);
            for (var a in formValues) {
                values[a] = formValues[a];
            }
        }

        this.switchDialogState(false);

        var myObj = {
            success        : this.onAddSuccess,
            failure        : this.onAddFailure,
            scope          : this
        };

        com.conjoon.service.provider.twitterAccount.addAccount({
            name             : values['name'],
            twitterId        : values['twitterId'],
            oauthToken       : values['oauthToken'],
            oauthTokenSecret : values['oauthTokenSecret']
        }, function (result, remotingObject) {
            Ext.apply(myObj, {
                result         : result,
                remotingObject : remotingObject
            });
            com.conjoon.groupware.ResponseInspector.doCallbackForDirectApiResponse(myObj);
        });

    },

    /**
     * Callback for a succesfull ajax request.
     * successfull in this context means, that the result property has been returned
     * and that the type is set to rpc.
     * However, the result property may contain more information about a problem
     * that might have occured on the server, such as not being able to connect
     * to the twitter service, or any db failure.
     *
     * @param {Object} result The result property as returned by the server
     */
    onAddSuccess : function(result)
    {
        this.switchDialogState(true);

        if (result.length === 0 || (result.success === undefined)) {
            // result property empty or success property not available
            com.conjoon.SystemMessageManager.error(new com.conjoon.SystemMessage({
                type  : com.conjoon.SystemMessage.TYPE_ERROR,
                text  : com.conjoon.Gettext.gettext("An error occured while trying to add a Twitter account. No further error information is available."),
                title : com.conjoon.Gettext.gettext("Error - Add Twitter account")
            }));

            return;
        } else if (result.success === false && result.connectionFailure === true) {
            // no connection to the twitter API
            com.conjoon.SystemMessageManager.error(new com.conjoon.SystemMessage({
                type  : com.conjoon.SystemMessage.TYPE_CONFIRM,
                text  : String.format(
                    com.conjoon.Gettext.gettext("An error occured while trying to connect to the Twitter service. The account could not be verified. Please try again. {0}"),
                    result.error
                    ? String.format(
                        com.conjoon.Gettext.gettext("The server returned the following message: <br /><br />{0}"),
                        result.error.message
                      )
                    : ""
                ),
                title : com.conjoon.Gettext.gettext("Error - Verifying Twitter account")
            }));

            return;
        } else if (result.success === false) {
            if (result.error) {
                com.conjoon.SystemMessageManager.error(new com.conjoon.SystemMessage({
                    type  : com.conjoon.SystemMessage.TYPE_ERROR,
                    text  : error.message,
                    title : result.error.title
                            ? result.error.title
                            : com.conjoon.Gettext.gettext("Error - Add Twitter account")
                }));
            } else {
                com.conjoon.SystemMessageManager.error(new com.conjoon.SystemMessage({
                    type  : com.conjoon.SystemMessage.TYPE_ERROR,
                    text  : com.conjoon.Gettext.gettext("An error occured while trying to add a Twitter account. No further error information is available."),
                    title : com.conjoon.Gettext.gettext("Error - Add Twitter account")
                }));

                return;
            }
        }

        this.addAccount(result.account);
        this.close();
    },

    /**
     * Callback for an errorneous Ajax request. This method gets called
     * whenever the request couldn't be processed, due to connection or
     * server problems.
     *
     * @param {Object} remotingObject The remotingObject as returned by the server.
     */
    onAddFailure : function(response)
    {
        this.switchDialogState(true);

        com.conjoon.groupware.ResponseInspector.handleFailure(response);
    },

    /**
     * Adds the account as returned by the server to the accoutn store.
     *
     * @param {Object} accountObject The raw account object which has to be converted
     * to an instance of com.conjoon.service.twitter.data.AccountRecord first.
     *
     * @private
     */
    addAccount : function(accountObject)
    {
        var ns    = com.conjoon.service.twitter.data;
        var store = ns.AccountStore.getInstance();

        var rec = com.conjoon.util.Record.convertTo(
            ns.AccountRecord,
            accountObject, accountObject.id
        );

        if (store.getById(accountObject.id)) {
            return;
        }

        store.addSorted(rec);
    }


});