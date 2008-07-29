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
 
de.intrabuild.groupware.email.EmailAccountWizard = Ext.extend(Ext.ux.Wiz, {
	
	/**
	 * @param {Object} requestId The current id of the ajax request that stores the 
	 * form values.
	 */
	requestId : null,
	
	/**
	 * Inits this component.
	 */
	initComponent : function()
	{
	
		this.cards = [
            new Ext.ux.Wiz.Card({
    			title		 : de.intrabuild.Gettext.gettext("Willkommen"),
    			header		 : false,
    			border		 : false,
    			monitorValid : false,
    			items		 : [{
    				border	  : false,
    				bodyStyle : 'background-color:#F6F6F6;',
    				html	  : '<div style="margin-top:20px;">'
					            +de.intrabuild.Gettext.gettext("You need to have an email account configured for sending and receiving email messages.<br /><br />This assistant will guide you through the neccessary steps for collecting the needed account information. If you are unsure about specific information asked by this assistant, please contact your email provider.")
								+'</div>'	
    			}]
            }),		
			new de.intrabuild.groupware.email.EmailAccountWizardNameCard(),
			new de.intrabuild.groupware.email.EmailAccountWizardserverInboxCard(),
			new de.intrabuild.groupware.email.EmailAccountWizardServerOutboxCard(),
			new de.intrabuild.groupware.email.EmailAccountWizardAccountNameCard(),
			new de.intrabuild.groupware.email.EmailAccountWizardFinishCard()
		];
		this.cls = 'de-intrabuild-groupware-email-EmailAccountWizard-panelBackground';
		this.title        = de.intrabuild.Gettext.gettext("Email account assistant");
		this.headerConfig = {
		    title : de.intrabuild.Gettext.gettext("Create a new Email-Account")    
		};
		
		de.intrabuild.groupware.email.EmailAccountWizard.superclass.initComponent.call(this);
	},
	
	/**
	 * Callback for the "finish" button. Collects all form values and sends them to the server
	 * to create a new email account.
	 */
	onFinish : function()
	{
		var values = {};
		var formValues = {};
		for (var i = 0, len = this.cards.length; i < len; i++) {
			formValues = this.cards[i].form.getValues(false);
			for (var a in formValues) {
				values[a] = formValues[a];	
			}
		}
		
		values['isOutboxAuth'] = values['isOutboxAuth'] == 'on' ? true : false;
		
		this.switchDialogState(false);
		
        this.requestId = Ext.Ajax.request({
            url            	  : '/groupware/email/add.email.account/format/json',
            params         	  : values,
            success        	  : this.onAddSuccess, 
            failure        	  : this.onAddFailure,
            scope          	  : this
        });		
	},
	
	/**
	 * Callback for a succesfull ajax request. Successfull means that the server
	 * could handle the request and that no connection problems occured.
	 * The response may report a failure though, due to connection problems
	 * to the database or similiar. 
	 * The response will return the interger-value of the newly added account in
	 * the database, otherwise it will be empty or hoolding an error message.
	 * If the key of the newly created data is being returned, the account will be 
	 * added to the accountstore.
	 *
	 * @param {Object} response The response object returned by the server.
	 * @param {Object} options The options used to initiate the request.
	 */
	onAddSuccess : function(response, options)
	{
		var json = de.intrabuild.util.Json;
		
		// first off, check the response if it contains any error
        if (json.isError(response.responseText)) {
            this.onAddFailure(response, options);
            return;
        }     		
        
        // fetch the response values
        var responseValues = json.getResponseValues(response.responseText);
        var account = responseValues.account;
        var accountStore = de.intrabuild.groupware.email.AccountStore.getInstance();
        
        var rec = new de.intrabuild.groupware.email.AccountRecord(
            account
        );
        rec.id = account.id;
         
        accountStore.addSorted(rec); 
        
        this.switchDialogState(true);
        this.requestId = null;
        this.close();	
	},
	
	/**
	 * Callback for an errorneous Ajax request. This method gets called
	 * whenever the request couldn't be processed, due to connection or 
	 * server problems.
	 *
	 * @param {Object} response The response object returned by the server.
	 * @param {Object} options The options used to initiate the request.
	 */
	onAddFailure : function(response, options)
	{
		this.switchDialogState(true);		
		
		de.intrabuild.groupware.ResponseInspector.handleFailure(response);
		
		this.requestId = null;
	}
	
	
});

