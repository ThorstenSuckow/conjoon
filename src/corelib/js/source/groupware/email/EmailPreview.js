/**
 * intraBuild 2.0 
 * Copyright(c) 2007, MindPatterns Software Solutions
 * licensing@mindpatterns.com
 */


Ext.namespace('de.intrabuild.groupware.email');

/**
 * Controller for previewing Feed contents.
 * This is a singleton-object and used byde.intrabuild.groupware.feeds.FeedGrid
 * to enable previewing a feed in a panel sliding out left of the grid panel, 
 * aligned to the current selected cell. The panel is closable and draggable. 
 * Upon drag, a new panel will be created and added to the document's body,
 * holding the same content as the preview panel, but behaves like a window.
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
de.intrabuild.groupware.email.EmailPreview = function() {

// {{{ private members    

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

	var emailViewListeners = [];

    var lastRecord = null;
    
	var emailPreviewFx = null;

// }}}



// {{{ private methods

    /**
     * Callback.
     * Called when the preview panel's show-animation is finished.
     */
    var onShow = function()
    {
        if (!previewPanel) {
            return;
        }
				
        var y           = Ext.fly(clkCell).getY();
        var viewHeight  = Ext.fly(document.body).getHeight();
        var panelHeight = previewPanel.el.getHeight();
        
        if (y + panelHeight > viewHeight) {
            container.shift({
                y : container.getY() - (((y + panelHeight) - viewHeight) + 4)  
            });
        } 
    };
    
    var onBeforeLoad = function()
    {
        if (previewPanel !== null) {
        	emailView.clearView();
            loadMask.show();
        }
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
		previewPanel = null;
        loadMask.hide();
	};
	
    var onLoad = function()
    {
        if (!previewPanel) {
            return;
        }
        
        loadMask.hide();
        previewPanel.setTitle(emailView.emailRecord.get('subject'));
        lastRecord = emailView.emailRecord;
    };

    /**
     * Inits any component that is needed for displaying/animating 
     * the preview panel.
     * This method will only be called once.
     */
    var initComponents = function()
    {
        container = Ext.DomHelper.append(document.body, {
			id    : 'DOM:de.intrabuild.groupware.email.EmailPreview.container',
			style : "overflow:hidden;height:"+(height+5)+"px;width:"+width+"px"
		}, true);
        
        emailPreviewFx = Ext.DomHelper.append(container, {
            style : "position:absolute;top:0;height:"+(height+5)+"px;width:"+width+"px;"
		}, true);
    };

    /**
     * Callback.
     * Called when the preview panel's hide-animation is finished.
     */
    var onHide = function(skipAlign)
    {
        previewPanel.setTitle('Loading...');
        
        if (skipAlign === true) {
            return;
        }
        
        container.alignTo(clkCell, 'tr-tl');
    };
    
    
    /**
     * Loads the feed's data into the preview panel.
     */
    var decoratePreviewPanel = function()
    {
        if (clkRecord == null || previewPanel == null) {
            return;
        }
      
        var subject = clkRecord.get('subject');
        emailView.setEmailItem(clkRecord)
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
		previewPanel = null;
    	var view = de.intrabuild.groupware.email.EmailViewBaton.showEmail(emailItem);
    };
    
    /**
     * Creates a window for displaying feed contents.
     *
     * @return {Ext.Window} The window used for previewing.
     */
    var createPreviewWindow = function()
    {
    	if (!emailView) {
    		var templateConfig = {
    			header : new Ext.Template(
	    			'<div class="de-intrabuild-groupware-email-EmailView-wrap">',
		               '<div class="de-intrabuild-groupware-EmailView-dataInset de-intrabuild-groupware-email-EmailPreview-inset">',
		                '<span class="de-intrabuild-groupware-EmailView-date">{date:date("d.m.Y H:i")}</span>',               
		                '{subject}',
		                '<div class="de-intrabuild-groupware-EmailView-from"><div style="float:left;width:30px;">Von:</div><div style="float:left">{from}</div><div style="clear:both"></div></div>',
		                '<div class="de-intrabuild-groupware-EmailView-to"><div style="float:left;width:30px;">An:</div><div style="float:left">{to}</div><div style="clear:both"></div></div>',
		                '{cc}',
		                '{bcc}',
		               '</div>', 
		            '</div>'
	    	)};
    		
    		emailView = new de.intrabuild.groupware.email.EmailViewPanel({
    			autoLoad  : false,
    			loadMask  : false,
    			border    : false,
    			hideMode  : 'offsets',
    			templates : templateConfig
    		});	
    		
    		emailView.on('emailload', onLoad, de.intrabuild.groupware.email.EmailPreview);
    		emailView.on('beforeemailload', onBeforeLoad, de.intrabuild.groupware.email.EmailPreview);
			emailView.on('emailloadfailure', onLoadFailure, de.intrabuild.groupware.email.EmailPreview);
    		
    		var lc = null;
    		for (var i = 0, len = emailViewListeners.length; i < len; i++) {
    			lc = emailViewListeners[i];
    			emailView.on(lc[0], lc[1], lc[2], lc[3]);	
    		}
    		
    	}
    	
        var win =  new Ext.Window({
            bodyStyle  : 'background:white;', 
            autoScroll : false, 
            layout 	   : 'fit',
            title      : 'Loading...', 
            iconCls    : 'de-intrabuild-groupware-email-EmailPreview-Icon', 
            resizable  : false, 
            shadow     : false, 
            hideMode   : 'offsets',
            items 	   : [emailView],
            height     : height,
            width      : width
        });
        
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
    	
    	on : function(event, fn, scope, parameters)
    	{
    		if (emailView) {
    			emailView.on(event, fn, scope, parameters);
    		} else {
    			emailViewListeners.push([event, fn, scope, parameters]);
    		}
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
        show : function(grid, rowIndex, columnIndex, eventObject)
        {
            // ignore showPreview if the eventObject tells us that
            // shift or ctrl was pressed
            if (eventObject.shiftKey || eventObject.ctrlKey) {
                this.hide(false, false);
                return;
            }
            
            // get the record information of the current selected cell
            clkRecord = grid.getSelectionModel().getSelected();
            
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
            clkCell      = grid.view.getCell(rowIndex-grid.view.rowIndex, columnIndex);
            
            if (previewPanel !== null) {
                // preview panel can be reused for previewing another feed.
                // abort all pending operations    
                emailPreviewFx.stopFx();
                
                if (activeEmailId != null) {
                    // if the activeEmailId does not equal to zero, the 
                    // previewPanel was hidden using the animation effect.
                    emailPreviewFx.slideOut('r', {
                    					wrap : emailPreviewFx,
                                        duration : .4, 
                                        useDisplay: false,
                                        callback : function(){
                                            onHide();
                                            decoratePreviewPanel();}, 
                                        scope:this
                                   })
                                   .slideIn('l', {callback : onShow, duration : .4, wrap : emailPreviewFx, useDisplay: false});
                } else {
                    // the preview panel was hidden using the hide method
                    // reshow and slide in.
                    previewPanel.show();
                    container.alignTo(clkCell, 'tr-tl');
                    decoratePreviewPanel();
                    emailPreviewFx.slideIn('l', {callback : onShow, duration : .4, wrap : emailPreviewFx, useDisplay: false});
                }
            } else {
                container.alignTo(clkCell, 'tr-tl');
                previewPanel = createPreviewWindow();
                previewPanel.render(emailPreviewFx);
                loadMask = new Ext.LoadMask(previewPanel.el.dom);
                previewPanel.show();
                decoratePreviewPanel();
                emailPreviewFx.slideIn('l', {callback : onShow, duration : .4, wrap : emailPreviewFx, useDisplay: false});
                
                previewPanel.on('beforeclose', this.hide, this, [true, true]);
                previewPanel.on('move', onMove);
            }
            
            activeEmailId = pId;
        },
        
        /**
         * Hides the preview panel.
         * Returns <tt>false</tt> to prevents bubbling the <tt>close</tt> event
         * to the Ext.Window based on the passed argument <tt>preventBubbling</tt>.
         *
         * @param {boolean} <tt>true</tt> to skip animation, <tt>false</tt>
         *                  to show.
         * @param {boolean} <tt>true</tt> to prevent bubbling the event up, 
                            otherwise <tt>false</tt>.
         */
        hide : function(skipAnimation, preventBubbling)
        {
            if (previewPanel == null || activeEmailId == null) {
                return;
            }
            if (!skipAnimation) {
                emailPreviewFx.slideOut("r", {wrap : emailPreviewFx, useDisplay : false, duration : .4,  callback : onHide});
            } else {
                previewPanel.hide();
                onHide(true);
            }
            
            lastRecord   = null;
            activeEmailId = null;    
            
            return preventBubbling ===  true ? false : true;
        }
        
    };
    
}();