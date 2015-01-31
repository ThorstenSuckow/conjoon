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

Ext.namespace('com.conjoon.cudgets.grid.plugin.dataPreview.ui');

/**
 * An implementation of a DataPreview ui that renders the preview data into
 * in window that slides out to the left/right of the grid, depending on its
 * position, and aligns itself to the row that was clicked.
 * The rendered window is draggable.
 *
 * +-----+
 * |_____|+-------+
 * |__1__||   2   |
 * |_____||       |
 * |_____|+-------+
 * |_____|
 * |_____|
 * |_____|
 * +-----+
 *
 *  "1" denotes the grid where the plugin is bound to. As soon as a rowselect
 *  is triggered by it's selection model, the preview window will slide out to
 *  the right or to the left of the grid, giving an extended preview of the
 *  data the row represents.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @extends com.conjoon.cudgets.grid.plugin.dataPreview.ui.PreviewRenderer
 *
 * @abstract
 *
 * @class com.conjoon.cudgets.grid.plugin.dataPreview.ui.SlideWindowPreview
 */
com.conjoon.cudgets.grid.plugin.dataPreview.ui.SlideWindowPreview = Ext.extend(
    com.conjoon.cudgets.grid.plugin.dataPreview.ui.PreviewRenderer, {

    /**
     * The height of the window.
     * @cfg {Number} height
     */
    height : 250,

    /**
     * The width of the window.
     * @cfg {Number} width
     */
    width : 330,

    /**
     * The html container that is responsible for enabling animation effects
     * of the preview panel.
     * @type {Ext.Element} container
     */
    container : null,

    /**
     * Caches the grid which is using the plugin
     * @type {Ext.grid.GridPanel}
     */
    grid : null,

    /**
     * The animation configuration, i.e. in which direction the window slides
     * out. Will be changed during the runtime depending on the position of the
     * grid.
     * @type {String}
     */
    animConfig : 'r',

    /**
     * The y position of the grid row which shows the record that is about to
     * be previewed.
     * @type {Number}
     */
    rowY : null,

    /**
     * A refrence to the last record that has been successfully shown in the
     * preview panel.
     * @type {Ext.data.Record}
     */
    lastRecord : null,

    /**
     * @inheritdoc
     */
    init : function(plugin)
    {
        com.conjoon.cudgets.grid.plugin.dataPreview.ui
            .SlideWindowPreview.superclass.init.call(this, plugin);

        this.grid = this.plugin.getGrid();
    },

    /**
     * @inheritdoc
     */
    paintUi : function()
    {
        // intentionally left empty as we will lazy render
    },

    /**
     * Returns true if the panel is currently busy rendering the data.
     * This could be for example if data has to be queried from the backend
     * which has not finished yet.
     * @return {Boolean}
     */
    isRenderingProcessBusy : Ext.emptyFn,

    /**
     * Aborts any process related with the preview, for example an AJAX request
     * querying data from a server.
     *
     */
    abortRenderingProcess : Ext.emptyFn,

    /**
     * Decorates the preview panel with actual data.
     */
    decoratePreviewPanel : Ext.emptyFn,

    /**
     * @inheritdoc
     */
    repaintPreview : function()
    {
        // preview panel can be reused for previewing other data
        this.abortRenderingProcessIfBusy();

        this.previewComponent.el.stopFx();

        if (this.activePreviewId != null) {
            // if the activePreviewId does not equal to null, the
            // previewComponent was hidden using the disappear = false flag
            this.previewComponent.el.slideOut(this.animConfig, {
                duration : .4,
                callback : function(){
                    this.resetPreviewPanelBody();
                    this.decoratePreviewPanel();},
                scope:this
            }).slideIn(
                this.animConfig, {
                    callback : this.adjustContainerPosition,
                    scope    : this
            });
        } else {
            // the preview panel was hidden using the hide method
            // reshow and slide in.
            this.container.setDisplayed(true);
            this.refreshAnimSettings();
            this.decoratePreviewPanel();
            this.previewComponent.el.slideIn(
                this.animConfig, {
                    callback : this.adjustContainerPosition,
                    scope    : this
            });
        }
    },

    /**
     * @inheritdoc
     */
    paintPreview : function()
    {
        if (!this.container) {
            this.container = Ext.DomHelper.append(document.body, {
                style : "position:absolute;overflow:hidden;height:"
                    +(this.height+5)+"px;width:"+this.width+"px"
            }, true);
        }

        var previewPanel = this.createPreviewWindow();

        // install listeners before component gets rendered
        this.actionListener.installListenerForPreviewWindow(previewPanel);

        previewPanel.render(this.container);

        this.refreshAnimSettings();

        previewPanel.show();
        this.decoratePreviewPanel();
        previewPanel.el.slideIn(
            this.animConfig, {
                callback : this.adjustContainerPosition,
                scope    : this
            }
        );

        return previewPanel;
    },

    /**
     * @inheritdoc
     */
    hidePreview : function(disappear)
    {
        if (this.previewComponent == null || this.activePreviewId == null) {
            return;
        }
        if (!disappear) {
            this.previewComponent.el.slideOut(this.animConfig, {
                duration : .4,
                callback : function(){
                    this.resetPreviewPanelBody();
                    this.container.setDisplayed(false);
                },
                scope : this
            });

        } else {
            this.container.setDisplayed(false);
            this.previewComponent.el.slideOut(
                this.animConfig, {
                    useDisplay : false,
                    duration   : .1
            });
            this.resetPreviewPanelBody(true);
        }

        this.activePreviewId = null;
    },

    /**
     * @inheritdoc
     */
    harvestRecordInformation : function(record)
    {
        var g        = this.grid;
        var rowIndex = g.getStore().indexOf(record);

        this.rowY = Ext.fly(g.view.getRow(rowIndex)).getY();
    },

    /**
     * Returns the last record that was successfully loaded into the preview
     * panel.
     */
    getLastRecord : function()
    {
        return this.lastRecord;
    },

// -------- helper

    /**
     * Creates a window for displaying feed contents.
     *
     * @return {Ext.Window} The window used for previewing.
     */
    createPreviewWindow : function()
    {
        var win = new Ext.Window({
            cls        : 'com-conjoon-groupware-feeds-FeedPreview-panel',
            autoScroll : true,
            title      : com.conjoon.Gettext.gettext("Loading..."),
            iconCls    : 'com-conjoon-groupware-feeds-FeedPreview-Icon',
            resizable  : false,
            shadow     : false,
            height     : this.height,
            width      : this.width,
            //listeners  : com.conjoon.groupware.util.LinkInterceptor.getListener(),
            bbar       : [{
                cls      : 'x-btn-text-icon',
                iconCls  : 'com-conjoon-groupware-feeds-FeedPreview-visitEntryButton-icon',
                text     : '&#160;'+com.conjoon.Gettext.gettext("Visit entry")//,
               // handler  : visitFeedEntry
            }, {
                cls      : 'x-btn-text-icon',
                iconCls  : 'com-conjoon-groupware-feeds-FeedPreview-openFeedButton-icon',
                text     : '&#160;'+com.conjoon.Gettext.gettext("Open in new tab")//,
               // handler  : openEntryInNewTab
            }]
        });

        var container = this.container;

        win.initDraggable = function() {
            Ext.Window.prototype.initDraggable.call(this);

            this.dd.b4Drag = function(e) {
                container.dom.style.overflow = "visible";
            };

            this.dd.endDrag = function(e){
                this.win.unghost(true, false);
                this.win.setPosition(0, 0);
                this.win.saveState();
                container.dom.style.overflow = "hidden";
            };
        }

        return win;
    },

    /**
     * Refreshes the settings related to animation of the container that's used
     * to let the previewComponent slide in/out.
     */
    refreshAnimSettings : function()
    {
        var gridEl = this.grid.el;
        var x      = gridEl.getX();

        // this should work in most cases - determine if the panel is rendered
        // in the left side of the workbench
        if (x <= 50) {
            x += gridEl.getSize().width;
            this.animConfig = 'l';
        } else {
            x -= this.container.getSize().width;
            this.animConfig = 'r';
        }

        // align the container
        this.container.setY(this.rowY);
        this.container.setX(x);
    },

    /**
     * Adjusts the container position if the rowY value does not allow the
     * window to be displayed as it would be clipped by the lower bounds of the
     * system's native (browser) window.
     *
     */
    adjustContainerPosition : function()
    {
        var viewHeight  = Ext.fly(document.body).getHeight();
        var panelHeight = this.previewComponent.el.getHeight();

        if (this.rowY + panelHeight > viewHeight) {
            this.container.shift({
                y : this.container.getY() -
                    (((this.rowY + panelHeight) - viewHeight) + 4)
            });
        }
    },

    /**
     * Resets the previewComponent's body to a default state.
     *
     * @param {Boolean} skipAlign true to not re-align the container to the
     * grid, otherwise false
     */
    resetPreviewPanelBody : function(skipAlign)
    {
        this.abortRenderingProcessIfBusy();

        this.previewComponent.setTitle(com.conjoon.Gettext.gettext("Loading..."));
        this.previewComponent.body.update("");
        if (!skipAlign) {
            this.refreshAnimSettings();
        }
    }

});
