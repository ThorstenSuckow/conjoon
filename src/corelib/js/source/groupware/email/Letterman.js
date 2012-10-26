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

Ext.namespace('com.conjoon.groupware.email');

/**
 * The Letterman is a singleton that's responsible for checking for new emails
 * in a given interval. It checks for new emails and delivers the data to the
 * registered listeners. After the Letterman has checked for new mails, it's store
 * will be wiped to make room for new data.
 *
 * If you want to receive messages from this component, you can subscribe to the
 * Ext.ux.util.MessageBus and listen for the following messages:
 * <ul>
 * <li><strong>com.conjoon.groupware.email.Letterman.beforeload</strong> - sent
 * before this component's store sends a request to the server to receive new messages.
 * The subscriber is passed the following parameters:
 *     <ul>
 *      <li>subject - the subject of the message</li>
 *      <li>message - an empty object</li>
 *     </ul>
 * </li>
 * <li><strong>com.conjoon.groupware.email.Letterman.loadexcepion</strong> - sent when
 * a request made by this component's store resulted in a failure.
 * The subscriber is passed the following parameters:
 *     <ul>
 *      <li>subject - the subject of the message</li>
 *      <li>message - an empty object</li>
 *     </ul>
 * </li>
 * <li><strong>com.conjoon.groupware.email.Letterman.load</strong> - sent when a request made
 * by this component's store resulted in a successfull response.
 * The subscriber is passed the following parameters:
 *     <ul>
 *      <li>subject - the subject of the message</li>
 *      <li>message - an object containing details about the message, with the follwing properties:
 *        <ul>
 *         <li>total - the total number of new emails as received by the server</li>
 *        </ul>
 *       </li>
 *     </ul>
 * </li>
 * </ul>
 *
 *
 */
