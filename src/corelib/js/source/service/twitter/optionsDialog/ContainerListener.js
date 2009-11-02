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

Ext.namespace('com.conjoon.service.twitter.optionsDialog');

/**
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
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
        var wizard = new com.conjoon.service.twitter.wizard.AccountWizard();
        wizard.show();
    }


});