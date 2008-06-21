/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author$
 * $Id$
 * $Date$ 
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$ 
 */
 
Ext.namespace('de.intrabuild.groupware.email');

de.intrabuild.groupware.email.EmailViewPanel = Ext.extend(Ext.Panel, {
	
    /**
     * @cfg {Boolean} autoLoad
     * Overrides completely the functionality of the parents implementation.
     * If autoLoad is true, the according message will be loaded immediately
     * after this component was rendered, otherwise a blank panel will be rendered.
     * Set this to <tt>false</tt> to load messages manually for this panel. 
     */	
	
	/**
	 * @cfg {de.intrabuild.groupware.email.EmailItemRecord} emailItem The email item this
	 * panel represents. Will load the message body according to the record's id.
	 * If provided, the title will be automatically set to the value of the record's 
	 * subject field.
	 */

	/**
	 * @param {de.intrabuild.groupware.email._EmailView} view the view for this panel
	 * to render the message
	 */
	view : null,
	
	/**
	 * @param {Object} requestId The id of the current ajax request in progress
	 */
	requestId : null,
	
	/**
	 * @param {Ext.data.JsonReader} reader The reader for the email message loaded
	 */
	reader : null,
	
	/**
	 * @param {Boolean} viewReady Flag to help determine wether the view is rendered 
	 * or not.
	 */
	viewReady : false,
	
	/**
	 * @param {de.intrabuild.groupware.email.EmailRecord} emailRecord The last loaded 
	 * email record as specified in the id of emailItem
	 */
	emailRecord : null, 
	
	initComponent : function()
	{
	    Ext.apply(this, {
	        closable  : true,
	        iconCls   : this.iconCls || 'de-intrabuild-groupware-EmailView-Icon',
	        hideMode  : 'offsets',
	        listeners : de.intrabuild.groupware.util.LinkInterceptor.getListener()
	    });
	    
		this.addEvents(
			/**
			 * Gets fired before the ajax request for this panel gets started.
			 */
			'beforeemailload',
			/**
			 * Gets fired when the ajax request for this panel successfully 
			 * loaded the email message which is about to be displayed.
			 */
			'emailload'
		);
		
		if (this.emailItem) {
			this.title = this.emailItem.data.subject;
		}
		
		if (!this.reader) {
			this.reader = new Ext.data.JsonReader({
					root            : 'item',
					id              : 'id',
					successProperty : 'success'
				}, de.intrabuild.groupware.email.EmailRecord
			);	
		}
		
		de.intrabuild.groupware.email.EmailViewPanel.superclass.initComponent.call(this);	
	},
	
	/**
	 * Renders the view to display the current message.
	 *
	 */
	renderView : function()
	{
		if (this.emailRecord != null) {
			this.view.onEmailLoad(this.emailRecord);	
		}		
	},
	
	clearView : function()
	{
		if (this.viewReady) {
			this.view.clear();	
		}
		
		if (this.loadMask) {
			this.loadMask.hide();	
		}
		
		if (this.requestId) {
			Ext.Ajax.abort(this.requestId);	
			this.requestId = null;
		}
	},
	
	onRender : function(ct, position)
	{
        de.intrabuild.groupware.email.EmailViewPanel.superclass.onRender.apply(this, arguments);
        
        var view = new de.intrabuild.groupware.email._EmailView({templates : this.templates});
        view.init(this);

		view.render();
		
		this.view = view;
		
		this.viewReady = true;
    },
    
    
    // private
    initEvents : function()
    {
        de.intrabuild.groupware.email.EmailViewPanel.superclass.initEvents.call(this);

		if (this.loadMask)
        this.loadMask = new Ext.LoadMask(this.bwrap, this.loadMask);
    },
    
    // private
    onDestroy : function()
    {
        if(this.rendered){
            if(this.loadMask){
                this.loadMask.destroy();
            }
            var c = this.body;
            c.removeAllListeners();
            this.view.destroy();
            c.update("");
        }
        
        de.intrabuild.groupware.email.EmailViewPanel.superclass.onDestroy.call(this);
    },

    // private
    onResize : function()
    {
        de.intrabuild.groupware.email.EmailViewPanel.superclass.onResize.apply(this, arguments);
        
        if(this.viewReady){
            this.view.layout();
        }
    },

	/**
	 * Sets a new email item to for this panel to display
	 *
	 * @param {de.intrabuild.groupware.email.EmailItemRecord} emailItem The
	 * new emailItem this panel should represent
	 * @param {Boolean} suspendAutoLoad If set to true, the according message will not be loaded
	 *
	 */
	setEmailItem : function(emailItem, suspendAutoLoad)
	{
		this.emailItem = emailItem;
		this.setTitle(emailItem.data.subject);
		this.clearView();
		
		if (suspendAutoLoad === true) {
			return;	
		} 
		
		this.load();
	}, 

	/**
	 * Loads an email message into this panel.
	 *
	 * @param {Number} id The id of the email to load. If not provided,
	 * the id of the emailItem that was passed to the constriuctor will be used
	 * instead
	 */
    load : function(id)
    {
    	if (id == undefined) {
    		if (this.emailItem) {
    			id = this.emailItem.id;	
    		} else {
    			return;	
    		}
    	}
    	
    	if (this.fireEvent('beforeemailload', id) === false) {
    		return;	
    	}
    	
    	this.setTitle(/*@LNG*/"Loading...");
    	
    	if (this.loadMask) {
    		this.loadMask.show();	
    	}
    	
    	this.requestId = Ext.Ajax.request({
            url            : '/groupware/email/get.email/format/json',
            params         : {
                id  : id
            },
            success        : this.onEmailLoadSuccess, 
            failure        : this.onEmailLoadFailure,
            scope          : this,
            disableCaching : true
        });     
    },

    abortRequest : function()
    {
        if (this.requestId) {
            Ext.Ajax.abort(this.requestId);  
        }    
    },

    // private
    doAutoLoad : function()
    {
    	this.load();	
    },
    
	/**
	 * Callback for successfully retrieving an email
	 */    
	onEmailLoadSuccess : function(response, parameters)
	{
		var json = de.intrabuild.util.Json;
        
        var source = response.responseText;
        
		// first off, check the response value property for being 
		// an array.
        if (json.isError(source)) {
            this.onEmailLoadFailure(response, options);
            return;
        }           
        
        var result = this.reader.read(response);	
        
        if (!result.success) {
        	this.onEmailLoadFailure(response, parameters);	
        	return;
        }
        
        var record = result.records[0];
        
        this.emailRecord = record;
        
        if (this.loadMask) {
        	this.loadMask.hide(); 	
        }
        
        this.setTitle(record.data.subject);
        this.renderView();
        		
		this.fireEvent('emailload', record);
		
		this.requestId = null;
	},
	
	/**
	 * Callback for any error that occured during loading an email
	 */   
	onEmailLoadFailure : function(response, parameters)
	{
		if (this.loadMask) {
        	this.loadMask.hide(); 	
        }
        
		// shorthands
		var json = de.intrabuild.util.Json;
		var msg  = Ext.MessageBox;
		
		var error = json.forceErrorDecode(response);
		    
		msg.show({
		    title   : error.title || 'Error',
		    msg     : error.message,
		    buttons : msg.OK,
		    icon    : msg[error.level.toUpperCase()],
		    cls     :'de-intrabuild-msgbox-'+error.level,
		    width   : 400
		});
        
        this.requestId = null;
	}   
});


