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

Ext.namespace('com.conjoon.service.twitter');

/**
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 * @class com.conjoon.service.twitter.OptionsDialog
 * @extends com.conjoon.cudgets.settings.Dialog
 */
com.conjoon.service.twitter.OptionsDialog = Ext.extend(
    com.conjoon.cudgets.settings.Dialog, {

    initComponent : function()
    {
        Ext.apply(this, {

            settingsContainer : new com.conjoon.cudgets.settings.Container({
                ui : new com.conjoon.service.twitter.optionsDialog.ContainerUi({
                    actionListener : new com.conjoon.service.twitter.optionsDialog.ContainerListener()
                }),
                storeSync : new com.conjoon.cudgets.data.StoreSync({
                    dataIndex : 'name',
                    orgStore  : com.conjoon.service.twitter.data.AccountStore.getInstance(),
                    api       : {
                        update  : com.conjoon.service.provider.twitterAccount.updateAccount,
                        destroy : com.conjoon.service.provider.twitterAccount.removeAccount
                    }
                })
            }),
            ui : new com.conjoon.cudgets.settings.ui.DefaultDialogUi({
                iconCls : 'twitterSettingsIcon',
                title   : com.conjoon.Gettext.gettext("Twitter Account Management")
            }),
            width  : 550,
            height : 375

        });

        com.conjoon.service.twitter.OptionsDialog.superclass.initComponent.call(this);
    }

});