de.intrabuild.groupware.email.EmailAccountWizardNameCard = Ext.extend(Ext.ux.Wiz.Card, {
	
	nameField : null,
	addressField : null,
	
	initComponent : function()
	{
		this.monitorValid = true;
	
		this.baseCls    = 'x-small-editor';
		this.labelWidth	= 80;
        
		this.defaultType = 'textfield';
		this.title = de.intrabuild.Gettext.gettext("Personal data");
		this.defaults = {
            labelStyle : 'width:80px;font-size:11px',
            anchor: '100%'
         };
        
        
        this.nameField = new Ext.form.TextField({
    		fieldLabel : de.intrabuild.Gettext.gettext("Your name"),
    		allowBlank : false,
    		name	   : 'userName'
    	});
         
        this.addressField = new Ext.form.TextField({
    		fieldLabel : de.intrabuild.Gettext.gettext("Email addresse"),
    		allowBlank : false,
    		validator  : Ext.form.VTypes.email,
    		name	   : 'address'
    	});
	
		this.items = [
			new de.intrabuild.groupware.util.FormIntro({
        		style   : 'margin:10px 0 5px 0;',
        		label	: de.intrabuild.Gettext.gettext("Personal data"),
        		text	: "Specify your real name and your email address here. This information will be visible to the recipients of your messages."	
        	}),
        	this.nameField,	
        	this.addressField
		];
        	
		de.intrabuild.groupware.email.EmailAccountWizardNameCard.superclass.initComponent.call(this);
	}
});  

de.intrabuild.groupware.email.EmailAccountWizardserverInboxCard = Ext.extend(Ext.ux.Wiz.Card, {
	
	hostField	  : null,
	usernameField : null,
	passwordField : null,
	accountStore  : null,
	
	initComponent : function()
	{
		this.monitorValid = true;
		this.accountStore = de.intrabuild.groupware.email.AccountStore.getInstance();
	
	
		this.baseCls    = 'x-small-editor';
		this.labelWidth	= 100;
        
		this.defaultType = 'textfield';
		this.title = de.intrabuild.Gettext.gettext("Inbox server");
		this.defaults = {
            labelStyle : 'width:100px;font-size:11px',
            anchor: '100%'
         };
         
        
        this.hostField = new Ext.form.TextField({
    		fieldLabel : de.intrabuild.Gettext.gettext("Host"),
    		allowBlank : false,
    		validator  : this.validateInbox.createDelegate(this),
    		name	   : 'serverInbox'
    	});
         
        this.usernameField = new Ext.form.TextField({
    		fieldLabel : de.intrabuild.Gettext.gettext("User name"),
    		allowBlank : false,
    		name	   : 'usernameInbox'
    	});
    	
        this.passwordField = new Ext.form.TextField({
        	inputType  : 'password',
    		fieldLabel : de.intrabuild.Gettext.gettext("Password"),
    		allowBlank : false,
    		name	   : 'passwordInbox'
    	});    	
	
		this.items = [
			new de.intrabuild.groupware.util.FormIntro({
        		style   : 'margin:10px 0 5px 0;',
        		label	: de.intrabuild.Gettext.gettext("Inbox server"),
        		text	: de.intrabuild.Gettext.gettext("Specify the host address of the inbox server here (e.g. pop3.provider.de) and your user credentials for authentication.")	
        	}),
        	this.hostField,	
        	this.usernameField,
        	this.passwordField
		];
        	
		de.intrabuild.groupware.email.EmailAccountWizardNameCard.superclass.initComponent.call(this);
	},
	
	validateInbox : function(value)
	{
		value = value.trim();
		
		if (value === "") {
			return false;	
		} else {
			/**
			 * @ext-bug 2.0.2 seems to look for any match
			 */
			//var index = this.accountStore.find('name', value, 0, false, false);
			/*var recs = this.accountStore.getRange();
			for (var i = 0, len = recs.length; i < len; i++) {
				if (recs[i].get('serverInbox').toLowerCase() === value) {
					return false;	
				}
			}
			
			return true;*/
		}
		
		
		return true;
	}
	
});  