com.conjoon.groupware.email.Letterman = function(config) {

    /**
     * A property to check whether the letterman is currently busy
     * looking for new messages.
     */
    var _busy = false;

    /**
     * A shorthand for the {@see Ext.ux.util.MessageBus} which is used
     * to publish messages to and receive messages from
     */
    var _messageBroadcaster = Ext.ux.util.MessageBus;

    /**
     * The store in which new emails will be added. The store is wiped upon
     * each new call to load.
     */
    var store = new Ext.data.Store({
        autoLoad    : false,
        remoteSort  : true,
        reader      : new Ext.data.JsonReader({
                          root            : 'items',
                          totalProperty   : 'totalCount',
                          successProperty : 'success',
                          id              : 'id'
                      },
                      com.conjoon.groupware.email.EmailItemRecord
                      ),
        sortInfo   : {field: 'date', direction: 'ASC'},
        proxy      : new Ext.data.HttpProxy({
            url      : './groupware/email.item/fetch.emails/format/json',
            timeout  : 20*60*1000
        })
    });

    store.loadRecords = function(o, options, success)
    {
        /**
         * Adds lastLoadingDate property to the store to be able to determine
         * when the store was last loaded.
         */
        this.lastLoadingDate = (new Date()).getTime();

        Ext.data.Store.prototype.loadRecords.call(this, o, options, success);
    };


    /**
     * The interval in which the letterman should check for new mails, in minutes.
     * @param {Number}
     */
    var interval = 5;

    /**
     * The task of the letterman.
     */
    var task = null;

    /**
     * The TaskRunner.
     * @param {Ext.util.TaskRunner}
     */
    var letterman = null;

    /**
     * Propert to check if the Lettermans run method has been called for the
     * first time.
     * Defaults to false.
     */
    var called = false;

    /**
     * Overrides proxy's createCallback to check for error
     *
     * Returns a callback function for a request.  Note a special case is made for the
     * read action vs all the others.
     * @param {String} action [create|update|delete|load]
     * @param {Ext.data.Record[]} rs The Store-recordset being acted upon
     * @private
     */
    var createCallback = function(action, rs)
    {
        return function(o, success, response) {
            var json = com.conjoon.util.Json;
            if (json.isError(response.responseText)) {
                com.conjoon.groupware.email.Letterman.onRequestFailure(this, 'response', action, o, response);
            }

            Ext.data.HttpProxy.prototype.createCallback.call(this, action, rs).call(this, o, success, response);
        };
    };

    /**
     * @param {com.conjoon.groupware.email.AccountStore}
     * Shorthand for the store with the configured email accounts.
     */
    var _accountStore = com.conjoon.groupware.email.AccountStore.getInstance();

    /**
     * Listener for the store's beforeload event.
     * Will check if any account is currently configured. If none is found,
     * the request to the server will be cancelled.
     *
     */
    var _onBeforeLoad = function()
    {
        if (_accountStore.getRange().length == 0) {
            return false;
        }

        com.conjoon.groupware.email.Letterman.rest();

        _messageBroadcaster.publish('com.conjoon.groupware.email.Letterman.beforeload', {});
    };

    var _reception = com.conjoon.groupware.Reception;

    return {

        init : function()
        {
            store.on('beforeload',    _onBeforeLoad,         this);
            store.on('exception',     this.onRequestFailure, this);
            store.on('load',          this.onLoad, this);
            store.proxy.createCallback = createCallback;
            return this;
        },

        on : function(eventType, callback, scope)
        {
            store.on(eventType, callback, scope);
        },

        un : function(eventType, callback, scope)
        {
            store.un(eventType, callback, scope);
        },

        /**
         * If the task for periodically checking mails is not started yet, this
         * method will wake the letterman up and order him to check for new mails
         * in the given interval.
         *
         */
        wakeup : function()
        {
            if (task != null) {
                return;
            }

            task = {
                run      : this.run,
                scope    : this,
                interval : (interval*60*1000)
            };

            if (letterman == null) {
                letterman = new Ext.util.TaskRunner();
            }

            letterman.start(task);
        },

        /**
         * Tells the letterman to take a break.
         *
         */
        rest : function()
        {
            if (letterman == null || task == null) {
                return;
            }

            letterman.stop(task);
            task   = null;
            called = false;
        },

        /**
         * Since the taskrunner executes the given method as soon as the thread
         * starts, this method will check each call and skip if it was called
         * for the first time.
         *
         * If the workbench is locked, the task will be skipped.
         */
        run : function()
        {
            if (!called) {
                called = true;
                return;
            }

            if (_reception.isLocked()) {
                return;
            }

            this.peekIntoInbox(undefined, false);
        },

        /**
         * Method sends a request to the server to fetch new mails.
         *
         * @param {Number} accountId optional, the id of the account to fetch new
         * emails for. If not specified, the server will query all configured
         * accounts for new emails.
         * @param {Boolean} publish When set to false, the method will not publish
         * the 'com.conjoon.groupware.email.Letterman.peekIntoInbox' message
         *
         */
        peekIntoInbox : function(accountId, publish)
        {
            if (publish !== false) {
                Ext.ux.util.MessageBus.publish('com.conjoon.groupware.email.Letterman.peekIntoInbox', {
                    accountId : accountId
                });
            }

            if (store.proxy.activeRequest[Ext.data.Api.actions.read]) {
                return;
            }
            _busy = true;
            store.reload({
                params : {
                    accountId : accountId
                }
            });
        },

        /**
         * @param {Number} length A value > 0
         */
        callout : function(length)
        {
            var text = String.format(
                com.conjoon.Gettext.ngettext("You have one new email", "You have {0} new emails", length),
                length
            );

            new Ext.ux.ToastWindow({
                title   : com.conjoon.Gettext.ngettext("New email", "New emails", length),
                html    : text
            }).show(document);
        },

        /**
        *
        *
        */
        onLoad : function(store, records, options)
        {
            this.wakeup();
            store.removeAll();
            var length = records.length;
            _busy = false;
            _messageBroadcaster.publish('com.conjoon.groupware.email.Letterman.load', {
                items : records,
                total : length
            });
            if (length > 0) {
                this.callout(length)
            }
        },

        /**
         * This method is called if the server did not return a valid response,
         * or if the missingInboxForAccountId property in the response is set,
         * which points to the first account for which no inbox account was configured.
         *
         *
         * @param {Ext.data.Proxy} proxy The proxy that sent the request
         * @param {String} type The value of this parameter will be either 'response'
         * or 'remote'.
         *  - 'response': An invalid response from the server was returned: either 404,
         *                500 or the response meta-data does not match that defined in
         *                the DataReader (e.g.: root, idProperty, successProperty).
         *   - 'remote':  A valid response was returned from the server having
         *                successProperty === false. This response might contain an
         *                error-message sent from the server. For example, the user may have
         *                failed authentication/authorization or a database validation error
         *                occurred.
         * @param {String} action Name of the action (see Ext.data.Api.actions)
         * @param {Object} options The options for the action that were specified in the
         * request.
         * @param {Object} response The value of this parameter depends on the value of the
         * type parameter:
         *   - 'response': The raw browser response object (e.g.: XMLHttpRequest)
         *   - 'remote': The decoded response object sent from the server.
         * @param {Mixed} arg The type and value of this parameter depends on the value of
         * the type parameter:
         *   - 'response': Error The JavaScript Error object caught if the configured Reader
         *                 could not read the data. If the remote request returns
         *                 success===false, this parameter will be null.
         *   - 'remote': Record/Record[] This parameter will only exist if the action was a
         *               write action (Ext.data.Api.actions.create|update|destroy).
         *
         */
        onRequestFailure : function(proxy, type, action, options, response, arg)
        {
            _busy = false;
            _messageBroadcaster.publish('com.conjoon.groupware.email.Letterman.loadexception', {});
            this.wakeup();

            if (response.raw && response.raw.missingInboxForAccountId) {
                var accountId = parseInt(response.raw.missingInboxForAccountId);
                com.conjoon.groupware.email.options.FolderMappingBaton.showNotice(
                    accountId, 'INBOX'
                );
                return;
            } else {
                com.conjoon.groupware.ResponseInspector.handleFailure(response);
            }
        },

        /**
         * Tells whether the letterman is currently busy, i.e. looking
         * for new messages
         *
         * @return {Boolean}
         */
        isBusy : function()
        {
            return _busy;
        }


    };


}().init();