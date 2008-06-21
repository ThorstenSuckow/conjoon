Ext.namespace('de.intrabuild.form');

de.intrabuild.form.TextField = Ext.extend(Ext.form.TextField, {
	
	initComponent : function()
	{
		this.addEvents(
			'regularkey'
		);	
	},
	
	fireKey : function(e)
	{
	    if(e.isSpecialKey()){
			this.fireEvent('specialkey', this, e);
        } else {
        	this.fireEvent('regularkey', this, e);
        }
    },
    
    initEvents : function() {
        
        de.intrabuild.form.TextField.superclass.initEvents.call(this);
        
        this.el.un(Ext.isIE ? "keydown" : "keypress", this.fireKey,  this);
        
        this.el.on("keydown", this.fireKey,  this);
        this.el.on("keypress", this.fireKey,  this);
        this.el.on("keyup", this.fireKey,  this);
    }    
	
});