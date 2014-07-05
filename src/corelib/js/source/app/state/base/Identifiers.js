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

Ext.namespace('conjoon.state.base');

/**
 * An object which contains identifiers to be used as stateIds
 * for various components in conjoon.
 *
 * @Object com.conjoon.state.base.Identifiers
 */
conjoon.state.base.Identifiers = {

    emailModule : {

        contentPanel : {
            mailItemsGrid : '/emailModule/contentPanel/mailItemsGrid',
            previewButton : '/emailModule/contentPanel/previewButton',
            rightPreview : '/emailModule/contentPanel/rightPreview',
            bottomPreview  : '/emailModule/contentPanel/bottomPreview',
            folderTree : '/emailModule/contentPanel/folderTree'
        }

    },

    workbench : {

        widgets   : {
            quickPanelWidget : '/workbench/widgets/quickPanelWidget',
            twitterWidget    : '/workbench/widgets/twitterWidget',
            emailWidget      : '/workbench/widgets/emailWidget',
            feedWidget       : '/workbench/widgets/feedWidget'
        },

        panels : {
            eastPanel    : '/workbench/panels/eastPanel',
            westPanel    : '/workbench/panels/westPanel',
            contentPanel : '/workbench/panels/contentPanel'
        }

    }

};
