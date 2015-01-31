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

    emptyText : "",

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