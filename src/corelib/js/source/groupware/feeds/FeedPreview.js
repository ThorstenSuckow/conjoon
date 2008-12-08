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

Ext.namespace('de.intrabuild.groupware.feeds');

/**
 * Controller for previewing Feed contents.
 * This is a singleton-object and used byde.intrabuild.groupware.feeds.FeedGrid
 * to enable previewing a feed in a panel sliding out left of the grid panel,
 * aligned to the current selected cell. The panel is closable and draggable.
 * Once a panel was created, it can not be closed such that the object gets
 * destroyed.
 *
 * The preview panel depends on record properties passed from the grid to the
 * showPreview-method. The needed properties are
 *
 * <ul>
 *  <li>id - the id of the feed to preview</li>
 *  <li>title - the title of the feed</li>
 *  <li>link - the link of the feed  of the feed to preview</li>
 *  <li>pubDate - the publication date of the feed</li>
 * </ul>
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 * @copyright 2007 MindPatterns Software Solutions
 *
 */
de.intrabuild.groupware.feeds.FeedPreview = function() {

// {{{ private members

    var LinkInterceptor = de.intrabuild.groupware.util.LinkInterceptor;

    /**
     * The y position of the last clicked cell.
     * @param {Number} clkCellY
     */
    var clkCellY = 0;

    /**
     * Initial width of the preview panel.
     * @param {Number}
     */
    var width = 330;

    /**
     * Initial height of the preview panel.
     * @param {Number}
     */
    var height = 250;

    /**
     * The id of the ajax-request loading a feed-item
     * @param {Object} requestId
     */
    var requestId = null;

    /**
     * The loadMask used to mask the previewWindow.
     * @param {Ext.LoadMask} loadMask
     */
    var loadMask =null;

    /**
     * The latest record that was fully loaded into a preview panel.
     * @param {de.intrabuild.groupware.feeds.ItemRecord} lastRecord
     */
    var lastRecord = null;

    /**
     * Stores the id of the last previewed feed. If a preview panel gets closed,
     * the property will be reset to <tt>null</tt>.
     */
    var activeFeedId = null;

    /**
     * The html container that is responsible for enabling animation effects
     * of the preview panel.
     */
    var container = null;

    /**
     * The panel that is used for previewing a feed content. The property will
     * hold an instance of <tt>Ext.Panel</tt> which is being reused for previewing
     * until the panel was detached from the grid.
     */
    var previewPanel = null;

    /**
     * Any window that needs to be created after detaching a preview panel from
     * it's cell will be created using this window config.
     * @param {Ext.Window}
     */
    var tmpWindow = null;

    /**
     * Any preview panel that needs to be created uses a cloned version of this
     * tmpPreview property.
     * @param {Ext.Window}
     */
    var tmpPreview = null;

    /**
     * Stores the active cell to which the preview panel is aligned.
     */
    var clkCell = null;

    /**
     * Stores the row index of the cell to which the preview panel is aligned.
     */
    var clkRowIndex = -1;

    /**
     * Stores the record information of the cell's row associated with previewing.
     * The record needs to have a id-property that holds a unique id of the
     * grid's record that was selected.
     */
    var clkRecord = null;

    /**
     * The load configuration for reading out feeds. Will be used by the preview
     * panel and the window that gets created when the panel was detached from
     * the corresponding grid.
     */
    var loadConfig = {
        url: '/groupware/feeds/get.feed.content/format/json',
        discardUrl: false,
        nocache: true,
        text: de.intrabuild.Gettext.gettext("Please wait. Loading entry..."),
        timeout: 30,
        scripts: false
    };
// }}}

// {{{ private methods

    /**
     * Returns the first left outermost columnIndex of the first column
     * that is not hidden in the grid.
     * This method is guaranteed to return a value in the range of [0, columnIndex].
     *
     * @param {Ext.grid.GridPanel} grid
     * @param {Numbe} columnIndex
     *
     * @return {Number}
     */
    var _getColumnIndex = function(grid, columnIndex)
    {
        var colModel = grid.getColumnModel();
        i = 0;
        while (i < columnIndex) {
            if (!colModel.isHidden(i)) {
                return i;
            }
            i++;
        }

        return i;
    };

    /**
     * Inits any component that is needed for displaying/animating
     * the preview panel.
     * This method will only be called once.
     */
    var initComponents = function()
    {
        container = Ext.DomHelper.append(document.body, {
            id    : 'DOM:de.intrabuild.groupware.feeds.FeedPreview.container',
            style : "overflow:hidden;height:"+(height+5)+"px;width:"+width+"px"
        }, true);

    };

    /**
     * Callback.
     * Called when the preview panel's hide-animation is finished.
     */
    var onHide = function(skipAlign)
    {
        previewPanel.getUpdater().abort();
        previewPanel.setTitle(de.intrabuild.Gettext.gettext("Loading..."));
        previewPanel.body.update("");
        if (!skipAlign) {
            container.alignTo(clkCell, 'tr-tl');
        }
    };

    /**
     * Callback.
     * Called when the preview panel's show-animation is finished.
     */
    var onShow = function()
    {
        var viewHeight  = Ext.fly(document.body).getHeight();
        var panelHeight = previewPanel.el.getHeight();

        if (clkCellY + panelHeight > viewHeight) {
            container.shift({
                y : container.getY() - (((clkCellY + panelHeight) - viewHeight) + 4)
            });
        }
    };

    /**
     * Loads the feed's data into the preview panel.
     */
    var decoratePreviewPanel = function()
    {
        if (clkRecord == null) {
            return;
        }
        if (requestId !== null) {
            Ext.Ajax.abort(requestId);
        }
        loadMask.show();
        var feedTitle = clkRecord.get('title');
        previewPanel.setTitle(de.intrabuild.Gettext.gettext("Loading"));

        requestId = Ext.Ajax.request({
            url       : '/groupware/feeds/get.feed.content/format/json',
            params    : {id : clkRecord.id},
            success   : onLoadSuccess,
            failure   : onLoadFailure,
            disableCaching : true
        });
    };

    var onLoadSuccess = function(response, options)
    {
        requestId = null;
        var json = de.intrabuild.util.Json;

        var responseText = response.responseText;

        if (json.isError(responseText)) {
            onLoadFailure(response, options);
            return;
        }

        var values = json.getResponseValues(responseText);
        var item   = values.item;
        if (item == null) {
            onLoadFailure(response, options);
            return;
        }

        lastRecord = de.intrabuild.util.Record.convertTo(
            de.intrabuild.groupware.feeds.ItemRecord,
            item,
            item.id
        );

        Ext.ux.util.MessageBus.publish(
            'de.intrabuild.groupware.feeds.FeedPreview.onLoadSuccess', {
            id : item.id
        });

        previewPanel.setTitle(lastRecord.get('title'));
        previewPanel.body.update(lastRecord.get('content'));

        loadMask.hide();
    };

    var onLoadFailure = function(response, options)
    {
        de.intrabuild.groupware.ResponseInspector.handleFailure(response, {
            onLogin: {
                fn : function(){
                    decoratePreviewPanel();
                }
            }
        });
        previewPanel.close();
        loadMask.hide();
    };

    /**
     * Callback.
     * Called after the panel was detached from the grid and dropped anywhere
     * on the document body.
     * Sets <tt>previewPanel</tt> to <tt>null</tt> to notify the <tt>show</tt> method
     * to create a new preview panel.
     *
     */
    var onMove = function()
    {
        if (!lastRecord) {
            previewPanel.close();
            return;
        }

        var feedItem = lastRecord.copy();
        previewPanel.close();
        de.intrabuild.groupware.feeds.FeedViewBaton.showFeed(feedItem);
    };

    var visitFeedEntry = function()
    {
        if (!lastRecord) {
            return;
        }

        var link = lastRecord.get('link');

        (function() {
            this.open(LinkInterceptor.getRedirectLink(link));
        }).defer(1, window);
    };

    var openEntryInNewTab = function()
    {
        if (!lastRecord) {
            return;
        }

        var feedItem = lastRecord.copy();
        previewPanel.close();
        de.intrabuild.groupware.feeds.FeedViewBaton.showFeed(feedItem);
    };

    /**
     * Creates a window for displaying feed contents.
     *
     * @return {Ext.Window} The window used for previewing.
     */
    var createPreviewWindow = function()
    {
        var win = new Ext.Window({
            cls        : 'de-intrabuild-groupware-feeds-FeedPreview-panel',
            autoScroll : true,
            title      : de.intrabuild.Gettext.gettext("Loading..."),
            iconCls    : 'de-intrabuild-groupware-feeds-FeedPreview-Icon',
            resizable  : false,
            shadow     : false,
            height     : height,
            width      : width,
            listeners  : de.intrabuild.groupware.util.LinkInterceptor.getListener(),
            bbar       : [{
                cls      : 'x-btn-text-icon',
                iconCls  : 'de-intrabuild-groupware-feeds-FeedPreview-visitEntryButton-icon',
                text     : '&#160;'+de.intrabuild.Gettext.gettext("Visit entry"),
                handler  : visitFeedEntry
            }, {
                cls      : 'x-btn-text-icon',
                iconCls  : 'de-intrabuild-groupware-feeds-FeedPreview-openFeedButton-icon',
                text     : '&#160;'+de.intrabuild.Gettext.gettext("Open in new tab"),
                handler  : openEntryInNewTab
            }]
        });

        win.on('render', function() {
            this.header.on('dblclick', function(){
                onMove();
            });
        }, win);

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
    };

// }}}


    return {

        /**
         * Shows the preview panel using a slide-in animation effect.
         * The preview will not been shown if ctrl or shift was pressed while
         * calling this method.
         *
         * @param {Ext.grid.GridPanel} The grid panel that calls this method.
         * @param {Number} The row index of the cell the panel is aligned to.
         * @param {Number} The column index of the cell the panel is aligned to.
         * @param {Ext.EventObject} The raw event object that triggered this method.
         */
        show : function(grid, rowIndex, columnIndex, eventObject)
        {
            // ignore showPreview if the eventObject tells us that
            // shift or ctrl was pressed
            if (eventObject.shiftKey || eventObject.ctrlKey) {
                this.hide(false);
                return;
            }

            // get the record information of the current selected cell
            clkRecord = grid.getSelectionModel().getSelected();

            var pId = clkRecord.id;
            if (activeFeedId == pId) {
                // previewing is already active for this record.
                return;
            }

            // lazy create needed components
            if (container == null) {
                initComponents();
            }

            clkRowIndex = rowIndex;
            clkCell     = grid.view.getCell(rowIndex, _getColumnIndex(grid, columnIndex));
            clkCellY    = Ext.fly(clkCell).getY();

            if (previewPanel !== null) {
                // preview panel can be reused for previewing another feed.
                // abort all pending operations
                previewPanel.getUpdater().abort();

                previewPanel.el.stopFx();

                if (activeFeedId != null) {
                    // if the activeFeedId does not equal to zero, the
                    // previewPanel was hidden using the animation effect.
                    previewPanel.el.slideOut('r', {
                                        duration : .4,
                                        callback : function(){
                                            onHide();
                                            decoratePreviewPanel();},
                                        scope:this
                                   })
                                   .slideIn('r', {callback: onShow});
                } else {
                    // the preview panel was hidden using the hide method
                    // reshow and slide in.
                    container.setDisplayed(true);
                    container.alignTo(clkCell, 'tr-tl');
                    decoratePreviewPanel();
                    previewPanel.el.slideIn('r', {callback: onShow});
                }
            } else {
                previewPanel = createPreviewWindow();
                previewPanel.render(container);
                container.alignTo(clkCell, 'tr-tl');
                loadMask = new Ext.LoadMask(previewPanel.el.dom);
                previewPanel.show();
                decoratePreviewPanel();
                previewPanel.el.slideIn('r', {callback: onShow});

                previewPanel.on('beforeclose', this.hide, this, [true]);
                previewPanel.on('move', onMove);
            }

            activeFeedId = pId;
        },

        /**
         * Hides the preview panel.
         * Returns <tt>false</tt> to prevents bubbling the <tt>close</tt> event
         * to the Ext.Window based on the passed argument <tt>preventBubbling</tt>.
         *
         * @param {boolean} <tt>true</tt> to skip animation, <tt>false</tt>
         *                  to show.
         *
         * @todo update every call since second paramter is now deprecated!
         */
        hide : function(skipAnimation)
        {
            if (previewPanel == null || activeFeedId == null) {
                return;
            }
            if (!skipAnimation) {
                previewPanel.el.slideOut("r", {duration : .4,  callback : onHide});
            } else {
                container.setDisplayed(false);
                previewPanel.el.slideOut("r", {useDisplay : false, duration : .1});
                onHide(true);
            }

            activeFeedId = null;

            return false;
        }

    };

}();