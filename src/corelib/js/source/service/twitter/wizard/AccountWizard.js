/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.service.twitter.wizard.AccountWizard
 * @extends Ext.ux.Wiz
 */
com.conjoon.service.twitter.wizard.AccountWizard = Ext.extend(Ext.ux.Wiz, {

    /**
     * Inits this component.
     */
    initComponent : function()
    {
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
                new com.conjoon.service.twitter.wizard.AccountCard(),
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
    onFinish : function()
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
            name     : values['name'],
            password : values['password']
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
        var rec = com.conjoon.util.Record.convertTo(
            com.conjoon.service.twitter.data.AccountRecord,
            accountObject, accountObject.id
        );

        com.conjoon.service.twitter.data.AccountStore.getInstance().addSorted(rec);
    }


});