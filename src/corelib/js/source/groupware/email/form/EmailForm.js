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

com.conjoon.groupware.email.form.EmailForm = Ext.extend(Ext.Panel, {

    __is : 'com.conjoon.groupware.email.EmailForm',


    initComponent : function()
    {
        var accSt        = com.conjoon.groupware.email.AccountStore;
        var accountStore = accSt.getInstance();

        var view = new Ext.grid.GridView({
            getRowClass : function(record, rowIndex, p, ds){
                return 'com-conjoon-groupware-email-EmailForm-gridrow';
            }
        });

        var standardAcc = accSt.getStandardAccount(false);

        this.fromComboBox = new Ext.form.ComboBox({
           name : 'from',
           tpl : '<tpl for="."><div class="x-combo-list-item">{address:htmlEncode} - {name:htmlEncode}</div></tpl>',
           fieldLabel : com.conjoon.Gettext.gettext("From"),
           anchor     : '100%',
           typeAhead: false,
           triggerAction: 'all',
           editable : false,
           lazyRender:true,
           displayField  : 'address',
           value : (standardAcc ? standardAcc.id : undefined),
           mode : 'local',
           valueField    : 'id',
           listClass: 'x-combo-list-small',
           store : accountStore
        });

        var addressQueryComboBox = new com.conjoon.groupware.email.form.RecipientComboBox();

        this.gridStore = new Ext.data.JsonStore({
            id       : 'id',
            fields   : ['receiveType', 'address']
        });

        var receiveTypeEditor = new Ext.form.ComboBox({
            typeAhead     : false,
            triggerAction : 'all',
            lazyRender    : true,
            editable      : false,
            mode          : 'local',
            value         : 'gg',
            listClass     : 'x-combo-list-small',
            store         : [
                ['to',  com.conjoon.Gettext.gettext('To:')],
                ['cc',  com.conjoon.Gettext.gettext('CC:')],
                ['bcc', com.conjoon.Gettext.gettext('BCC:')]
            ]
        });


        this.grid = new Ext.grid.EditorGridPanel({
            hideHeaders : true,
            region  : 'center',
            margins : '2 5 2 5',
            style   : 'background:none',
            store   : this.gridStore,
            columns : [{
                id        : 'receiveType',
                header    : 'receiveType',
                width     : 100,
                dataIndex : 'receiveType',
                editor    : receiveTypeEditor,
                renderer  : function(value, metadata, record, rowIndex, colIndex, store) {
                    var st  = receiveTypeEditor.store;
                    var ind = st.find('field1', value, 0, false, true);
                    var sRecord = null;
                    if (ind >= 0) {
                        sRecord = st.getAt(ind);
                    }
                    if(sRecord) {
                        return sRecord.get('field2');
                    } else {
                        '';
                    }
                }
            },{
                id : 'address',
                header: "address",
                dataIndex: 'address',
                editor: addressQueryComboBox,
                renderer: function(value, p, record) {
                    return Ext.util.Format.htmlEncode(value);
                }
            }],
            view : view,
            header : false,
            clicksToEdit:1
        });



       this.subjectField = new Ext.form.TextField({
            name            : 'subject',
            fieldLabel      : com.conjoon.Gettext.gettext("Subject"),
            anchor          : '100%',
            enableKeyEvents : true
        });

        this.htmlEditor = new com.conjoon.groupware.email.form.EmailEditor();

        Ext.apply(this, {
            items : [{
                layout : 'border',
                bodyStyle : 'background-color:#F6F6F6',
                region : 'north',
                split : true,
                hideMode : 'offsets',
                height:125,
                minSize:125,
                items  : [
                    new Ext.form.FormPanel({
                        labelWidth  : 30,
                        region : 'north',
                        height : 20,
                        minSize: 20,
                        hideMode : 'offsets',
                        margins: '4 5 2 5',
                        style  : 'background:none',
                        baseCls     : 'x-small-editor',
                        border : false,
                        defaults : {
                            labelStyle : 'width:30px;font-size:11px'
                        },
                        defaultType : 'textfield',
                        items : [
                            this.fromComboBox
                        ]
                  }), this.grid,
                    new Ext.form.FormPanel({
                        labelWidth  : 45,
                        region : 'south',
                        height : 20,
                        hideMode : 'offsets',
                        minSize: 20,
                        style  : 'background:none',
                        margins: '2 5 4 5',
                        baseCls     : 'x-small-editor',
                        border : false,
                        defaults : {
                            labelStyle : 'width:45px;font-size:11px'
                        },
                        defaultType : 'textfield',
                        items : [
                            this.subjectField
                        ]
                  })
              ]},
                new Ext.form.FormPanel({
                    region : 'center',
                    hideMode : 'offsets',
                    baseCls     : 'x-small-editor',
                    border:false,
                    items  : [this.htmlEditor]
                })
            ]

        });


        this.loadMask = null;

        com.conjoon.util.Registry.register('com.conjoon.groupware.email.EmailForm', this, true);

        com.conjoon.groupware.email.form.EmailForm.superclass.initComponent.call(this);
    },


    initEvents : function()
    {
        this.mon(this.grid, 'resize', function() {
            var cm = this.getColumnModel();
            var rem = this.getGridEl().getWidth(true)-this.view.getScrollOffset()
                      - cm.getColumnWidth(0)-2;

            cm.setColumnWidth(1, rem);
            this.view.focusEl.setWidth(rem);
        }, this.grid);

        com.conjoon.groupware.email.form.EmailForm.superclass.initEvents.call(this);

    }





});