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

Ext.namespace('com.conjoon.groupware.email');

/**
 * Default Email Options Dialog
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 * @class com.conjoon.groupware.email.EmailOptionsDialog
 * @extends com.conjoon.cudgets.settings.Dialog
 */
com.conjoon.groupware.email.EmailOptionsDialog = Ext.extend(
    com.conjoon.cudgets.settings.Dialog, {

    initComponent : function()
    {
        var me = this,
            readingKey = '/client/conjoon/modules/mail/options/reading/';

        Ext.apply(this, {



            settingsContainer : new com.conjoon.cudgets.settings.Container({
                ui : new com.conjoon.groupware.email.options.ContainerUi({
                    entryContainerHeight : 250
                }),
                storeSync : new com.conjoon.cudgets.data.StoreSync({
                    save : Ext.emptyFn,
                    orgStore  : new Ext.data.ArrayStore({
                        fields : ['id', 'name', 'optionName', 'preferredFormat', 'allowExternals'],
                        data : [[
                            1,
                            com.conjoon.Gettext.gettext("Reading"),
                            'reading',
                            com.conjoon.groupware.Registry.get(
                                readingKey+ 'preferred_format'
                            ),
                            com.conjoon.groupware.Registry.get(
                                readingKey + 'allow_externals'
                            )
                        ]]
                    }),
                    dataIndex : 'name'
                })
            }),
            ui : new com.conjoon.cudgets.settings.ui.DefaultDialogUi({
                title : com.conjoon.Gettext.gettext("Email Options"),
                actionListener : new com.conjoon.groupware.email.options.DialogListener
            }),
            width  : 500,
            height : 325

        });

        com.conjoon.groupware.email.EmailOptionsDialog.superclass.initComponent.call(this);
    }

});

