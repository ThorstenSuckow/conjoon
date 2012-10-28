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

Ext.namespace('com.conjoon.iphone.service.twitter');

/**
 * Overwrites {com.conjoon.service.twitter.AccountButton} to add custom behavior.
 * An instance of this class will return a custom ExitMenuItem which will not be rendered
 * as disabled upon startup.
 *
 * @class com.conjoon.iphone.service.twitter.AccountButton
 * @extends com.conjoon.service.twitter.AccountButton
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
com.conjoon.iphone.service.twitter.AccountButton = Ext.extend(com.conjoon.service.twitter.AccountButton, {

    /**
     * Calls parent's implementation and returns a MenuItem which is not disabled
     * by default.
     *
     * @return {Ext.menu.Item}
     *
     * @protected
     */
    _getExitMenuItem : function()
    {
        var item = com.conjoon.iphone.service.twitter.AccountButton.superclass._getExitMenuItem.call(this);

        item.disabled = false;

        return item;
    }

});