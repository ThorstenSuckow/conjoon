/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: EmailViewBaton.js 231 2008-10-12 17:44:31Z T. Suckow $
 * $Date: 2008-10-12 19:44:31 +0200 (So, 12 Okt 2008) $
 * $Revision: 231 $
 * $LastChangedDate: 2008-10-12 19:44:31 +0200 (So, 12 Okt 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild_rep/trunk/src/corelib/js/source/groupware/email/EmailViewBaton.js $
 */

Ext.namespace('de.intrabuild.groupware.email');

/**
 * Singleton that provides convinient methods for saving/sending emails.
 *
 * @class de.intrabuild.groupware.email.Dispatcher
 * @singleton
 */
de.intrabuild.groupware.email.Dispatcher = function() {

    var _onBulkSendSuccess = function(response, options)
    {
        var data = de.intrabuild.groupware.ResponseInspector.isSuccess(response);

        if (data == null) {
            _onBulkSendFailure(response, options);
            return;
        }

        var sentItems = data.sentItems;

        if (sentItems.length == 0) {
            _onBulkSendFailure(response, options);
            return;
        }

        var sentData  = [];
        for (var i = 0, len = sentItems.length; i < len; i++) {
            sentData.push(de.intrabuild.util.Record.convertTo(
                de.intrabuild.groupware.email.EmailItemRecord,
                sentItems[i],
                sentItems[i].id
            ));
        }

        Ext.ux.util.MessageBus.publish('de.intrabuild.groupware.email.Smtp.bulkSent', {
           emailItems : options.emailItems,
           sentItems  : sentData
        });

    };

    var _onBulkSendFailure = function(response, options)
    {
        de.intrabuild.groupware.ResponseInspector.handleFailure(response, {
            title : de.intrabuild.Gettext.gettext("Error - Could not send messages")
        });

        Ext.ux.util.MessageBus.publish('de.intrabuild.groupware.email.Smtp.bulkSentFailure', {
            emailItems : options.emailItems
        });
    };

    var _onManageDraftSuccess = function(response, options, type)
    {
        var data = de.intrabuild.groupware.ResponseInspector.isSuccess(response);

        if (data == null) {
            _onManageDraftFailure(response, options, type);
            return;
        }

        var itemRecord = de.intrabuild.util.Record.convertTo(
            de.intrabuild.groupware.email.EmailItemRecord,
            data.item,
            data.item.id
        );

        var subject = '';

        switch (type) {
            case 'send':
                subject = 'de.intrabuild.groupware.email.Smtp.emailSent';
            break;

            case 'outbox':
                subject = 'de.intrabuild.groupware.email.outbox.emailMove';
            break;

            case 'edit':
                subject = 'de.intrabuild.groupware.email.editor.draftSave';
            break;
        }

        Ext.ux.util.MessageBus.publish(subject, {
            referencedItem : options.referencedItem,
            draft          : options.draft,
            options        : options.additionalOptions,
            itemRecord     : itemRecord
        });
    };

    var _onManageDraftFailure = function(response, options, type)
    {
        var subject = '';
        var title   = '';

        switch (type) {
            case 'send':
                title   = de.intrabuild.Gettext.gettext("Error - Could not send message.");
                subject = 'de.intrabuild.groupware.email.Smtp.emailSentFailure';
            break;

            case 'outbox':
                title   = de.intrabuild.Gettext.gettext("Error - Could not move message to the outbox.");
                subject = 'de.intrabuild.groupware.email.outbox.emailMoveFailure';
            break;

            case 'edit':
                title   = de.intrabuild.Gettext.gettext("Error - Could not save draft.");
                subject = 'de.intrabuild.groupware.email.editor.draftSaveFailure';
            break;
        }

        de.intrabuild.groupware.ResponseInspector.handleFailure(response, {
            title : title
        });

        Ext.ux.util.MessageBus.publish(subject, {
            options        : options.additionalOptions,
            draft          : options.draft,
            referencedItem : options.referencedItem,
            response       : response
        });
    };

    var _manageDraft = function(draft, referencedItem, options, type)
    {
        // check if any valid email-addresses have been submitted
        if ((type == 'send' || type == 'outbox') &&
            (draft.get('to') == '' && draft.get('cc') == '' && draft.get('bcc') == '')) {
            var msg  = Ext.MessageBox;

            msg.show({
                title   : de.intrabuild.Gettext.gettext("Error - specify recipient(s)"),
                msg     : de.intrabuild.Gettext.gettext("Please specify one or more recipients for this message."),
                buttons : msg.OK,
                icon    : msg.WARNING,
                scope   : this,
                cls     :'de-intrabuild-msgbox-warning',
                width   : 400
            });

            return;
        }

        var subject     = '';
        var url         = '';

        switch (type) {
            case 'send':
                subject     = 'de.intrabuild.groupware.email.Smtp.beforeEmailSent';
                url         = '/groupware/email/send/format/json';
            break;

            case 'outbox':
                subject     = 'de.intrabuild.groupware.email.outbox.beforeEmailMove';
                url         = '/groupware/email/move.to.outbox/format/json';
            break;

            case 'edit':
                subject     = 'de.intrabuild.groupware.email.editor.beforeDraftSave';
                url         = '/groupware/email/save.draft/format/json';
            break;
        }

        Ext.ux.util.MessageBus.publish(subject, {
            draft          : draft,
            referencedItem : referencedItem,
            options        : options
        });

        var opts = {
            additionalOptions : options,
            draft             : draft,
            referencedItem    : referencedItem,
            url               : url,
            params            : draft.data,
            success           : function(response, options) {
                _onManageDraftSuccess(response, options, type);
            },
            failure           : function(response, options) {
                _onManageDraftFailure(response, options, type);
            },
            disableCaching    : true
        };

        Ext.Ajax.request(opts);
    };

    return {

        /**
         * Sends a request to the server to send the specified emailItems which
         * are currently pending in the outbox folder.
         *
         * @param {Array} emailItems An array of {de.intrabuild.groupware.email.EmailItemRecord}s
         * @param {Number} date unix timestamp. If specified, the supplied argument
         * will be used for setting the "date"-header in the emails to send.
         */
        sendPendingEmails : function(emailItems, date)
        {
            if (!Ext.isArray(emailItems) || emailItems.length == 0) {
                return;
            }

            Ext.ux.util.MessageBus.publish('de.intrabuild.groupware.email.Smtp.beforeBulkSent', {
               emailItems : emailItems
            });

            var ids = [];
            for (var i = 0, len = emailItems.length; i < len; i++) {
                ids.push(emailItems[i].get('id'));
            }

            var params = {
                ids  : Ext.encode(ids)
            };

            if (date) {
                Ext.apply(params, {
                    date : date
                });
            }

            var opts = {
                emailItems     : emailItems,
                url            : '/groupware/email/bulk.send/format/json',
                params         : params,
                success        : _onBulkSendSuccess,
                failure        : _onBulkSendFailure,
                disableCaching : true
            };

            Ext.Ajax.request(opts);
        },

        /**
         * Sends the specified data as an email message.
         *
         * @param {de.intrabuild.groupware.email.data.Draft} draft
         * @param {de.intrabuild.groupware.email.EmailItemRecord} referencedItem
         * @param {Object} options additional set of options to be used for the
         * Ext.Ajax.request.
         */
        sendEmail : function(draft, referencedItem, options)
        {
            _manageDraft(draft, referencedItem, options, 'send');
        },

        /**
         * Moves the specified draft into the outbox folder.
         *
         * @param {de.intrabuild.groupware.email.data.Draft} draft
         * @param {de.intrabuild.groupware.email.EmailItemRecord} referencedItem
         * @param {Object} options additional set of options to be used for the
         * Ext.Ajax.request.
         */
        moveDraftToOutbox : function(draft, referencedItem, options)
        {
            _manageDraft(draft, referencedItem, options, 'outbox');
        },

        /**
         * Saves the specified draft.
         *
         * @param {de.intrabuild.groupware.email.data.Draft} draft
         * @param {de.intrabuild.groupware.email.EmailItemRecord} referencedItem
         * @param {Object} options additional set of options to be used for the
         * Ext.Ajax.request.
         */
        saveDraft : function(draft, referencedItem, options)
        {
            _manageDraft(draft, referencedItem, options, 'edit');
        }

    };

}();