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
