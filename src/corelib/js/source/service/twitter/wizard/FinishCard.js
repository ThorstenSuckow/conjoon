/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
 * licensing@conjoon.org
 *
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */

Ext.namespace('com.conjoon.service.twitter.wizard');

/**
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.service.twitter.wizard.FinishCard
 * @extends Ext.ux.Wiz.Card
 */
com.conjoon.service.twitter.wizard.FinishCard = Ext.extend(Ext.ux.Wiz.Card, {

    templates    : null,
    contentPanel : null,


    initComponent : function()
    {
        this.templates =  {
            master : new Ext.Template(
                '<table style="margin-top:25px;" border="0", cellspacing="2" cellpadding="2">'+
                    '<tbody>'+
                    '<tr><td>'+com.conjoon.Gettext.gettext("Twitter account")+':</td><td>{name:htmlEncode}</td></tr>'+
                    '</tbody>'+
                '</table>'
            )
        };

        var ts = this.templates;

        for(var k in ts){
            ts[k].compile();
        }


        this.border = false;
        this.monitorValid = false;

        this.title = com.conjoon.Gettext.gettext("Confirm");

        this.contentPanel = new Ext.Panel({
            style : 'margin:0 0 0 20px'
        });

        this.items = [{
                border    : false,
                html      : "<div>"+com.conjoon.Gettext.gettext("You can now import your Twitter account. The Twitter service will be contacted in order to verify your account settings.")+"</div>",
                bodyStyle : 'background-color:#F6F6F6;margin:10px 0 10px 0'
            },
            this.contentPanel
        ];

        this.contentPanel.on('render', this.addContent, this, {single : true});

        com.conjoon.service.twitter.wizard.FinishCard.superclass.initComponent.call(this);
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

        var html = ts.master.apply({
            name     : values.name,
            password : "****"
        });

        this.contentPanel.el.update(html);

        this.on('show', this.addContent, this);
    }



});