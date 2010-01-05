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

Ext.namespace('com.conjoon.iphone.groupware.service');

/**
 * Overwrites {com.conjoon.groupware.service.TwitterPanel} to add custom behavior.
 * An instance of this class will return a custom AccountButton
 * ({com.conjoon.iphone.service.twitter.AccountButton}) and prevent the ExitMenuItem
 * of the AccountButton to be never rendered as disabled.
 *
 * @class com.conjoon.iphone.groupware.service.TwitterPanel
 * @extends com.conjoon.groupware.service.TwitterPanel
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.iphone.groupware.service.TwitterPanel = Ext.extend(com.conjoon.groupware.service.TwitterPanel, {

    /**
     * Listens to the exitclick event of the account button.
     * Calls the parent's implementation and renders the menuItem
     * enabled afterwards, instead of disabled.
     *
     * @param {com.conjoon.service.twitter.AccountButton} accountButton
     * @param {Ext.menu.Item} menuItem
     *
     * @protected
     */
    _onAccountButtonExitClick : function(accountButton, menuItem)
    {
        com.conjoon.iphone.groupware.service.TwitterPanel.superclass._onAccountButtonExitClick.call(this, accountButton, menuItem);
        menuItem.setDisabled(false);
    },

    /**
     * Returns a custom implementation of {com.conjoon.service.twitter.AccountButton}
     *
     * @return {com.conjoon.iphone.service.twitter.AccountButton}
     *
     * @protected
     */
    _getChooseAccountButton : function()
    {
        return new com.conjoon.iphone.service.twitter.AccountButton({
            accountStore : this.accountStore
        });
    },

    /**
     * Returns a custom implementation of {com.conjoon.service.twitter.HomePanel}.
     *
     * @return {com.conjoon.service.twitter.HomePanel}
     *
     * @protected
     */
    _getHomePanel : function()
    {
        return new com.conjoon.iphone.service.twitter.HomePanel();
    },

    _onShow : function()
    {
        this.accountStore.load();
    }

});