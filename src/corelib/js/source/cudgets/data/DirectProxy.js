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

Ext.namespace('com.conjoon.cudgets.data');

if (Ext.version != '3.1.0') {
    throw("Using Ext "+Ext.version+" - please check overrides in com.conjoon.cudgets.data.DirectProxy");
}

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