/*
 * Ext JS Library 3.0 RC1
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.layout.BoxLayout=Ext.extend(Ext.layout.ContainerLayout,{monitorResize:true,scrollOffset:0,extraCls:'x-box-item',ctCls:'x-box-layout-ct',innerCls:'x-box-inner',defaultMargins:{left:0,top:0,right:0,bottom:0},padding:'0',pack:'start',isValidParent:function(c,target){return c.getEl().dom.parentNode==this.innerCt.dom;},onLayout:function(ct,target){var cs=ct.items.items,len=cs.length,c,i,last=len-1,cm;if(!this.innerCt){target.addClass(this.ctCls);this.innerCt=target.createChild({cls:this.innerCls});this.padding=this.parseMargins(this.padding);}
this.renderAll(ct,this.innerCt);},renderItem:function(c){if(typeof c.margins=='string'){c.margins=this.parseMargins(c.margins);}else if(!c.margins){c.margins=this.defaultMargins;}
Ext.layout.BoxLayout.superclass.renderItem.apply(this,arguments);},getTargetSize:function(target){return(Ext.isIE6&&Ext.isStrict&&target.dom==document.body)?target.getStyleSize():target.getViewSize();}});Ext.layout.VBoxLayout=Ext.extend(Ext.layout.BoxLayout,{align:'left',pack:'start',onLayout:function(ct,target){Ext.layout.VBoxLayout.superclass.onLayout.call(this,ct,target);var cs=ct.items.items,len=cs.length,c,i,last=len-1,cm;var size=this.getTargetSize(target);var w=size.width-target.getPadding('lr')-this.scrollOffset,h=size.height-target.getPadding('tb'),l=this.padding.left,t=this.padding.top;if((Ext.isIE&&!Ext.isStrict)&&(w<1||h<1)){return;}else if(w<1&&h<1){return;}
var stretchWidth=w-(this.padding.left+this.padding.right);var totalFlex=0;var totalHeight=0;var maxWidth=0;for(i=0;i<len;i++){c=cs[i];cm=c.margins;totalFlex+=c.flex||0;totalHeight+=c.getHeight()+cm.top+cm.bottom;maxWidth=Math.max(maxWidth,c.getWidth()+cm.left+cm.top);}
var innerCtWidth=maxWidth+this.padding.left+this.padding.right;switch(this.align){case'stretch':this.innerCt.setSize(w,h);break;case'stretchmax':case'left':case'center':this.innerCt.setSize(w=Math.max(w,innerCtWidth),h);break;}
var extraHeight=h-totalHeight-this.padding.top-this.padding.bottom;var allocated=0;var cw,ch,cl,availableWidth=w-this.padding.left-this.padding.right;if(this.pack=='center'){t+=extraHeight?extraHeight/2:0;}else if(this.pack=='end'){t+=extraHeight;}
for(i=0;i<len;i++){c=cs[i];cm=c.margins;cw=c.getWidth();ch=c.getHeight();t+=cm.top;if(this.align!='center'){cl=l+cm.left;}else{var diff=availableWidth-(cw+cm.left+cm.right);if(diff==0){cl=l+cm.left;}else{cl=l+cm.left+(diff/2);}}
c.setPosition(cl,t);if(this.pack=='start'&&c.flex){var ratio=c.flex/totalFlex;var add=Math.floor(extraHeight*ratio);allocated+=add;if(i==last){add+=(extraHeight-allocated);}
ch+=add;c.setHeight(ch);}
if(this.align=='stretch'){c.setWidth((stretchWidth-(cm.left+cm.right)).constrain(c.minWidth||0,c.maxWidth||1000000));}else if(this.align=='stretchmax'){c.setWidth((maxWidth-(cm.left+cm.right)).constrain(c.minWidth||0,c.maxWidth||1000000));}
t+=ch+cm.bottom;}}});Ext.Container.LAYOUTS['vbox']=Ext.layout.VBoxLayout;Ext.layout.HBoxLayout=Ext.extend(Ext.layout.BoxLayout,{align:'top',pack:'start',onLayout:function(ct,target){Ext.layout.HBoxLayout.superclass.onLayout.call(this,ct,target);var cs=ct.items.items,len=cs.length,c,i,last=len-1,cm;var size=this.getTargetSize(target);var w=size.width-target.getPadding('lr')-this.scrollOffset,h=size.height-target.getPadding('tb'),l=this.padding.left,t=this.padding.top;if((Ext.isIE&&!Ext.isStrict)&&(w<1||h<1)){return;}else if(w<1&&h<1){return;}
var stretchHeight=h-(this.padding.top+this.padding.bottom);var totalFlex=0;var totalWidth=0;var maxHeight=0;for(i=0;i<len;i++){c=cs[i];cm=c.margins;totalFlex+=c.flex||0;totalWidth+=c.getWidth()+cm.left+cm.right;maxHeight=Math.max(maxHeight,c.getHeight()+cm.top+cm.bottom);}
var innerCtHeight=maxHeight+this.padding.top+this.padding.bottom;switch(this.align){case'stretch':this.innerCt.setSize(w,h);break;case'stretchmax':case'top':this.innerCt.setSize(w,innerCtHeight);break;case'middle':this.innerCt.setSize(w,h=Math.max(h,innerCtHeight));break;}
var extraWidth=w-totalWidth-this.padding.left-this.padding.right;var allocated=0;var cw,ch,ct,availableHeight=h-this.padding.top-this.padding.bottom;if(this.pack=='center'){l+=extraWidth?extraWidth/2:0;}else if(this.pack=='end'){l+=extraWidth;}
for(i=0;i<len;i++){c=cs[i];cm=c.margins;cw=c.getWidth();ch=c.getHeight();l+=cm.left;if(this.align!='middle'){ct=t+cm.top;}else{var diff=availableHeight-(ch+cm.top+cm.bottom);if(diff==0){ct=t+cm.top;}else{ct=t+cm.top+(diff/2);}}
c.setPosition(l,ct);if(this.pack=='start'&&c.flex){var ratio=c.flex/totalFlex;var add=Math.floor(extraWidth*ratio);allocated+=add;if(i==last){add+=(extraWidth-allocated);}
cw+=add;c.setWidth(cw);}
if(this.align=='stretch'){c.setHeight((stretchHeight-(cm.top+cm.bottom)).constrain(c.minHeight||0,c.maxHeight||1000000));}else if(this.align=='stretchmax'){c.setHeight((maxHeight-(cm.top+cm.bottom)).constrain(c.minHeight||0,c.maxHeight||1000000));}
l+=cw+cm.right;}}});Ext.Container.LAYOUTS['hbox']=Ext.layout.HBoxLayout;