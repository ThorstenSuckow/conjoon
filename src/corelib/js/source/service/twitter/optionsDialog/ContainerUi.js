/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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

Ext.namespace('com.conjoon.service.twitter.optionsDialog');

/**
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 *
 * @class com.conjoon.service.twitter.optionsDialog.ContainerUi
 * @extends com.conjoon.cudgets.ui.SettingsContainerUi
 */

com.conjoon.service.twitter.optionsDialog.ContainerUi = Ext.extend(
    com.conjoon.cudgets.settings.ui.DefaultContainerUi, {

    /**
     * @cfg {String} removeMsg The message to show if confirmBeforeRemove is set to true and the user
     * must confirm removing an entry.
     */
    removeMsg : com.conjoon.Gettext.gettext("Do you really want to remove the Twitter account \"{0}\"?"),

    /**
     * Returns an array with {Ext.FormPanel}s used to edit the
     * currently selected record.
     *
     * @return {Array}
     */
    buildFormCards : function()
    {
        return [
            new com.conjoon.service.twitter.optionsDialog.SettingsCard({
                settingsContainer    : this.container,
                enableStartEditEvent : true
            })
        ]
    },

    buildIntroductionCard : function()
    {
        return new Ext.BoxComponent({
            autoEl : {
                tag  : 'div',
                cls  : 'com-conjoon-service-twitter-optionsDialog-introCard',
                cn   : [{
                    tag  : 'div',
                    html : com.conjoon.Gettext.gettext("Twitter account management"),
                    cls  : 'headerLabel'
                },{
                    tag  : 'div',
                    cls  : 'com-conjoon-margin-t-10',
                    html : com.conjoon.Gettext.gettext("Please choose from the list of existing accounts or create a new Twitter account.")
                }]
            }
        });
    }

});