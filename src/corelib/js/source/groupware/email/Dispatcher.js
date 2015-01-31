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

Ext.namespace('com.conjoon.groupware.email');

/**
 * Singleton that provides convinient methods for saving/sending emails.
 *
 * @class com.conjoon.groupware.email.Dispatcher
 * @singleton
 */
com.conjoon.groupware.email.Dispatcher = function() {

    var _onBulkSendSuccess = function(response, options)
    {
        var data = com.conjoon.groupware.ResponseInspector.isSuccess(response);

        if (!data) {
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
            sentData.push(com.conjoon.util.Record.convertTo(
                com.conjoon.groupware.email.EmailItemRecord,
                sentItems[i],
                sentItems[i].id
            ));
        }

        var cris = [];
        var contextReferencedItems = data.contextReferencedItems;
        for (var i = 0, len = contextReferencedItems.length; i < len; i++) {
            cris.push(com.conjoon.util.Record.convertTo(
                com.conjoon.groupware.email.EmailItemRecord,
                contextReferencedItems[i],
                contextReferencedItems[i].id
            ));
        }

        Ext.ux.util.MessageBus.publish('com.conjoon.groupware.email.Smtp.bulkSent', {
            newVersions            : data.newVersions,
            emailItems             : options.emailItems,
            sentItems              : sentData,
            contextReferencedItems : cris
        });

    };

    var _onBulkSendFailure = function(response, options)
    {
        com.conjoon.groupware.ResponseInspector.handleFailure(response, {
            title : com.conjoon.Gettext.gettext("Error - Could not send messages")
        });

        Ext.ux.util.MessageBus.publish('com.conjoon.groupware.email.Smtp.bulkSentFailure', {
            emailItems : options.emailItems
        });
    };

    var _onManageDraftSuccess = function(response, options, type)
    {
        var data = com.conjoon.groupware.ResponseInspector.isSuccess(response);

        if (!data) {
            _onManageDraftFailure(response, options, type);
            return;
        }

        if (data.folderMappingError) {
            var msg  = Ext.MessageBox;
            msg.show({
                title   : data.folderMappingError.title,
                msg     : data.folderMappingError.message,
                buttons : msg.OK,
                icon    : msg.WARNING,
                scope   : this,
                cls     :'com-conjoon-msgbox-warning',
                width   : 400
            });
        }

        var itemRecord = null;
        if (data.item) {
            itemRecord = com.conjoon.util.Record.convertTo(
                com.conjoon.groupware.email.EmailItemRecord,
                data.item,
                data.item.id
            );
        }

        var subject = '';

        var pubObject = {
            referencedItem : options.referencedItem,
            draft          : options.draft,
            options        : options.additionalOptions,
            itemRecord     : itemRecord
        };

        switch (type) {
            case 'send':
                subject = 'com.conjoon.groupware.email.Smtp.emailSent';
                var cri = null;
                if (data.contextReferencedItem) {
                    cri = com.conjoon.util.Record.convertTo(
                        com.conjoon.groupware.email.EmailItemRecord,
                        data.contextReferencedItem,
                        data.contextReferencedItem.id
                    );
                    Ext.apply(pubObject, {
                        contextReferencedItem : cri
                    });
                }

                Ext.apply(pubObject, {
                    // tells if a new message was created for sending a draft.
                    // This basically happens on IMAP servers when a draft is
                    // for composing a new message. The message is created and
                    // stored with a new id in a "sent" flder, wehile the draft
                    // with the previousId gets removed from the server
                    newVersion : data.newVersion,
                    previousId : data.previousId
                });
            break;

            case 'outbox':
                subject = 'com.conjoon.groupware.email.outbox.emailMove';
            break;

            case 'edit':
                subject = 'com.conjoon.groupware.email.editor.draftSave';
                var emailRecord = com.conjoon.util.Record.convertTo(
                    com.conjoon.groupware.email.EmailRecord,
                    data.emailRecord,
                    data.emailRecord.id
                );
                Ext.apply(pubObject, {
                    emailRecord : emailRecord,
                    // tells if a new version was stored. this basically means that
                    // the draft that was previously edited was removed, and a new
                    // id for this draft is available
                    newVersion : data.newVersion,
                    previousId : data.previousId

                });
            break;
        }

        Ext.ux.util.MessageBus.publish(subject, pubObject);
    };

    var _onManageDraftFailure = function(response, options, type)
    {
        var subject = '';
        var title   = '';

        switch (type) {
            case 'send':
                title   = com.conjoon.Gettext.gettext("Error - Could not send message.");
                subject = 'com.conjoon.groupware.email.Smtp.emailSentFailure';
            break;

            case 'outbox':
                title   = com.conjoon.Gettext.gettext("Error - Could not move message to the outbox.");
                subject = 'com.conjoon.groupware.email.outbox.emailMoveFailure';
            break;

            case 'edit':
                title   = com.conjoon.Gettext.gettext("Error - Could not save draft.");
                subject = 'com.conjoon.groupware.email.editor.draftSaveFailure';
            break;
        }

        com.conjoon.groupware.ResponseInspector.handleFailure(response, {
            title : title
        });

        Ext.ux.util.MessageBus.publish(subject, {
            options        : options.additionalOptions,
            draft          : options.draft,
            referencedItem : options.referencedItem,
            response       : response
        });
    };

    var _manageDraft = function(draft, referencedItem, options, type, checkSubject)
    {
        // check if any valid email-addresses have been submitted
        if (type == 'send' || type == 'outbox') {

            if (draft.get('to') == '' && draft.get('cc') == '' && draft.get('bcc') == '') {
                var msg  = Ext.MessageBox;

                msg.show({
                    title   : com.conjoon.Gettext.gettext("Error - specify recipient(s)"),
                    msg     : com.conjoon.Gettext.gettext("Please specify one or more recipients for this message."),
                    buttons : msg.OK,
                    icon    : msg.WARNING,
                    scope   : this,
                    cls     :'com-conjoon-msgbox-warning',
                    width   : 400
                });

                return;
            }

            // check if subject is available
            if (draft.get('subject').trim() == "" && checkSubject !== false) {
                var msg = new com.conjoon.SystemMessage({
                    title : com.conjoon.Gettext.gettext("Missing subject"),
                    text  : com.conjoon.Gettext.gettext("You did not specify a subject for this message. If you want to specify a subject, you can do so now."),
                    type  : com.conjoon.SystemMessage.TYPE_PROMPT
                });

                com.conjoon.SystemMessageManager.prompt(msg, {
                    value : com.conjoon.Gettext.gettext("(no subject)"),
                    fn    : function(button, text) {
                        if (button != 'ok') {
                            return;
                        }
                        text = text == undefined ? "" : text+"";
                        draft.set('subject', text);

                        if (options.setSubjectCallback) {
                            var cb   = options.setSubjectCallback;
                            var args = [text].concat(cb.args);
                            cb.fn.apply(cb.scope, args);
                        }

                        _manageDraft(draft, referencedItem, options, type, false);
                    }
                });

                return;
            }
        }

        var subject = '';
        var url     = '';
        var timeout = undefined;

        switch (type) {
            case 'send':
                subject     = 'com.conjoon.groupware.email.Smtp.beforeEmailSent';
                url         = './groupware/email.send/send/format/json';
                timeout     = 1200000;
            break;

            case 'outbox':
                subject     = 'com.conjoon.groupware.email.outbox.beforeEmailMove';
                url         = './groupware/email.edit/move.to.outbox/format/json';
            break;

            case 'edit':
                subject     = 'com.conjoon.groupware.email.editor.beforeDraftSave';
                url         = './groupware/email.edit/save.draft/format/json';
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
            timeout           : timeout,
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
         * @param {Array} emailItems An array of {com.conjoon.groupware.email.EmailItemRecord}s
         * @param {Number} date unix timestamp. If specified, the supplied argument
         * will be used for setting the "date"-header in the emails to send.
         */
        sendPendingEmails : function(emailItems, date)
        {
            if (!Ext.isArray(emailItems) || emailItems.length == 0) {
                return;
            }

            Ext.ux.util.MessageBus.publish('com.conjoon.groupware.email.Smtp.beforeBulkSent', {
               emailItems : emailItems
            });

            var ids = [];
            for (var i = 0, len = emailItems.length; i < len; i++) {
                ids.push({
                    id   : emailItems[i].get('id'),
                    path : emailItems[i].get('path')
                });
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
                url            : './groupware/email.send/bulk.send/format/json',
                params         : params,
                timeout        : 1200000,
                success        : _onBulkSendSuccess,
                failure        : _onBulkSendFailure,
                disableCaching : true
            };

            Ext.Ajax.request(opts);
        },

        /**
         * Sends the specified data as an email message.
         *
         * @param {com.conjoon.groupware.email.data.Draft} draft
         * @param {com.conjoon.groupware.email.EmailItemRecord} referencedItem
         * @param {Object} options additional set of options to be used for the
         * Ext.Ajax.request.
         *`@param {Boolean} checkSubject Whether to check for a missing subject
         */
        sendEmail : function(draft, referencedItem, options, checkSubject)
        {
            _manageDraft(draft, referencedItem, options, 'send', checkSubject);
        },

        /**
         * Moves the specified draft into the outbox folder.
         *
         * @param {com.conjoon.groupware.email.data.Draft} draft
         * @param {com.conjoon.groupware.email.EmailItemRecord} referencedItem
         * @param {Object} options additional set of options to be used for the
         * Ext.Ajax.request.
         *`@param {Boolean} checkSubject Whether to check for a missing subject
         */
        moveDraftToOutbox : function(draft, referencedItem, options, checkSubject)
        {
            _manageDraft(draft, referencedItem, options, 'outbox', checkSubject);
        },

        /**
         * Saves the specified draft.
         *
         * @param {com.conjoon.groupware.email.data.Draft} draft
         * @param {com.conjoon.groupware.email.EmailItemRecord} referencedItem
         * @param {Object} options additional set of options to be used for the
         * Ext.Ajax.request.
         */
        saveDraft : function(draft, referencedItem, options)
        {
            _manageDraft(draft, referencedItem, options, 'edit');
        }

    };

}();