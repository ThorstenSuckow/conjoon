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

Ext.namespace('com.conjoon.cudgets.grid.ui');

/**
 * Builds and layouts the FilePanel's layout and its components.
 *
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.cudgets.grid.ui.DefaultFilePanelUi
 */
com.conjoon.cudgets.grid.ui.DefaultFilePanelUi = function(config) {

    config = config || {};

    Ext.apply(this, config);
};

com.conjoon.cudgets.grid.ui.DefaultFilePanelUi.prototype = {


    /**
     * @cfg {com.conjoon.cudgets.grid.listener.DefaultFilePanelListener} actionListener
     * The actionListener for the panel this ui class manages. If not provided,
     * defaults to {com.conjoon.cudgets.grid.listener.DefaultFilePanelListener}
     */
    actionListener : null,

    /**
     * @type {Ext.grid.EditorGridPanel} panel The panel this ui class manages. Gets assigned in the init()
     * method.
     */
    panel : null,

    /**
     * Inits the layout of the panel.
     * gets called from the initComponent's "initComponent()" method.
     *
     * @param {Ext.grid.EditoGridPanel} panel The panel this ui will manage.
     */
    init : function(panel)
    {
        if (this.panel) {
            return;
        }

        this.panel = panel;

        this.buildPanel();
        this.installListeners();
    },

    /**
     *
     * @protected
     */
    installListeners : function()
    {
        if (!this.actionListener) {
            this.actionListener = new com.conjoon.cudgets.grid.listener.DefaultFilePanelListener();
        }

        this.actionListener.init(this.panel);
    },

    /**
     *
     * @protected
     */
    buildPanel : function()
    {
        Ext.apply(this.panel, {
            cls : 'com-conjoon-cudgets-grid-FilePanel',
            sm  : new Ext.grid.RowSelectionModel({
                singleSelect : true
            })
        });
    },

    /**
     *
     * @return {com.conjoon.cudgets.grid.FilePanelContextMenu}
     *
     * @protected
     */
    buildContextMenu : function()
    {
        return new com.conjoon.cudgets.grid.FilePanelContextMenu();
    }
};