de.intrabuild.groupware.email.EmailAccountWizardServerOutboxCard = Ext.extend(Ext.ux.Wiz.Card, {
	
	hostField	  : null,
	useAuthField  : null,
	usernameField : null,
	passwordField : null,
	
	initComponent : function()
	{
		this.monitorValid = true;
	
		this.baseCls    = 'x-small-editor';
		
		this.defaultType = 'textfield';
		this.title = de.intrabuild.Gettext.gettext("Outbox server");
		
        
        this.hostField = new Ext.form.TextField({
    		fieldLabel : de.intrabuild.Gettext.gettext("Host"),
    		allowBlank : false,
    		labelStyle : 'width:85px;font-size:11px',
    		width	   : 200,
    		name	   : 'serverOutbox'
    	});

        this.useAuthField = new Ext.form.Checkbox({
    		fieldLabel : de.intrabuild.Gettext.gettext("Server requires authentication"),
    		labelStyle : 'margin-top:12px;width:140px;font-size:11px',
    		style 	   : 'margin-top:14px;',
    		name	   : 'isOutboxAuth'
    	});
         
        this.useAuthField.on('check', this.onAuthCheck, this); 
         
        this.usernameField = new Ext.form.TextField({
    		fieldLabel : de.intrabuild.Gettext.gettext("User name"),
    		disabled   : true,
    		labelStyle : 'width:85px;font-size:11px',
    		width	   : 200,
    		name	   : 'usernameOutbox'
    	});
    	
        this.passwordField = new Ext.form.TextField({
        	inputType  : 'password',
    		fieldLabel : de.intrabuild.Gettext.gettext("Password"),
    		disabled   : true,
    		labelStyle : 'width:85px;font-size:11px',
    		width	   : 200,
    		name	   : 'passwordOutbox'
    	});    	
	
		this.items = [
			new de.intrabuild.groupware.util.FormIntro({
        		style   : 'margin:10px 0 5px 0;',
        		label	: de.intrabuild.Gettext.gettext("Outbox server"),
        		text    : de.intrabuild.Gettext.gettext("Specify the host address of the outbox server here (e.g. smtp.provider.de) and your user credentials, if the server requires authentication.")	
        	}),
        	this.hostField,	
        	this.useAuthField,
        	this.usernameField,
        	this.passwordField
		];
        	
        	
        	
		de.intrabuild.groupware.email.EmailAccountWizardNameCard.superclass.initComponent.call(this);
	},
	
	
	onAuthCheck : function(checkbox, checked)
	{
		this.passwordField.allowBlank = !checked;
		this.usernameField.allowBlank = !checked;
		
		if (!checked) {
			this.passwordField.reset();
			this.usernameField.reset();
		}
		
		this.passwordField.setDisabled(!checked);
		this.usernameField.setDisabled(!checked);
	}
	
});  
    
