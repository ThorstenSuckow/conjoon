Ext.namespace('de.intrabuild.groupware.util');

/**
 * @class de.intrabuild.groupware.form.FormIntro
 * @extends Ext.BoxComponent
 * Utility Formintro class.
 * @constructor
 * @param {Number} height (optional) Spacer height in pixels (defaults to 22).
 */
de.intrabuild.groupware.util.FormIntro = Ext.extend(Ext.BoxComponent, {
    
	/**
	 * @cfg {String} imageClass
	 */
	
	_textEl : null,
	
	_labelEl : null,
  
	autoEl : {
		tag : 'div',
		children : [{
		  	tag		 : 'div', 
		  	cls		 : 'de-intrabuild-groupware-util-FormIntro-container',
		  	children : [{
		  		tag	 : 'div',
		  		cls  :	'de-intrabuild-groupware-util-FormIntro-label',
		  		html : 'Formintro'
		  	}, {
		  		tag	 	 : 'div',
		  		cls	 	 : 'de-intrabuild-groupware-util-FormIntro-outersep',
		  		children : [{
		  			tag	 : 'div',
		  			cls  : 'de-intrabuild-groupware-util-FormIntro-sepx',
		  			html : '&nbsp;' 
		  		}]
		  	}]
		}, {
			tag  : 'div',
			html : '',
			cls  : 'de-intrabuild-groupware-util-FormIntro-description'
		}]
	},
  
    setLabel : function(label)
    {
        this.label = label;
        
		if (this.rendered) {
			this._labelEl.update(label);
		}
    },  
  
    setText : function(text)
	{
		this.text = text;
		
		if (this.rendered) {
			if (this.text != undefined) {
	            this._textEl.update(this.text);
				this._textEl.setDisplayed(true);
	        } else {
	            this._textEl.setDisplayed(false);
	        }
		}
	},
  
	onRender : function(ct, position)
	{
		de.intrabuild.groupware.util.FormIntro.superclass.onRender.call(this, ct, position);
	
        this._labelEl = new Ext.Element(this.el.dom.firstChild.firstChild);
	    this._textEl  = new Ext.Element(this.el.dom.lastChild);
		
		if (this.label) {
			this._labelEl.update(this.label);
		}
	
		if (this.text != undefined) {
		    this._textEl.update(this.text);
		} else {
			this._textEl.setDisplayed(false);
		}
		
		if (this.imageClass) {
			this._textEl.addClass(this.imageClass);
		}
		
	}
});