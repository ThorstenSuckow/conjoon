/*
 * Ext JS Library 3.0 RC1
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.data.DataReader=function(meta,recordType){this.meta=meta;this.recordType=Ext.isArray(recordType)?Ext.data.Record.create(recordType):recordType;};Ext.data.DataReader.prototype={};