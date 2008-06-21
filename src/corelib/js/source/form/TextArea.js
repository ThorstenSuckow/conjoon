Ext.namespace('de.intrabuild.form');

de.intrabuild.form.TextArea = Ext.extend(Ext.form.TextArea, {
	
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
    }
	
});
