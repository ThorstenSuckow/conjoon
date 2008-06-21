Ext.namespace('de.intrabuild.groupware.util');

/**
 * @class de.intrabuild.groupware.form.FormIntro
 * @extends Ext.BoxComponent
 * Utility Formintro class.
 * @constructor
 * @param {Number} height (optional) Spacer height in pixels (defaults to 22).
 */
de.intrabuild.groupware.util.FormIntro = Ext.extend(Ext.BoxComponent, {
  
	autoEl : {
		tag : 'div',
		children : [{
		  	tag		 : 'div', 
		  	cls		 : 'de-intrabuild-formintro-container',
		  	children : [{
		  		tag	 : 'div',
		  		cls  :	'de-intrabuild-formintro-label',
		  		html : 'Formintro'
		  	}, {
		  		tag	 	 : 'div',
		  		cls	 	 : 'de-intrabuild-formintro-outersep',
		  		children : [{
		  			tag	 : 'div',
		  			cls  : 'de-intrabuild-formintro-sepx',
		  			html : '&nbsp;' 
		  		}]
		  	}]
		}, {
			tag  : 'div',
			html : 'Hier könnte dann eine Beschreibung stehen',
			cls  : 'de-intrabuild-formintro-description'
		}]
	},
  
	onRender : function(ct, position)
	{
		de.intrabuild.groupware.util.FormIntro.superclass.onRender.call(this, ct, position);
	
		if (this.label) {
			this.el.dom.firstChild.firstChild.innerHTML = this.label;
		}
	
		if (this.text) {
			this.el.dom.lastChild.innerHTML = this.text;
		}
	}
});