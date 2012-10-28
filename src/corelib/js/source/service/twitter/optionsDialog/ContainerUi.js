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

Ext.namespace('com.conjoon.service.twitter.optionsDialog');

/**
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
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
    removeMsg : com.conjoon.Gettext.gettext("Do you really want to remove the Twitter account \"{0}\"? Please make also sure to update your application settings at <a target=\"_blank\" href=\"{1}\">{1}</a> accordingly."),

    /**
     * Inits the layout of the container.
     * gets called from the initComponent's "initComponent()" method.
     *
     * @param {Ext.Container} container The container this ui will manage.
     */
    init : function(container)
    {
        if (this.container) {
            return;
        }

        this.additionalMessageValues = ["http://twitter.com/settings/applications"];

        com.conjoon.service.twitter.optionsDialog.ContainerUi
        .superclass.init.call(this, container);
    },

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