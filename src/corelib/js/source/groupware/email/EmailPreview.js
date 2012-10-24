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

Ext.namespace('com.conjoon.groupware.email');

/**
 * Controller for previewing Feed contents.
 * This is a singleton-object and used bycom.conjoon.groupware.feeds.FeedGrid
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
 */
com.conjoon.groupware.email.EmailPreview = function() {

// {{{ private members

    /**
     * @type {Ext.Element} gridEl The element of the grid the preview is attached to
     */
    var gridEl = null;

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
     * The y position of the last clicked cell.
     * @param {Number} clkCellY
     */
    var clkCellY = 0;

    /**
     * Stores the id of the last previewed feed. If a preview panel gets closed,
     * the property will be reset to <tt>null</tt>.
     */
    var activeEmailId = null;

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


    var loadMask = null;

    var emailView = null;

    var lastRecord = null;

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
     * Callback.
     * Called when the preview panel's show-animation is finished.
     */
    var onShow = function()
    {
        emailView.show();
        var viewHeight  = Ext.fly(document.body).getHeight();
        var panelHeight = previewPanel.el.getHeight();

        if (clkCellY + panelHeight > viewHeight) {
            container.shift({
                y : container.getY() - (((clkCellY + panelHeight) - viewHeight) + 4)
            });
        }
    };

    var onBeforeLoad = function()
    {
        loadMask.show();
        emailView.hide();
    };

    var onLoadFailure = function(response, options)
    {
        com.conjoon.groupware.ResponseInspector.handleFailure(response, {
            onLogin: {
                fn : function(){
                    decoratePreviewPanel();
                }
            }
        });
        previewPanel.close();
        loadMask.hide();
    };

    var onLoadSuccess = function()
    {
        lastRecord = emailView.emailRecord;

        loadMask.hide();
        previewPanel.setTitle(lastRecord.get('subject'));
    };

    /**
     * Inits any component that is needed for displaying/animating
     * the preview panel.
     * This method will only be called once.
     */
    var initComponents = function()
    {
        container = Ext.DomHelper.append(document.body, {
            id    : 'DOM:com.conjoon.groupware.email.EmailPreview.container',
            style : "position:absolute;overflow:hidden;height:"+(height+5)+"px;width:"+width+"px"
        }, true);
    };

    /**
     * Callback.
     * Called when the preview panel's hide-animation is finished.
     */
    var onHide = function(skipAlign)
    {
        emailView.abortRequest();

        previewPanel.setTitle(com.conjoon.Gettext.gettext("Loading..."));

        if (skipAlign === true) {
            return;
        }

        refreshAnimSettings();
    };


    /**
     * Loads the feed's data into the preview panel.
     */
    var decoratePreviewPanel = function()
    {
        if (clkRecord == null) {
            return;
        }

        var subject = clkRecord.get('subject');
        var rec = com.conjoon.groupware.email.EmailViewBaton.getRecord(clkRecord.id);
        if (rec) {
            lastRecord = rec;
            emailView.emailRecord = rec;
            emailView.renderView();
        } else {
            emailView.setEmailItem(clkRecord)
        }

        previewPanel.setTitle(subject);
    }

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
        if (lastRecord == null) {
            previewPanel.close();
            return;
        }
        var emailItem = lastRecord.copy();
        previewPanel.close();
        var view = com.conjoon.groupware.email.EmailViewBaton.showEmail(emailItem, {
            autoLoad : false
        }, true);

    };

    /**
     * Creates a window for displaying feed contents.
     *
     * @return {Ext.Window} The window used for previewing.
     */
    var createPreviewWindow = function()
    {
        var templateConfig = {
            header : new Ext.Template(
                '<div class="wrap">',
                   '<div class="dataInset com-conjoon-groupware-email-EmailPreview-inset">',
                    '<span class="date">{date:date("d.m.Y H:i")}</span>',
                    '{subject}',
                    '<table border="0" cellspacing="0" cellpadding="0" class="headerTable">',
                    '{from}',
                    '{replyTo}',
                    '{to}',
                    '{cc}',
                    '{bcc}',
                    '</table>',
                   '</div>',
                '</div>'
        )};

        emailView = new com.conjoon.groupware.email.EmailViewPanel({
            autoLoad     : false,
            refreshFrame : true,
            border       :  false,
            hideMode     : 'visibility',
            viewConfig   : {
                templates        : templateConfig,
                fromValue        : com.conjoon.Gettext.gettext("From"),
                toValue          : com.conjoon.Gettext.gettext("To"),
                ccValue          : com.conjoon.Gettext.gettext("CC"),
                bccValue         : com.conjoon.Gettext.gettext("BCC")
            }
        });

        var win =  new Ext.Window({
            bodyStyle  : 'background:white;',
            autoScroll : false,
            layout     : 'fit',
            title      : ("Loading..."),
            iconCls    : 'com-conjoon-groupware-email-EmailPreview-Icon',
            resizable  : false,
            shadow     : false,
            hideMode   : 'visibility',
            items      : [emailView],
            height     : height,
            width      : width
        });

        win.mon(emailView, 'emailload',        onLoadSuccess, com.conjoon.groupware.email.EmailPreview);
        win.mon(emailView, 'beforeemailload',  onBeforeLoad, com.conjoon.groupware.email.EmailPreview);
        win.mon(emailView, 'emailloadfailure', onLoadFailure, com.conjoon.groupware.email.EmailPreview);

        win.on('render', function() {
            this.mon(this.header, 'dblclick', function(){
               onMove();
            }, this);
        }, win, {single : true});

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

    var animConfig = 'r';

    var refreshAnimSettings = function()
    {
        var x = gridEl.getX();

        // this should work in most cases - determine if the panel is rendered
        // in the left side of the workbench
        if (x <= 50) {
            x += gridEl.getSize().width;
            animConfig = 'l';
        } else {
            x -= container.getSize().width;
            animConfig = 'r';
        }

        container.setY(clkCellY);
        container.setX(x);
    };

// }}}


    return {

        getLastRecord : function()
        {
            return lastRecord;
        },

        getActiveRecord : function()
        {
            return clkRecord;
        },

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
        show : function(grid, record)
        {
            if (!record) {
                return;
            }
            clkRecord = record.copy();

            var rowIndex = grid.getStore().indexOf(record);

            var pId = clkRecord.id;
            if (activeEmailId == pId) {
                // previewing is already active for this record.
                return;
            }

            // lazy create needed components
            if (container == null) {
                initComponents.call(this);
            }

            clkRowIndex  = rowIndex;
            clkCell      = grid.view.getRow(rowIndex);
            clkCellY     = Ext.fly(clkCell).getY();
            gridEl       = grid.el;

            if (previewPanel !== null) {
                // preview panel can be reused for previewing another feed.
                // abort all pending operations
                previewPanel.el.stopFx();

                if (activeEmailId != null) {
                    // if the activeEmailId does not equal to zero, the
                    // previewPanel was hidden using the animation effect.
                    previewPanel.el.slideOut(animConfig, {
                                        duration : .4,
                                        useDisplay: false,
                                        callback : function(){
                                            onHide();
                                            decoratePreviewPanel();
                                            emailView.hide();
                                        },
                                        scope:this
                                   })
                                   .slideIn(animConfig, {callback : onShow, duration : .4, useDisplay: false});



                } else {
                    // the preview panel was hidden using the hide method
                    // reshow and slide in.
                    container.setDisplayed(true);
                    refreshAnimSettings();
                    decoratePreviewPanel();
                    previewPanel.el.slideIn(animConfig, {callback : onShow, duration : .4, useDisplay: false});
                }
            } else {
                refreshAnimSettings();
                previewPanel = createPreviewWindow();
                previewPanel.render(container);
                loadMask = new Ext.LoadMask(previewPanel.el.dom);
                previewPanel.show();
                decoratePreviewPanel();
                previewPanel.el.slideIn(animConfig, {callback : onShow, duration : .4, useDisplay: false});
                previewPanel.on('beforeclose', this.hide, this, [true, true]);
                previewPanel.on('move', onMove);
            }

            activeEmailId = pId;
        },

        isPreviewShownForRecord : function(record)
        {
            return record
                ? record.id === activeEmailId
                : false;
        },

        getCurrentlyShownRecordId : function()
        {
            return activeEmailId;
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
            if (previewPanel == null || activeEmailId == null) {
                return;
            }
            if (!skipAnimation) {
                previewPanel.el.slideOut(animConfig, {
                    useDisplay : false,
                    duration   : .4,
                    callback   : function(){
                        emailView.hide();
                        onHide();
                        container.setDisplayed(false);
                    }
                });
            } else {
                container.setDisplayed(false);
                previewPanel.el.slideOut(animConfig, {callback : function(){emailView.hide();}, useDisplay : false, duration : .1});
                onHide(true);
            }

            lastRecord   = null;
            activeEmailId = null;

            return false;
        }

    };

}();