/**
 * A simple view for an email message. The view must not be delivered server-side,
 * instead everything gets configured over templates.
 *
 * WARING: The view assumes that the delivered data is already hmtl-encoded!
 *
 * @private
 */
 
de.intrabuild.groupware.email._EmailView = function(config){
    Ext.apply(this, config);
    de.intrabuild.groupware.email._EmailView.superclass.constructor.call(this);
};
 
 
 
Ext.extend(de.intrabuild.groupware.email._EmailView, Ext.util.Observable, {
	
	/**
	 * @param {Ext.Element} iframe The iframe that displays the message body.
	 */
	iframe : null,
	
	/**
	 * @param {HTMLElement} doc The document of the iframe
	 */
	doc : null, 
	
	/**
	 * @cfg {String} emptyMarkup The default content of teh iframe when there is no 
	 * message to display
	 */	
	emptyMarkup : '<html><head></head><body></body></html>',
	
	/**
	 * @cfg {de.intrabuild.groupware.email.EmailViewPanel} panel The panel
	 * to which this view is bound
	 */
	
	/**
	 * @cfg {Object} templates An object containing the templates for the message. 
	 * This view needs the following templates:
	 * <ul>
	 *  <li><strong>master</strong>: The body template in which the other templates get nested</li>
	 *  <li><strong>header</strong>: The header template in which subject, to, cc et.c get nested</li>
	 *  <li><strong>subject</strong>: The template for the subject-data of the email</li>
	 *  <li><strong>cc</strong>: The template for the cc addresses of the email</li>
	 *  <li><strong>bcc</strong>: The template for the bcc addresses of the email</li>
	 *	<li><strong>footer</strong>: The footer template in which the attachments get nested</li>
	 *  <li><strong>attachments</strong>: The template body for the attachment items</li>
	 *  <li><strong>attachmentItem</strong>: The template for a single attachment item</li>
	 *  </ul>
	 */
	
	/**
	 * @param {Boolean} cleared false if the message was rendered
	 */
	cleared : true, 
	 
    // private
    init: function(panel)
    {
        this.initTemplates();
        this.initData(panel);
        this.initUI(panel);
    },	
	
    // private
    render : function()
    {
        this.renderUI();
    },	
    
    // private
    renderUI : function()
    {
    	var ts = this.templates;
    	
        var header = "";
        var footer = "";
        var html = ts.master.apply({
        	header : header, 
        	footer : footer
        });
        
        var p = this.panel;
        p.body.dom.innerHTML = html;
    
        this.initElements();
    },    
    
    /**
     * Determines height of the iframe and sets style-attribute accordingly.
     */
    layout : function()
    {
    	var height = this.panel.body.getHeight(true);
    
    	var iframe = this.iframe.dom;
    	
    	var heightPrev = iframe.previousSibling ? Ext.fly(iframe.previousSibling).getHeight() : 0;
    	var heightNext = iframe.nextSibling ? Ext.fly(iframe.nextSibling).getHeight() : 0;
    	
    	var nHeight = height - (heightPrev + heightNext);
    	
    	iframe.style.height = (nHeight < 0 ? 0 : nHeight) + "px";
    },
    
    /**
     * Renders the view with the given data
     * @private
     */
    doRender : function(subject, from, to, cc, bcc, date, body, attachments)
    {
    	var ts = this.templates;
    	
    	var ccHtml = cc ? ts.cc.apply({
    		cc : cc
    	}) : "";
    	
    	var bccHtml = bcc ? ts.bcc.apply({
    		bcc : bcc
    	}) : "";
    	
    	var subjectHtml = ts.subject.apply({
    		subject : subject
    	});
    	
    	var header = ts.header.apply({
    		from 	: from,
    		to 		: to,
    		cc 		: ccHtml,
    		bcc 	: bccHtml,
    		date	: date,
    		subject : subjectHtml		
    	});
    	
    	var attachItemsHtml = "";
    	var attachHtml = "";
    	
        for (var i = 0, max_i = attachments.length; i < max_i; i++) {
            attachItemsHtml += ts.attachmentItem.apply({
                mimeIconCls : de.intrabuild.util.MimeIconFactory.getIconCls(attachments[i].mimeType),
                name        : attachments[i].fileName
            });
        }
        
        if (max_i > 0) {
            attachHtml = ts.attachments.apply({
                attachmentItems : attachItemsHtml
            });
        }    	
    	
    	var footer = ts.footer.apply({
    		attachments : attachHtml
    	});
    	
    	var DomHelperInsertHtml = Ext.DomHelper.insertHtml;
    	
        DomHelperInsertHtml('beforeBegin', this.iframe.dom, header);
        DomHelperInsertHtml('beforeEnd', this.el.dom, footer);
        
        var doc = this.doc;
    	doc.open();
        doc.write(body)
        doc.close(); 	
        
        this.cleared = false;
    },
    
    clear : function()
    {
    	var doc = this.doc;
    	doc.open();
        doc.write(this.emptyMarkup)
        doc.close(); 
        
    	var dom = this.iframe.dom;
    	var prev = dom.previousSibling;
    	var next = dom.nextSibling;
    	if (prev) {
    		prev.parentNode.removeChild(prev);		
    	}
    	if (next) {
    		next.parentNode.removeChild(next);		
    	}
        this.cleared = true;	
    },
    
    // private
    initElements : function()
    {
        var E = Ext.Element;
    
        var el = this.panel.body.dom.firstChild;
        var cs = el.childNodes;
    
        this.el = new E(el);
        
        var iframe = el.getElementsByTagName('iframe')[0];
        iframe.name = Ext.id();

		var doc;        
    	if(Ext.isIE){
            doc = iframe.contentWindow.document;
        } else {
            doc = (iframe.contentDocument || window.frames[iframe.name].document);
        }
        doc.open();
        doc.write(this.emptyMarkup)
        doc.close();    
    
    	this.doc = doc;
    	this.iframe = new E(iframe);
    	this.iframe.swallowEvent("click", true);
    },    
    
    // private
    initTemplates : function()
    {
        var ts = this.templates || {};
        
        if (!ts.master){
		    ts.master = new Ext.Template(
		             '<div style="height:100%">{header}',
		             '<iframe style="width:100%;border:0px;" frameborder="0" src="'+(Ext.SSL_SECURE_URL || "javascript:false")+'"></iframe>',
		             '{footer}</div>'
		    );
		}
	    
	    if (!ts.header) {
	    	ts.header = new Ext.Template(
	    			'<div class="de-intrabuild-groupware-email-EmailView-wrap">',
		               '<div class="de-intrabuild-groupware-EmailView-dataInset">',
		                '<span class="de-intrabuild-groupware-EmailView-date">{date:date("d.m.Y H:i")}</span>',               
		                '{subject}',
		                '<div class="de-intrabuild-groupware-EmailView-from"><div style="float:left;width:30px;">Von:</div><div style="float:left">{from}</div><div style="clear:both"></div></div>',
		                '<div class="de-intrabuild-groupware-EmailView-to"><div style="float:left;width:30px;">An:</div><div style="float:left">{to}</div><div style="clear:both"></div></div>',
		                '{cc}',
		                '{bcc}',
		               '</div>', 
		            '</div>'
	    	);
	    }
	    if (!ts.subject) {
		    ts.subject = new Ext.Template(
		        '<div class="de-intrabuild-groupware-EmailView-subject">{subject}</div>'
		    );
		}
	    
	    if (!ts.cc) {
		    ts.cc = new Ext.Template(
		        '<div class="de-intrabuild-groupware-EmailView-cc"><div style="float:left;width:30px;">CC:</div><div style="float:left">{cc}</div><div style="clear:both"></div></div>'
		    );
		}
	    
	    if (!ts.bcc) {
		    ts.bcc = new Ext.Template(
		        '<div class="de-intrabuild-groupware-EmailView-bcc"><div style="float:left;width:30px;">BCC:</div><div style="float:left">{bcc}</div><div style="clear:both"></div></div>'
		    );
		}	    
	    
	    if (!ts.footer) {
		    ts.footer = new Ext.Template(
		    	/**
		    	 * @CSS_PROBLEM
		    	 * problems with rendering div after iframe, somehow in Mozilla
		    	 * the iframe adds a bottom margin.
		    	 * tried with setting frameborder="yes", then the iframe height looks
		    	 * as expected, but not if the attribute is missing
		    	 * adding a margin-top -5px makes it look correct
		    	 */
		        '<table  cellspacing="0" cellpadding="0" border="0" style="width:100%;"><tr><td style="padding:2px;background-color:#F5F5F5;border-top:1px solid #99BBE8">',
		        '{attachments}',
		        '</td></tr></table>'
		    );
		}	    
	    
	    if (!ts.attachments) {
		    ts.attachments = new Ext.Template(
		            '<table cellspacing="0" cellpadding="0" border="0" style="width:100%"><tr>',
		                '<td style="width:60px;vertical-align:top;"><span style="font-family:Tahoma,Helvetica,Arial;font-size:11px;float:left;padding:2px;font-weight:bold;color:#15428B;">Anh&auml;nge:</span></td>',
		                '<td style="background:white;border:1px solid #767676;padding:2px;">',
		                '{attachmentItems}',
		            '</td></tr></table>'
		    );
		}
	    
	    if (!ts.attachmentItem) {
		    ts.attachmentItem = new Ext.Template(
		        '<a href="#" class="de-intrabuild-groupware-email-attachmentItem {mimeIconCls}">{name}</a>'
		    );        
		}
        
        for(var k in ts){
            var t = ts[k];
            if(t && typeof t.compile == 'function' && !t.compiled){
                t.disableFormats = false; // needed for correct date parsing
                t.compile();
            }
        }
    
        this.templates = ts;
    
    },  
    
    // private
    initUI : function(panel)
    {
    
    },    
    
    // private
    initData : function(panel)
    {
        if(this.panel){
            //this.panel.un('emailload', this.onEmailLoad, this);
            //this.panel.un('beforeemailload', this.onBeforeEmailLoad, this);
        }
        if(panel){
        	//panel.on('emailload', this.onEmailLoad, this);
        	//panel.on('beforeemailload', this.onBeforeEmailLoad, this);
        }
        this.panel = panel;
    },
    
    // private
    destroy : function()
    {
        this.initData(null);
    },
    
    onEmailLoad : function(record)
    {
    	var subject 	= record.data.subject 	  || undefined;
    	var from 		= record.data.from 		  || undefined;
    	var to  		= record.data.to 		  || undefined;
    	var cc 			= record.data.cc 		  || undefined;
    	var bcc 		= record.data.bcc 		  || undefined;
    	var date 		= record.data.date 		  || undefined;
    	var body 		= record.data.body		  || undefined;
    	var attachments = record.data.attachments || undefined;
    	
    	this.doRender(subject, from, to, cc, bcc, date, body, attachments);	
    	this.layout();
    },
    
    onBeforeEmailLoad : function(id)
    {
    	
    }
	
});