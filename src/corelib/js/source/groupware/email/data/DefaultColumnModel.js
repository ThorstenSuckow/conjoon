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

Ext.namespace('com.conjoon.groupware.email.data');

com.conjoon.groupware.email.data.DefaultColumnModel = function(config) {

    config = config || {};

    Ext.apply(config, {
        columns : [{
            header: '<div class="com-conjoon-groupware-email-EmailGrid-backgroundContainer '
                    + 'com-conjoon-groupware-email-EmailGrid-attachmentColumn-headerBackground">&#160;</div>',
            width: 25,
            //align:'center',
            sortable: true,
            resizable : false,
            dataIndex: 'isAttachment',
            renderer : function(value, p, record){
                   if (!value) {
                       return '&#160';
                   }
                   return '<div class="com-conjoon-groupware-email-EmailGrid-backgroundContainer '
                          + 'com-conjoon-groupware-email-EmailGrid-attachmentColumn-cellBackground">&#160;</div>';
               }
          },{
            header : '<div class="com-conjoon-groupware-email-EmailGrid-backgroundContainer '
                     + 'com-conjoon-groupware-email-EmailGrid-readColumn-headerBackground">&#160;</div>',
            width: 25,
            //align:'center',
            sortable: true,
            resizable : false,
            dataIndex: 'isRead',
            renderer : function(value, p, record){
                          return '<div class="com-conjoon-groupware-email-EmailGrid-backgroundContainer '
                                 + 'com-conjoon-groupware-email-EmailGrid-readColumn-cellBackground '
                                 + (value ? 'itemread' : 'itemunread')
                                 + '">&#160;</div>';
                       }
          },{
            id : 'subject',
            header: com.conjoon.Gettext.gettext("Subject"),
            width     : 315,
            sortable  : true,
            dataIndex : 'subject',
            renderer  : com.conjoon.groupware.email.view.EmailGridRowRenderer.renderSubjectColumn
          },{
            header: com.conjoon.Gettext.gettext("Sender"),
            width: 160,
            sortable: true,
            dataIndex: 'sender'
          },{
            header: com.conjoon.Gettext.gettext("Recipients"),
            width: 160,
            sortable: true,
            dataIndex: 'recipients'
          },{
            header : '<div class="com-conjoon-groupware-email-EmailGrid-backgroundContainer '
                     + 'com-conjoon-groupware-email-EmailGrid-spamColumn-headerBackground">&#160;</div>',
            width: 25,
            //align:'center',
            resizable : false,
            sortable: true,
            dataIndex: 'isSpam',
            renderer : function(value, p, record){
                           return '<div class="com-conjoon-groupware-email-EmailGrid-backgroundContainer '
                                     + 'com-conjoon-groupware-email-EmailGrid-spamColumn-cellBackground '
                                     + (value ? 'itemspam' : 'itemnospam')
                                     + '">&#160;</div>';
               }
          },{
            header: com.conjoon.Gettext.gettext("Date"),
            width: 100,
            sortable: true,
            dataIndex: 'date',
            renderer: com.conjoon.groupware.email.view.EmailGridRowRenderer.renderDateColumn
          }
    ]});

    com.conjoon.groupware.email.data.DefaultColumnModel.superclass.constructor.call(this, config);
}

Ext.extend(com.conjoon.groupware.email.data.DefaultColumnModel, Ext.grid.ColumnModel, {

    /**
     * @type {Boolean} usePlainHeaders Set this to true to let the getColumnHeader()
     * method return a textual representation of the column, even if the header uses
     * html.
     */
    usePlainHeaders : false,


    /**
     * Returns the header for the specified column.
     * Overriden so the custom headers can return a string.
     *
     * @param {Number} col The column index
     *
     * @return {String}
     */
    getColumnHeader : function(col)
    {
        if (this.usePlainHeaders !== true) {
            return this.config[col].header;
        }

        var col = this.config[col];

        switch (col.dataIndex) {
            case 'isAttachment':
                return com.conjoon.Gettext.gettext("has attachments");

            case 'isRead':
                return com.conjoon.Gettext.gettext("is read");

            case 'isSpam':
                return com.conjoon.Gettext.gettext("is spam");

            default:
                return col.header;
        }
    }

});