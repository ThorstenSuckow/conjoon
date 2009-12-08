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

Ext.namespace('com.conjoon.cudgets.direct');

if (Ext.version != '3.0.3') {
    throw("Using Ext "+Ext.version+" - please check overrides in com.conjoon.cudgets.direct.ZendProvider");
}


/**
 * This is a special implementation for the Zend Framework router that allows
 * for specifying the module as the url, the controller as the action
 * and the method as the action.
 *
 * For example, this Provider configuration
 *
 * Ext.app.REMOTING_API = {
 *     url     : '/groupware/',
 *     type    : 'zend',
 *     actions :{
 *         feeds : [{
 *             name : updateAccounts
 *         }]
 *     }
 * };
 *
 * will translate to the url
 *
 *     /groupware/feeds/update.accounts
 *
 * when calling ffeds.updateAccounts();
 *
 * This is done by hooking into the connection process by listening to the
 * beforerequest-event of Ext.data.Connection and altering the url if a provider of
 * type com.conjoon.cudgets.direct.ZendProvider is detected.
 * Sadly, the API of Ext.data.Provider does not allow for url manipulation (specially
 * because of "threaded" connection calls which may lead to race conditions when
 * operating on the url property of the class directly), thus an event listener is used.
 *
 * You can pass the additional "format" property to the remoting api, which will be appended
 * to the url. This is to support teh contextSwitch functionality of the Zend Framework.
 *
 * Example:
 *
 * Ext.app.REMOTING_API = {
 *     format  : json
 *     url     : '/groupware/',
 *     type    : 'zend',
 *     actions :{
 *         feeds : [{
 *             name : updateAccounts
 *         }]
 *     }
 * };
 *
 * will translate to the url
 *
 * /groupware/feeds/update.accounts/format/json
 *
 * when feeds.updateAccounts() is called.
 *
 *
 */
com.conjoon.cudgets.direct.ZendProvider = Ext.extend(Ext.direct.RemotingProvider, {

    /**
     * @cfg {String} format The additional parameter to append to the url for supporting
     * the context switch functionality of Zend Framework.
     */

    /**
     * @ext bug 3.0.3
     * @see http://www.extjs.com/forum/showthread.php?p=400493
     */
    getEvents: function(xhr, opt){
        var data = null;
        var exc  = null;
        try{
            data = this.parseResponse(xhr);
        }catch(e){
            exc  = e;
            data = null;
        }

        if (data === null) {
            var events = [];

            var opts = [].concat(opt.ts);

            for (var i = 0, len = opts.length; i < len; i++) {
                events.push(
                    new Ext.Direct.ExceptionEvent({
                        data   : exc,
                        tid    : opts[i].tid,
                        xhr    : xhr,
                        code   : Ext.Direct.exceptions.PARSE,
                        message: 'Error parsing json response'
                    })
                )
            }

            return events;
        }

        return com.conjoon.cudgets.direct.ZendProvider.superclass.getEvents.call(
            this, xhr
        );
    },

    /**
     * @ext bug 3.0.3
     * @see http://www.extjs.com/forum/showthread.php?p=400493
     */
    onData: function(opt, success, xhr){
        if(success){
            var events = this.getEvents(xhr, opt);
            for(var i = 0, len = events.length; i < len; i++){
                var e = events[i],
                    t = this.getTransaction(e);

                this.fireEvent('data', this, e);
                if(t){
                    this.doCallback(t, e, true);
                    Ext.Direct.removeTransaction(t);
                }
            }
            return;
        }

        return com.conjoon.cudgets.direct.ZendProvider.superclass.onData.call(
            this, opt, success, xhr
        );
    }

});

Ext.Direct.PROVIDERS['zend'] = com.conjoon.cudgets.direct.ZendProvider;


Ext.Ajax.on('beforerequest', function(conn, options) {

    if (options.ts && options.ts.provider && options.ts.provider.type == 'zend') {
        var controller = options.ts.action;
        controller = controller.replace(/([a-z])([A-Z])/g, "$1.$2").toLowerCase();
        var action    = options.ts.method;
        action = action.replace(/([a-z])([A-Z])/g, "$1.$2").toLowerCase();

        var url = options.url;
        url     = url +
                   (url.lastIndexOf('/') == url.length-1 ? '' : '/') +
                   controller +
                   '/' +
                   action;

        if (options.ts.provider.format) {
            url += '/format/' + options.ts.provider.format;
        }

        options.url = url;
    }

    options.url += '?dc=' + Math.round((new Date()).getTime());

});