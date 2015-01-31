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
 * @class com.conjoon.service.twitter.optionsDialog.ContainerListener
 *
 * @constructor
 */
com.conjoon.service.twitter.optionsDialog.ContainerListener = Ext.extend(
    com.conjoon.cudgets.settings.listener.DefaultContainerListener, {

    /**
     * @type {String} clsId
     */
    clsId : '209428aa-686d-48d6-9f19-ef82af7246be',


    /**
     * Listener for the addEntryButton click-event.
     *
     * @param {Ext.Button} button The button that triggered this event
     */
    onAddEntryButtonClick : function(button)
    {
        com.conjoon.service.twitter.wizard.AccountWizardBaton.show();
    }


});