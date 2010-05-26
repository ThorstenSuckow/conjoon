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

Ext.namespace('com.conjoon.groupware.email.form.ui');

/**
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
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