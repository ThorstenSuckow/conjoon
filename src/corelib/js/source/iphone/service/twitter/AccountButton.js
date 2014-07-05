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