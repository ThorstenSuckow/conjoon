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

Ext.namespace('com.conjoon.service.twitter');

/**
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
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
                    entryContainerHeight : 190,
                    actionListener       : new com.conjoon.service.twitter.optionsDialog.ContainerListener()
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
                iconCls        : 'twitterSettingsIcon',
                title          : com.conjoon.Gettext.gettext("Twitter Account Management"),
                actionListener : new com.conjoon.service.twitter.optionsDialog.DialogListener()
            }),
            width  : 500,
            height : 325

        });

        com.conjoon.service.twitter.OptionsDialog.superclass.initComponent.call(this);
    }

});

