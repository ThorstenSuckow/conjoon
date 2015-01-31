/**
 * conjoon
 * (c) 2007-2015 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
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