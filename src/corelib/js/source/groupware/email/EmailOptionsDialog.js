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
            readingKey = '/client/conjoon/modules/email/options/reading/';

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

