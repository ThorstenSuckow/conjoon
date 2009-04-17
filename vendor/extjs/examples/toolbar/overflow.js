/*
 * Ext JS Library 3.0 RC1
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */

Ext.onReady(function(){

    var handleAction = function(action){
        Ext.example.msg('<b>Action</b>', 'You clicked "'+action+'"');
    };
    
    var p = new Ext.Window({
        title: 'Standard',
        closable: false,
        height:250,
        width: 500,
        bodyStyle: 'padding:10px',
        contentEl: 'content',
        autoScroll: true,
        tbar: [{
            xtype:'splitbutton',
            text: 'Hideous',
            iconCls: 'add16',
            handler: handleAction.createCallback('Hideous'),
            menu: [{text: 'Hideous menu', handler: handleAction.createCallback('Hideous menu')}]
        },'-',{
            xtype:'splitbutton',
            text: 'Cut',
            iconCls: 'add16',
            handler: handleAction.createCallback('Cut'),
            menu: [{text: 'Cut menu', handler: handleAction.createCallback('Cut menu')}]
        },{
            text: 'Copy',
            iconCls: 'add16',
            handler: handleAction.createCallback('Copy')
        },{
            text: 'Paste',
            iconCls: 'add16',
            menu: [{text: 'Paste menu', handler: handleAction.createCallback('Paste menu')}]
        },'-',{
            text: 'Format',
            iconCls: 'add16',
            handler: handleAction.createCallback('Format')
        },'->',{
            text: 'Right',
            iconCls: 'add16',
            handler: handleAction.createCallback('Right')
        }]
    });
    p.show();

});