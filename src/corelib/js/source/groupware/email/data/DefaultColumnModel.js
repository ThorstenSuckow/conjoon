/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
     * Returns the header for the specified column.
     * Overriden so the custom headers can return a string.
     *
     * @param {Number} col The column index
     * @param {Boolean} plain true for returning a textual representation
     * of the header
     *
     * @return {String}
     */
    getColumnHeader : function(col, plain)
    {
        if (plain !== true) {
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