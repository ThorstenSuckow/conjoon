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

Ext.namespace('com.conjoon.cudgets.data');

if (Ext.version != '3.4.0') {
    throw("Using Ext "+Ext.version+" - please check overrides in com.conjoon.cudgets.data.Store");
}

com.conjoon.cudgets.data.Store = Ext.extend(Ext.data.Store, {


    /**
     * @bug ext 3.0.3
     * @see http://www.extjs.com/forum/showthread.php?p=400968
     */
    // @private callback-handler for remote CRUD actions
    // Do not override -- override loadRecords, onCreateRecords, onDestroyRecords and onUpdateRecords instead.
    createCallback : function(action, rs, batch) {
        var actions = Ext.data.Api.actions;

        return (action == 'read') ? this.loadRecords : function(data, response, success) {
            // calls: onCreateRecords | onUpdateRecords | onDestroyRecords
            this['on' + Ext.util.Format.capitalize(action) + 'Records'](success, rs, (data ? [].concat(data) : []));
            // If success === false here, exception will have been called in DataProxy

            if (data && data.success !== undefined) {
                this.fireEvent('write', this, action, data, response, rs);
            }
            // added to be compatible with Ext 3.1.2 API
            this.removeFromBatch(batch, action, data);
        };
    },

    /**
     * @bug ext 3.0.3
     * @see http://www.extjs.com/forum/showthread.php?t=83205
     *
     * See createCallback - data will be an array - only 1 index for update, though.
     */
    // @protected onDestroyRecords proxy callback for destroy action
    onDestroyRecords : function(success, rs, data) {
        // splice each rec out of this.removed
        rs = (rs instanceof Ext.data.Record) ? [rs] : [].concat(rs);
        for (var i=0,len=rs.length;i<len;i++) {
            this.removed.splice(this.removed.indexOf(rs[i]), 1);
        }

        var rejectAll = true;

        data = data[0];

        if (data && data.success !== undefined) {
            if (data && Ext.isArray(data.failed)) {
                rejectAll = false;
                for (var i = 0, len = data.failed.length; i < len; i++) {
                    for (var a = 0, lena = rs.length; a < lena; a++) {
                        if (rs[a].id == data.failed[i]) {
                            this.insert(rs[a].lastIndex, rs[a]);    // <-- lastIndex set in Store#destroyRecord
                        }
                    }
                }
            }
        }

        if (rejectAll) {
            // put records back into store if remote destroy fails.
            // @TODO: Might want to let developer decide.
            for (i=rs.length-1;i>=0;i--) {
                this.insert(rs[i].lastIndex, rs[i]);    // <-- lastIndex set in Store#destroyRecord
            }
        }
    },


    /**
     * @bug ext 3.0.3
     * @see http://www.extjs.com/forum/showthread.php?t=83205
     *
     * See createCallback - data will be an array - only 1 index for update, though.
     */
    // @protected, onUpdateRecords proxy callback for update action
    onUpdateRecords : function(success, rs, data) {

        rs = [].concat(rs);

        var rejectAll = true;

        data = data[0];

        if (data && data.success !== undefined) {
            if (data && Ext.isArray(data.updated)) {
                rejectAll = false;
                for (var i = 0, len = data.updated.length; i < len; i++) {
                    for (var a = 0, lena = rs.length; a < lena; a++) {
                        if (rs[a].id == data.updated[i]) {
                            rs[a].commit();
                        }
                    }
                }
            }

            if (data && Ext.isArray(data.failed)) {
                rejectAll = false;
                for (var i = 0, len = data.failed.length; i < len; i++) {
                    for (var a = 0, lena = rs.length; a < lena; a++) {
                        if (rs[a].id == data.failed[i]) {
                            rs[a].reject();
                        }
                    }
                }
            }
        }

        if (rejectAll) {
            for (var i = 0, len = rs.length; i < len; i++) {
                rs[i].reject();
            }
        }


    }

});