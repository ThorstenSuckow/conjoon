/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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

Ext.namespace('com.conjoon.groupware.email.form');

/**
 * @class com.conjoon.groupware.email.form.RecipientComboBox
 *
 * A special combobox used for querying values server side.
 * The combobox will not trigger a new request if the value submitted has already been
 * queried before, and no corresponding values where returned by the server, i.e. the
 * server returned an empty result set.
 */
com.conjoon.groupware.email.form.RecipientComboBox = Ext.extend(Ext.form.ComboBox, {

    /**
     * Contains all query strings for which no records from the
     * server have been returned.
     * @type {Array} _blacklist
     */
    _blacklist : null,

    /**
     * @cfg {String} displayField The field to use for displaying the selected record.
     * Defaults to "fullAddress"
     */
    displayField : 'fullAddress',

    initComponent : function()
    {
        Ext.apply(this, {
            hideTrigger   : true,
            tpl           : '<tpl for="."><div class="x-combo-list-item">{fullAddress:htmlEncode}</div></tpl>',
            typeAhead     : true,
            queryDelay    : 250,
            lazyRender    : true,
            triggerAction : 'query',
            minChars      : 3,
            mode          : 'remote',
            listClass     : 'x-combo-list-small',
            // store
            store         : new Ext.data.JsonStore({
                url       : './groupware/email.edit/get.recipient/format/json',
                root      : 'matches',
                fields    : ['name', 'address', 'fullAddress'],
                autoLoad  : false,
                listeners : {
                    exception : {
                        fn:  function(proxy, type, action, options, response, arg) {
                            com.conjoon.groupware.ResponseInspector.handleFailure(response);
                        }
                    }
                }
            }),
            validator : function(value) {
                if (value && value.trim() != "") {
                    if (value.indexOf(';') != -1) {
                        return false;
                    }
                }
                return true;
            }
        });

        this._blacklist = [];

        com.conjoon.groupware.email.form.RecipientComboBox.superclass.initComponent.call(this);
    },

    initEvents : function()
    {
        com.conjoon.groupware.email.form.RecipientComboBox.superclass.initEvents.call(this);

        this.mon(this.store, 'load', this._onLoad, this);

        this.on('beforequery', this._onBeforeQuery, this);
        this.on('blur',        this._onBlur, this);
     },

    abortQuery : function()
    {
        var proxy = this.store.proxy;
        if (proxy.activeRequest[Ext.data.Api.actions.read]) {
            proxy.getConnection().abort(proxy.activeRequest[Ext.data.Api.actions.read]);
        }
    },

// -------- listeners

    /**
     * Listener for the blur event of this field.
     * Will abort any ongoing server request.
     *
     */
    _onBlur : function()
    {
        this.abortQuery();
    },

    /**
     * Listener for the beforequery event.
     * Will only return true if the value queried was not already queried
     * before and not found, if anyhing but whitespaces are submitted and
     * if it only contains chars that are allowed in a name or email address.
     * If the beforequery does not return false, this method will also request to
     * abort any ongoing server request.
     *
     * @param {Object} queryObject The event this method listens to.
     *
     * @return {Boolean} false if the request to the server should be cancelled,
     * otherwise true
     */
    _onBeforeQuery : function(queryObject)
    {
        var str = queryObject.query.toLowerCase();
        if (str.trim() == "") {
            return false;
        }

        if (str.length < 3) {
            return false;
        }


        var blacklist = this._blacklist;
        var i = blacklist.length-1;
        if (i >= 0) {
            do {
                if (blacklist[i].indexOf(str) != -1 || str.indexOf(blacklist[i]) != -1) {
                    return false;
                }
            } while (--i >= 0);
        }


        var alpha = /^[@a-z_ \-\.]+$/i;

        if (!alpha.test(str)) {
            return false;
        }

        this.abortQuery();
    },

    /**
     * Will blacklist the query value if no records have been returned
     * from the server.
     *
     * @param {Ext.data.Store} store
     * @param {Arrray} records
     * @param {Object} options
     *
     */
    _onLoad : function(store, records, options)
    {
        if (records.length == 0) {
            this._blacklist.push(store.baseParams[this.queryParam]);
        }
    }

});