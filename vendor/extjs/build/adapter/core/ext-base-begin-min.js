/*
 * Ext JS Library 3.0 Pre-alpha
 * Copyright(c) 2006-2008, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


(function(){var libFlyweight;function fly(el){if(!libFlyweight){libFlyweight=new Ext.Element.Flyweight();}
libFlyweight.dom=el;return libFlyweight;}