de.intrabuild.groupware.email.EmailAccountWizardAccountNameCard = Ext.extend(Ext.ux.Wiz.Card, {
	
	nameField : null,
	accountStore : null,
	
	initComponent : function()
	{
		this.monitorValid = true;
	
		this.accountStore = de.intrabuild.groupware.email.AccountStore.getInstance();
	
		 this.baseCls    = 'x-small-editor';
		this.labelWidth	= 75;
        
		this.defaultType = 'textfield';
		this.title = de.intrabuild.Gettext.gettext("Account name");
		this.defaults = {
            labelStyle : 'width:75px;font-size:11px',
            anchor: '100%'
         };
        
        this.nameField = new Ext.form.TextField({
    		fieldLabel : de.intrabuild.Gettext.gettext("Name"),
    		allowBlank : false,
    		validator  : this.validateAccountName.createDelegate(this),
    		name	   : 'name'
    	});
         
        this.items = [
			new de.intrabuild.groupware.util.FormIntro({
        		style   : 'margin:10px 0 5px 0;',
        		label	: de.intrabuild.Gettext.gettext("Account name"),
        		text	: de.intrabuild.Gettext.gettext("Specify a unique name for this account. This name will be used later on to identify this account. The name must not be already existing.")	
        	}),
        	this.nameField
		];
        	
		de.intrabuild.groupware.email.EmailAccountWizardAccountNameCard.superclass.initComponent.call(this);
	},
	
	validateAccountName : function(value)
	{
		value = value.trim();
		
		if (value === "") {
			return false;	
		} else {
			/**
			 * @ext-bug 2.0.2 seems to look for any match
			 */
			//var index = this.accountStore.find('name', value, 0, false, false);
			var recs = this.accountStore.getRange();
			for (var i = 0, len = recs.length; i < len; i++) {
				if (recs[i].get('name').toLowerCase() === value) {
					return false;	
				}
			}
			
			return true;
		}
		
		
		return true;
	}
	
});      
    
    
de.intrabuild.groupware.email.EmailAccountWizardFinishCard = Ext.extend(Ext.ux.Wiz.Card, {
	
	templates	 : null,
	contentPanel : null,
	
	
	initComponent : function()
	{
		this.templates =  {
			master : new Ext.Template(
				'<table style="margin-top:15px;" border="0", cellspacing="2" cellpadding="2">'+
					'<tbody>'+
					'<tr><td>'+de.intrabuild.Gettext.gettext("Account name")+':</td><td>{name:htmlEncode}</td></tr>'+
					'<tr><td>'+de.intrabuild.Gettext.gettext("Your name")+':</td><td>{userName:htmlEncode}</td></tr>'+
					'<tr><td>'+de.intrabuild.Gettext.gettext("Email address")+':</td><td>{address:htmlEncode}</td></tr>'+
					'<tr><td>'+de.intrabuild.Gettext.gettext("Inbox host")+':</td><td>{serverInbox:htmlEncode}</td></tr>'+
					'<tr><td>'+de.intrabuild.Gettext.gettext("Inbox user name")+':</td><td>{usernameInbox:htmlEncode}</td></tr>'+
					'<tr><td>'+de.intrabuild.Gettext.gettext("Inbox password")+':</td><td>{passwordInbox}</td></tr>'+
					'<tr><td>'+de.intrabuild.Gettext.gettext("Outbox host")+':</td><td>{serverOutbox:htmlEncode}</td></tr>'+
					'<tr><td>'+de.intrabuild.Gettext.gettext("Outbox authentication")+':</td><td>{isOutboxAuth}</td></tr>'+
					'{auth_template}'+
					'</tbody>'+			
				'</table>'
			),
			auth : new Ext.Template(
				'<tr><td>'+de.intrabuild.Gettext.gettext("Outbox user name")+':</td><td>{usernameOutbox:htmlEncode}</td></tr>'+
				'<tr><td>'+de.intrabuild.Gettext.gettext("Outbox password")+':</td><td>{passwordOutbox}</td></tr>'
			)
		};
		
		var ts = this.templates;
		
		for(var k in ts){
            ts[k].compile();
        }
		
		
		this.border = false;
		this.monitorValid = false;
	
		this.title = de.intrabuild.Gettext.gettext("Confirm");
		
		this.contentPanel = new Ext.Panel({
			style : 'margin:0 0 0 20px'
		});
		
		this.items = [{
				border    : false,
				html 	  : "<div>"+de.intrabuild.Gettext.gettext("The new account can now be created.<br />Please verify your submitted data and correct them if neccessary.")+"</div>",	
				bodyStyle : 'background-color:#F6F6F6;margin:10px 0 10px 0'
			},
			this.contentPanel
		];
    
    	this.contentPanel.on('render', this.addContent, this);
        	
		de.intrabuild.groupware.email.EmailAccountWizardFinishCard.superclass.initComponent.call(this);
	},
	
	addContent : function()
	{
		var ts = this.templates;
		
		var authTemplate = "";
		
		var items = this.ownerCt.items;
		
		var values = {};
		var formValues = {};
		for (var i = 0, len = items.length; i < len; i++) {
			formValues = items.get(i).form.getValues(false);
			for (var a in formValues) {
				values[a] = formValues[a];	
			}
		}
		
		if (values.isOutboxAuth == 'on') {
			authTemplate = ts.auth.apply({
				usernameOutbox : values.usernameOutbox,
				passwordOutbox : "****"
			});
		}
		
		var html = ts.master.apply({
			name          : values.name,
			userName      : values.userName,
			address       : values.address,
			serverInbox   : values.serverInbox,
			usernameInbox : values.usernameInbox,
			passwordInbox : "****",
			serverOutbox  : values.serverOutbox,
			isOutboxAuth  : values.isOutboxAuth == 'on' ? "Ja" : "Nein",
			auth_template : authTemplate
		});
		
		this.contentPanel.el.update(html);
		
		this.contentPanel.un('render', this.addContent, this);
		this.on('show', this.addContent, this);
	}
	
	
	
});