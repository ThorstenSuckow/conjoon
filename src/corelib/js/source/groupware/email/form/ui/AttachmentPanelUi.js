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

Ext.namespace('com.conjoon.groupware.email.form.ui');

/**
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.cudgets.grid.ui.DefaultFilePanelUi
 */
com.conjoon.groupware.email.form.ui.AttachmentPanelUi = Ext.extend(com.conjoon.cudgets.grid.ui.DefaultFilePanelUi, {

    /**
     *
     * @protected
     */
    buildPanel : function()
    {
        com.conjoon.groupware.email.form.ui.AttachmentPanelUi.superclass
        .buildPanel.call(this);

        Ext.apply(this.panel, {
            split       : true,
            region      : 'east',
            width       : 200,
            hideHeaders : true,
            hidden      : true,
            title       : com.conjoon.Gettext.gettext("Attachments:"),
            viewConfig  : {
                forceFit    : true,
                markDirty   : false,
                getRowClass : function(record, index)
                {
                    return 'attachmentRow';
                }

            }
        });
    }
});