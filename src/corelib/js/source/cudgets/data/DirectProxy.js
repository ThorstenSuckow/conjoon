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

Ext.namespace('com.conjoon.cudgets.data');

/*@REMOVE@*/
if (Ext.version != '3.4.0') {
    throw("Using Ext "+Ext.version+" - please check overrides in com.conjoon.cudgets.data.DirectProxy");
}
/*@REMOVE@*/

com.conjoon.cudgets.data.DirectProxy = Ext.extend(Ext.data.DirectProxy, {

    /**
     * @bug ext 3.0.3
     * @see http://www.extjs.com/forum/showthread.php?t=83205
     *
     * Callback for write actions
     * @param {String} action [{@link Ext.data.Api#actions create|read|update|destroy}]
     * @param {Object} trans The request transaction object
     * @param {Object} result Data object picked out of the server-response.
     * @param {Object} res The server response
     * @param {Ext.data.Record/[Ext.data.Record]} rs The Store resultset associated with the action.
     * @protected
     */
    onWrite : function(action, trans, result, res, rs) {

        // check first if result is set, a successpRoperty is available and if this
        // success property is set to false

        if (trans.reader.meta.successProperty) {
            var v = true;
            try {
               v = trans.reader.getSuccess(result);
            } catch (e) {
                // ignore
            }
            if(v === false || v === 'false'){
                this.fireEvent("write", this, action, result, res, rs, trans.request.arg);
                trans.request.callback.call(trans.request.scope, result, res, false);
                return;
            }
        }


        var data  = result;
        var myexc = null;

        // treat an empty result array like an exception!!!
        if (!data || data.length === 0) {
            this.fireEvent('exception', this, 'response', action, trans, res, myexc)
            trans.request.callback.call(trans.request.scope, null, trans.request.arg, false);
            return;
        }

        this.fireEvent("write", this, action, data, res, rs, trans.request.arg);
        trans.request.callback.call(trans.request.scope, data, res, true);
    },

    /**
     * Callback for read actions
     *
     * @ext bug 3.0.3
     * Overrides parent implementation by adding check for success property.
     * @see http://www.extjs.com/forum/showthread.php?t=83205
     *
     *
     * @param {String} action [Ext.data.Api.actions.create|read|update|destroy]
     * @param {Object} trans The request transaction object
     * @param {Object} res The server response
     * @private
     */
    onRead : function(action, trans, result, res) {
        if (result.success === false) {
            // @deprecated: fire old loadexception for backwards-compat.
            // TODO remove in 3.1
            this.fireEvent('loadexception', this, trans, res);
            this.fireEvent('exception', this, 'remote', action, trans, res, null);
            trans.request.callback.call(trans.request.scope, null, trans.request.arg, false);
            return;
        }

        com.conjoon.cudgets.data.DirectProxy.superclass.onRead.call(
            this, action, trans, result, res
        );
    }

});