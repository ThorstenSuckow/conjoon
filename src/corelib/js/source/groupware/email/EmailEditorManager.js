/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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
 * The EmailEditorManager Singleton allows for creating an instance of EmailForm
 * that will be reused for every email that will be written once the object was
 * instantiating. The form values will be stored in a set that maps to the
 * individual panel id. Upon deactivatin/activating the values will be stored
 * and set depending which panel shows.
 *
 *
 */
com.conjoon.groupware.email.EmailEditorManager = function(){

    var STATE_LOADING = 1;
    var STATE_SAVING  = 2;
    var STATE_SENDING = 3;
    var STATE_MOVING  = 4;

    var contentPanel = null;

    var utilDom = com.conjoon.util.Dom;

    var tabIdCount = 0;
    var tabCount   = 0;

    var controlBar  = null;
    var masterPanel = null;
    var form        = null;
    var activePanel = null;

    var subjectField   = null;
    var htmlEditor     = null;
    var accountField   = null;
    var recipientStore = null;
    var recipientsGrid = null;

    var messages = {
        loading : com.conjoon.Gettext.gettext("Loading message..."),
        saving  : com.conjoon.Gettext.gettext("Saving draft..."),
        sending : com.conjoon.Gettext.gettext("Sending message..."),
        outbox  : com.conjoon.Gettext.gettext("Moving message...")
    };


    var formValues = {};

    var activePanelMasks = [];

    var createPanel = function(emailItemRecord, type, recipient, position)
    {
        var draftId = -1;

        if (emailItemRecord instanceof com.conjoon.groupware.email.EmailItemRecord) {
            draftId = emailItemRecord.id;
        } else {
            draftId = emailItemRecord;
            emailItemRecord = null;
        }

        // check here if we need to edit a draft. If an editor with the specified draft
        // is already opened, change to this tab
        if (type == 'edit' && draftId > 0) {
            for (var i in formValues) {
                if (formValues[i].id == draftId) {
                    contentPanel.setActiveTab(i);
                    return;
                }
            };
        }

        if (form == null) {

            form = new com.conjoon.groupware.email.form.EmailForm({
                id                : 'DOM:com.conjoon.groupware.email.EmailEditor.form',
                layout            : 'border',
                region            : 'center',
                border            : false,
                hideMode          : 'offsets',
                autoScroll        : false
            });
            form.on('render', function(){
                this.loadMask = new Ext.LoadMask(this.el.dom.id, {msg : messages.loading});
                this.loadMask.el.dom.style.zIndex = 1;
            }, form, {single : true});


            masterPanel = new Ext.Panel({
                id         : 'DOM:com.conjoon.groupware.email.EmailEditor.masterTab',
                layout     : 'fit',
                items      : [form],
                hideMode   : 'offsets',
                border     : false,
                autoScroll : false
            });

            masterPanel.on('deactivate', onDeactivatePanel, emailEditor);

            subjectField = form.subjectField;

            var lConfig = {
                keyup    : onSubjectValueChange,
                keydown  : onSubjectValueChange,
                keypress : onSubjectValueChange,
                scope    : com.conjoon.groupware.email.EmailEditorManager
            };

            form.mon(subjectField, lConfig);

            recipientsGrid = form.grid;
            form.mon(recipientsGrid, 'afteredit', onAfterEdit, com.conjoon.groupware.email.EmailEditorManager);

            accountField   = form.fromComboBox;
            form.mon(accountField, 'select', onAccountSelect, com.conjoon.groupware.email.EmailEditorManager);

            htmlEditor     = form.htmlEditor;
            htmlEditor.on('initialize' , function(){
                var fly = Ext.fly(this.getDoc());
                fly.addKeyListener([10, 13], onHtmlEditorEdit,
                    com.conjoon.groupware.email.EmailEditorManager, {stopEvent:true});
            }, htmlEditor, {single : true});

            recipientStore = form.gridStore;

            contentPanel.add(masterPanel);
            document.getElementById(contentPanel.id+'__'+masterPanel.id).style.display = 'none';
            contentPanel.setActiveTab(masterPanel);

        }

        var panel = new Ext.Panel({
            id         : 'DOM:com.conjoon.groupware.email.EmailEditor.tab_'+tabIdCount,
            bodyStyle  : 'display:none',
            title      : com.conjoon.Gettext.gettext("Loading..."),
            hideMode   : 'offsets',
            closable   : true,
            border     : false,
            iconCls    : 'com-conjoon-groupware-pending-icon',
            autoScroll : false
        });

        formValues[panel.id] = {
            state             : STATE_LOADING,
            emailItemRecord   : emailItemRecord,
            signatureAttached : false,
            dirty             : false,
            pending           : false,
            disabled          : true,
            subject           : "",
            message           : "",
            accountId         : null,
            sourceEditMode    : false,
            recipients        : [],
            requestId         : null
        };

        var ajaxOptions = {
            url            : './groupware/email.edit/get.draft/format/json',
            disableCaching : true,
            panelId        : panel.id,
            params         : {
                id      : draftId || -1,
                type    : type || 'new',
                name    : '',
                address : ''
            },
            success : onDraftLoad,
            failure : onDraftLoadException,
            scope   : com.conjoon.groupware.email.EmailEditorManager
        };

        if (recipient) {
            Ext.apply(ajaxOptions.params, {
                name             : recipient.name,
                address          : recipient.address,
                contentTextPlain : recipient.contentTextPlain ? recipient.contentTextPlain : "",
                subject          : recipient.subject ? recipient.subject : ""
            });
        }

        formValues[panel.id].requestId = Ext.Ajax.request(ajaxOptions);

        registerToolbar();

        var emailEditor = com.conjoon.groupware.email.EmailEditorManager;

        panel.on('render',     onActivatePanel,   emailEditor);
        panel.on('activate',   onActivatePanel,   emailEditor);
        panel.on('destroy',    onDestroyPanel,    emailEditor);

        contentPanel.on('beforeremove',  onBeforeClose, emailEditor);
        contentPanel.on('beforedrop',    onBeforeDrop,  emailEditor);
        contentPanel.on('drop',          onDrop,        emailEditor);

        if (Ext.isNumber(position)) {
            contentPanel.insert(position, panel);
        } else {
            contentPanel.add(panel);
        }

        contentPanel.setActiveTab(panel);

        tabCount++;
        tabIdCount++;
    };

    var onBeforeDrop = function(data)
    {
        contentPanel.un('beforeremove', onBeforeClose, com.conjoon.groupware.email.EmailEditorManager);
    };

    var onDrop = function(data)
    {
        contentPanel.on('beforeremove', onBeforeClose, com.conjoon.groupware.email.EmailEditorManager);
    };

    var onBeforeClose = function(container, component)
    {
        var id = component.el.dom.id;
        var grp = id.split('_');

        if (grp[0] == 'DOM:com.conjoon.groupware.email.EmailEditor.tab') {

            if (!activePanel || activePanel.id != id) {
                container.setActiveTab(component);
            }

            if (formValues[id].pending) {
                return false;
            }

            recipientsGrid.stopEditing();

            if (formValues[id].dirty) {
                var msg   = Ext.MessageBox;
                msg.show({
                    title   : com.conjoon.Gettext.gettext("Confirm - close message"),
                    msg     : com.conjoon.Gettext.gettext("You did not send this message. Do you really want to close this message without saving?"),
                    buttons : msg.YESNO,
                    fn      : function(btn){
                                  if (btn == 'yes') {
                                      contentPanel.un('beforeremove',  onBeforeClose,    com.conjoon.groupware.email.EmailEditorManager);
                                      container.remove(component);
                                      if (tabCount > 0) {
                                        contentPanel.on('beforeremove',  onBeforeClose,    com.conjoon.groupware.email.EmailEditorManager);
                                      }
                                  }
                              },
                    icon    : msg.QUESTION,
                    cls     :'com-conjoon-msgbox-question',
                    width   : 375
                });

                return false;
           }
        }


        return true;
    };

    var onDraftLoadException = function(response, options)
    {
        delete formValues[options.panelId];

        contentPanel.un('beforeremove',  onBeforeClose, com.conjoon.groupware.email.EmailEditorManager);
        contentPanel.remove(Ext.getCmp(options.panelId));
        contentPanel.on('beforeremove',  onBeforeClose, com.conjoon.groupware.email.EmailEditorManager);

        com.conjoon.groupware.ResponseInspector.handleFailure(response);
    };

    /**
     * Callback for the successfull loading of a draft. This method awaits a fully
     * configured Conjoon_Modules_Groupware_Email_Draft in the response property
     * "draft".
     *
     * @param {XmlHttpResponse} response
     * @param {Object} options
     *
     */
    var onDraftLoad = function(response, options)
    {
        if (!formValues[options.panelId]) {
            return;
        }

        formValues[options.panelId].requestId = null;

        var data = com.conjoon.groupware.ResponseInspector.isSuccess(response);

        if (!data) {
            return onDraftLoadException(response, options);
        }

        var draft = data.draft;
        var type  = data.type;

        var recRecs         = [];
        var recipientRecord = com.conjoon.groupware.email.data.RecipientRecord;
        var add = null;

        // get all the recipients
        var len = Math.max(draft.to.length, draft.cc.length, draft.bcc.length);

        for (var i = 0; i < len; i++) {
            add = draft.to[i];
            if (add) {
                recRecs.push(new recipientRecord({
                    receiveType : 'to',
                    address     : add['name']
                                ? add['name'] + " <" + add['address']+">"
                                : add['address']
                }));
            }

            add = draft.cc[i];
            if (add) {
                recRecs.push(new recipientRecord({
                    receiveType : 'cc',
                    address     : add['name']
                                ? add['name'] + " <" + add['address']+">"
                                : add['address']
                }));
            }

            add = draft.bcc[i];
            if (add) {
                recRecs.push(new recipientRecord({
                    receiveType : 'cc',
                    address     : add['name']
                                ? add['name'] + " <" + add['address']+">"
                                : add['address']
                }));
            }
        }

        // Add an empty line so the user is able to submit another recipient
        recRecs.push(new recipientRecord({
                receiveType : 'to',
                address     : ''
        }));


        Ext.apply(formValues[options.panelId], {
            initialized : true,
            state       : null,
            id          : (data.type != 'edit' ? -1 : draft.id),
            type        : data.type,
            disabled    : false,
            references  : draft.references,
            inReplyTo   : draft.inReplyTo,
            subject     : draft.subject,
            message     : _decorateDraftText(draft.contentTextPlain),
            accountId   : draft.groupwareEmailAccountsId,
            recipients  : recRecs,
            folderId    : (data.type != 'edit' ? -1 : draft.groupwareEmailFoldersId)
        });

        completeForm(options.panelId);

        Ext.getCmp(options.panelId).setTitle(getTitle(draft.subject));
        Ext.getCmp(options.panelId).setIconClass(
            'com-conjoon-groupware-email-EmailForm-icon'
        );

        // mark form as clean, as it was just loaded from the server.
        formValues[options.panelId].dirty = false;
    };

    /**
     * Helps IE to stay in sync with midas. If not used when changes in the dom
     * occur (inital setting value of editor, quoting) the html gets rendered
     * wrong.
     *
     */
    var _layoutEditor = function()
    {
        if (Ext.isIE) {
            var els = htmlEditor.getDoc().body.getElementsByTagName('blockquote');

            for (var i = 0; i < els.length; i++) {

                var m = els[i].innerHTML;
                els[i].innerHTML = "";
                els[i].innerHTML = m;
            }
        }
    }

    /**
     * Wraps the message text after loading with a tag if needed.
     * While wrapping a pre tag around the text works in mozilla and safari,
     * IE won't, thus all whitespace-pairs get replaced with " &nbsp;" if
     * IE is detected.
     *
     * @param {String} text
     *
     * @return {String}
     */
    var _decorateDraftText = function(text)
    {
        if (!text) {
            return text;
        }

        return Ext.isIE
               ? '<div class="editorBodyWrap">'+com.conjoon.util.Format.replaceWhitespacePairs(text)+'</div>'
               : '<pre>'+text+'</pre>';
    };

    /**
     * Fills the editor with the values for the currently active tab.
     *
     * @param {String} panelId The id of the tab for which the editor should
     * be filled with the according values
     */
    var completeForm = function(panelId)
    {
        if (formValues[panelId].disabled == true) {

            controlBar.setDisabled(true);

            switch (formValues[panelId].state) {
                case STATE_SAVING:
                    showLoadMask('saving');
                break;
                case STATE_SENDING:
                    showLoadMask('sending');
                break;
                case STATE_MOVING:
                    showLoadMask('moving');
                break;
                default:
                    // return if the message is still loading
                    showLoadMask('loading');
                    return;

            }
        } else {
            controlBar.setDisabled(false);
            form.loadMask.hide();
        }

        if (!activePanel || activePanel.id != panelId) {
            return;
        }

        subjectField.setValue(formValues[panelId].subject);
        htmlEditor.setValue(formValues[panelId].message);

        if (formValues[panelId].accountId) {
            accountField.setValue(formValues[panelId].accountId);
            _attachSignature(panelId, formValues[panelId].accountId);
        }

        _layoutEditor();

        recipientStore.removeAll();
        recipientStore.add(formValues[panelId].recipients);
    };

    var init = function()
    {
        if (!contentPanel) {
            contentPanel = com.conjoon.util.Registry.get('com.conjoon.groupware.ContentPanel');
        }
    };

    var registerToolbar = function(panel)
    {
        if (controlBar == null) {
            var tbarManager = com.conjoon.groupware.workbench.ToolbarController;

            controlBar = new Ext.Toolbar([{
                cls     : 'x-btn-text-icon',
                iconCls : 'com-conjoon-groupware-email-EmailForm-toolbar-buttonSend-icon',
                text    : '&#160;'+com.conjoon.Gettext.gettext("Send now"),
                handler : function() {
                    _manageDraft('send');
                }
            },{
                cls     : 'x-btn-text-icon',
                iconCls : 'com-conjoon-groupware-email-EmailForm-toolbar-buttonOutbox-icon',
                text    : '&#160;'+com.conjoon.Gettext.gettext("Move to outbox"),
                handler : function() {
                    _manageDraft('outbox');
                }
            } ,'-', {
                cls     : 'x-btn-text-icon',
                iconCls : 'com-conjoon-groupware-email-EmailForm-toolbar-buttonDraft-icon',
                text    : '&#160;'+com.conjoon.Gettext.gettext("Save as draft"),
                handler : function() {
                    _manageDraft('edit');
                }
            }]);

            tbarManager.register('com.conjoon.groupware.email.EmailForm.toolbar', controlBar);
        }
    };

    var cacheFormValues = function(panelId)
    {
        formValues[panelId].subject = subjectField.getValue();
        if (!htmlEditor.sourceEditMode) {
            htmlEditor.pushValue();
        }
        formValues[panelId].sourceEditMode = htmlEditor.sourceEditMode;
        formValues[panelId].message = htmlEditor.getValue();
        formValues[panelId].accountId  = accountField.getValue();
        formValues[panelId].recipients = [];
        for (var i = 0, max_i = recipientStore.getCount(); i < max_i; i++) {
            formValues[panelId].recipients.push(recipientStore.getAt(i).copy());
        }
    };

// -----------------------------Toolbar listeners----------------------------------

    /**
     * Tells the dispatcher to either move or send the draft currently
     * being edited in the editor or to save it.
     *
     * @param {String} type
     */
    var _manageDraft = function(type)
    {
        if (!type) {
            return;
        }

        recipientsGrid.stopEditing();

        var panelId = activePanel.id;

        var draftRecord = _prepareDataToSend(panelId, type);

        switch (type) {
            case 'send':
                com.conjoon.groupware.email.Dispatcher.sendEmail(
                    draftRecord,
                    (formValues[panelId].emailItemRecord ? formValues[panelId].emailItemRecord : null),
                    {panelId            : panelId,
                     setSubjectCallback : {
                        fn    : com.conjoon.groupware.email.EmailEditorManager.setSubject,
                        scope : com.conjoon.groupware.email.EmailEditorManager,
                        args  : [panelId]
                     }}
                );
            break;

            case 'outbox':
                com.conjoon.groupware.email.Dispatcher.moveDraftToOutbox(
                    draftRecord,
                    (formValues[panelId].emailItemRecord ? formValues[panelId].emailItemRecord : null),
                    {panelId            : panelId,
                     setSubjectCallback : {
                        fn    : com.conjoon.groupware.email.EmailEditorManager.setSubject,
                        scope : com.conjoon.groupware.email.EmailEditorManager,
                        args  : [panelId]
                     }}
                );
            break;

            case 'edit':
                com.conjoon.groupware.email.Dispatcher.saveDraft(
                    draftRecord,
                    (formValues[panelId].emailItemRecord ? formValues[panelId].emailItemRecord : null),
                    {panelId            : panelId,
                     setSubjectCallback : {
                        fn    : com.conjoon.groupware.email.EmailEditorManager.setSubject,
                        scope : com.conjoon.groupware.email.EmailEditorManager,
                        args  : [panelId]
                     }}
                );
            break;
        }
    };

    /**
     * Callback before an email is moved to the outbox or send or a draft is saved.
     * This callback is defined using the MessageBus. It is possible that this method
     * is called when sending an email is triggered from another component.

     *
     * This implementation will both handle the messages
     * com.conjoon.groupware.email.Smtp.beforeEmailSent
     * and
     * com.conjoon.groupware.email.outbox.beforeEmailMove
     * and
     * com.conjoon.groupware.email.editor.beforeDraftSave
     *
     * @param {String} subject
     * @param {Object} message
     */
    var _onBeforeDraftHandle = function(subject, message)
    {
        var options = message.options;

        if (!options) {
            throw ('expected panelId in message\'s options but was not available.');
        }

        var panelId = options.panelId;

        // check if a panelId is available in the options.
        // if that is not the case, the EditorManager did not trigger this message
        // and we can exit here
        if (!panelId || !formValues[panelId]) {
            return;
        }

        formValues[panelId].disabled = true;
        formValues[panelId].pending  = true;

        if (panelId != activePanel.id) {
            return;
        }

        activePanel.setIconClass('com-conjoon-groupware-pending-icon');

        switch (subject) {
            case 'com.conjoon.groupware.email.Smtp.beforeEmailSent':
                formValues[panelId].state = STATE_SENDING;
                showLoadMask('sending');
            break;

            case 'com.conjoon.groupware.email.outbox.beforeEmailMove':
                formValues[panelId].state = STATE_MOVING;
                showLoadMask('outbox');
            break;

            case 'com.conjoon.groupware.email.editor.beforeDraftSave':
                formValues[panelId].state = STATE_SAVING;
                showLoadMask('saving');
            break;
        }

        controlBar.setDisabled(true);
    };

    /**
     * Callback for successfully saving/sending an email/ moving an email to the outbox.
     *
     * This implementation will both handle the messages
     * com.conjoon.groupware.email.Smtp.emailSent
     * and
     * com.conjoon.groupware.email.outbox.emailMove
     * and
     * com.conjoon.groupware.email.editor.draftSave
     *
     * @param {String} subject
     * @param {Object} message
     */
    var _onDraftHandleSuccess = function(subject, message)
    {
        // check if a panelId is available in the options.
        // if that is not the case, the EditorManager did not trigger this message
        // and we can exit here
        if (!message.options || (message.options && (!message.options.panelId || !formValues[message.options.panelId]))) {
            return;
        }

        var panelId = message.options.panelId;
        clearPendingState(panelId);

        formValues[panelId].emailItemRecord = message.itemRecord.copy();
        formValues[panelId].state = null;

        if (subject == 'com.conjoon.groupware.email.editor.draftSave') {

            // allow changing some properties if and only if this draft was created from
            // scratch
            formValues[panelId].id   = message.itemRecord.id;
            formValues[panelId].type = 'edit';
            formValues[panelId].folderId = message.itemRecord.get('groupwareEmailFoldersId');

            formValues[panelId].dirty = false;
        } else {
            contentPanel.un('beforeremove',  onBeforeClose, com.conjoon.groupware.email.EmailEditorManager);
            contentPanel.remove(Ext.getCmp(panelId));
            contentPanel.on('beforeremove',  onBeforeClose, com.conjoon.groupware.email.EmailEditorManager);
        }
    };

    /**
     * Callback for an unsuccessfull attempt to save/send an email/ move an email
     * to the outbox.
     * This implementation will both handle the messages
     * com.conjoon.groupware.email.Smtp.emailSentFailure
     * and
     * com.conjoon.groupware.email.outbox.emailMoveFailure
     * and
     * com.conjoon.groupware.email.editor.draftSaveFailure
     *
     * @param {String} subject
     * @param {Object} message
     */
    var _onDraftHandleFailure = function(subject, message)
    {
        // check if a panelId is available in the options.
        // if that is not the case, the EditorManager did not trigger this message
        // and we can exit here
        if (!message.options || (message.options && (!message.options.panelId || !formValues[message.options.panelId]))) {
            return;
        }

        clearPendingState(message.options.panelId);
    };

    /**
     * Prepares the data from the email form to be send to the server.
     * Returns an object with all needed properties.
     *
     * @param {String} panelId The id of the panel that holds the email to be worked
     * on
     * @param {String} type The context the data should be prepared for.
     * Can be 'send', 'outbox' or 'edit'
     *
     * @return {Object}
     */
    var _prepareDataToSend = function(panelId, type)
    {
        var to  = [];
        var cc  = [];
        var bcc = [];

        var receiveType = null;
        var address     = null;
        var recipients = recipientStore.getRange();
        for (var i = 0, max_i = recipients.length; i < max_i; i++) {
            address = recipients[i].get('address').trim();
            if (address != "") {
                receiveType = recipients[i].get('receiveType');
                switch (receiveType) {
                    case 'to':
                        to.push(address);
                    break;

                    case 'cc':
                        cc.push(address);
                    break;

                    case 'bcc':
                        bcc.push(address);
                    break;
                }
            }
        }

        htmlEditor.syncValue();

        var fValues = formValues[panelId];

        var params = {
            format       : 'text/plain', // can be 'text/plain', 'text/html' or 'multipart'
            id           : (
                (fValues.type == 'edit' || type == 'edit')
                ? fValues.id
                : -1
            ),
            referencesId : (fValues.type == 'edit'
                            ? -1
                            : (fValues.emailItemRecord ? fValues.emailItemRecord.id : -1)),
            // this differs from the passed argument as this is the context of the email
            // being written, i.e. reply, reply_all, forward, new or edit
            type         : fValues.type,
            inReplyTo    : fValues.inReplyTo,
            references   : fValues.references,
            date         : Math.floor((new Date().getTime())/1000),
            subject      : subjectField.getValue(),
            message      : htmlEditor.getValue(),
            to           : to.length  > 0 ? Ext.encode(to)  : '',
            cc           : cc.length  > 0 ? Ext.encode(cc)  : '',
            bcc          : bcc.length > 0 ? Ext.encode(bcc) : '',
            groupwareEmailFoldersId : (
                (fValues.type == 'edit' || type == 'edit')
                ? fValues.folderId
                : -1
            ),
            groupwareEmailAccountsId : accountField.getValue()
        };

        var rec = new com.conjoon.groupware.email.data.Draft(
            params, params.id
        );

        cacheFormValues(panelId);

        return rec;
    };


    var clearPendingState = function(id)
    {
        var panelId = activePanel ? activePanel.id : null;

        Ext.getCmp(id).setIconClass('com-conjoon-groupware-email-EmailForm-icon');

        formValues[id].state    = null;
        formValues[id].disabled = false;
        formValues[id].pending  = false;

        completeForm(id);
    };

// -----------------------------Form listeners----------------------------------

    /**
     * Callback whenever the return or enter key was pressed in the html editor.
     * This will search for the first blockquote and split it in half to
     * make quoting possible.
     *
     */
    var onHtmlEditorEdit = function(string, eventObject)
    {
        var doc = htmlEditor.getDoc();

        var splitRange = Ext.isIE
                         ? doc.selection.createRange()
                         : htmlEditor.win.getSelection().getRangeAt(0);

        var id = '_'+(new Date()).getTime();

        if (Ext.isIE) {
            splitRange.pasteHTML('<span id="'+id+'"></span>');
            splitRange.collapse(false);
            splitRange.select();
        } else {
            htmlEditor.execCmd('insertHTML', '<span id="'+id+'"></span>');
        }

        var splitter = doc.getElementById(id);
        splitter.id = "";
        var parent = splitter.parentNode;
        var quoteEl = null;
        var tagName = "";
        while (parent && parent.tagName) {
            tagName = parent.tagName.toLowerCase();
            if (tagName == 'blockquote') {
                quoteEl = parent;
            } else if (quoteEl) {
                break;
            }
            parent = parent.parentNode;
        }


        if (quoteEl) {
            eventObject.stopEvent();

            var splitterClone = splitter.cloneNode(false);
            var dividedNode   = utilDom.divideNode(splitter , splitterClone, quoteEl);

            var div = doc.createElement('div');
            div.className = 'text';
            div.innerHTML="&nbsp;";

            if(!quoteEl.nextSibling){
                quoteEl.parentNode.appendChild(div);
                quoteEl.parentNode.appendChild(dividedNode);
            } else {
                quoteEl.parentNode.insertBefore(dividedNode, quoteEl.nextSibling);
                quoteEl.parentNode.insertBefore(div, dividedNode);
            }

            if (Ext.isIE && splitRange) {
                splitRange.move("character", 2);
                splitRange.collapse(false);
                splitRange.select();
                div.innerHTML = "";
            } else {
                htmlEditor.win.getSelection().collapse(div, 0);
            }

            splitterClone.parentNode.removeChild(splitterClone);
            splitter.parentNode.removeChild(splitter);

            // remove all br, nbps that are the first childs in a blockquote,
            // and all resulting blockquotes that have no child
            // IE seems to add a nbsp to an empty tag, i.e. <b></b> becomes
            // <b>&nbsp;</b> when setting this tag as innerHTML
            dividedNode.innerHTML = dividedNode.innerHTML.replace(
                                        /<blockquote>?((<br>|&nbsp;|\s)*)/ig,
                                        "<blockquote>"
                                    ).replace(
                                        /<blockquote><\/blockquote>/ig,
                                        ""
                                    );

            // remove all trailing brs and nbsps of the quoted element
            quoteEl.innerHTML = quoteEl.innerHTML.replace(
                                    /((<br>|&nbsp;|\s)*)<\/blockquote>?/ig,
                                    "</blockquote>"
                                ).replace(
                                    /<blockquote><\/blockquote>/ig,
                                    ""
                                );

            _layoutEditor();


            var cq = quoteEl.innerHTML.replace(/<blockquote>|<\/blockquote>|<br>|\s|&nbsp;/ig, "").trim();
            if (cq == "") {
                quoteEl.parentNode.removeChild(quoteEl);
            }

            cq = dividedNode.innerHTML.replace(/<blockquote>|<\/blockquote>|<br>|\s|&nbsp;/ig, "").trim();
            if (cq == "") {
                dividedNode.parentNode.removeChild(dividedNode);
            }
        } else {
            splitter.parentNode.removeChild(splitter);
        }

    };



    var getTitle = function(value)
    {
        value = (value != undefined ? value+"" : "").trim();

        if (value == "") {
            value = com.conjoon.Gettext.gettext("(no subject)");
        } else {
            value = Ext.util.Format.htmlEncode(value);
        }

        return value;
    };

    /**
     * Listener for the key events of the subject input field.
     * Sets the title of the currently active panel to the html encoded value
     * of the subject text field.
     *
     * @param {Ext.form.TextField}
     * @param {Ext.Eventobject} eventObject
     */
    var onSubjectValueChange = function(textField, eventObject)
    {
        var panelId = activePanel.id;

        formValues[activePanel.id].dirty = true;
        activePanel.setTitle(getTitle(subjectField.getValue().trim()));
    }

    /**
     * grid - This grid
     * record - The record being edited
     * field - The field name being edited
     * value - The value being set
     * originalValue - The original value for the field, before the edit.
     * row - The grid row index
     * column - The grid column index
     */
    var onAfterEdit = function(editObject)
    {
        var row   = editObject.row,
            value = editObject.value,
            grid  = editObject.grid,
            store = grid.store;

        store.commitChanges();

        formValues[activePanel.id].dirty = true;

        if (value.trim() != "" && store.getAt(row).data.address.trim() != "" &&
            store.getAt(row+1) == null) {

            var tmpRecord = new com.conjoon.groupware.email.data.RecipientRecord({
                receiveType : 'to',
                address : ''
            });

            store.insert(row+1, tmpRecord);
            grid.view.ensureVisible(row+1, 0);
        }
    };

    /**
     * Listener for the select event of the "from"-combobox of the EmailForm.
     *
     * @param {Object} comboBox
     * @param {Object} record
     * @param {Number} index
     */
    var onAccountSelect = function(comboBox, record, index)
    {
        formValues[activePanel.id].dirty = true;
        _attachSignature(activePanel.id, record.id, true);

    };

    /**
     *
     * @param {Object} panelId
     * @param {Object} accountId
     * @param {Boolean} refresh Wether the signature has to be re-build if the
     * signature's container was not found. This should be set to true whenever
     * the "select"-event of the account field is involved
     */
    var _attachSignature = function(panelId, accountId, refresh)
    {
        var store = com.conjoon.groupware.email.AccountStore.getInstance();
        var rec   = store.getById(accountId);

        var sigId = formValues[panelId]['signatureAttached'];

        // no signature used yet
        if (sigId === false) {
            if (!rec || !rec.get('isSignatureUsed')) {
                return;
            }
            var signature = rec.get('signature')
            formValues[panelId]['signatureAttached'] = '_' + (new Date()).getTime();
            htmlEditor.setValue(
                htmlEditor.getValue()
                + _getSignatureHtml(signature, formValues[panelId]['signatureAttached'])
            );
        } else {
            var doc    = htmlEditor.getDoc();
            var sigDiv = doc.getElementById(sigId);

            // the selected account does not have a signature configured,
            // remove any currently used signature
            if (!rec || !rec.get('isSignatureUsed')) {
                if (!sigDiv) {
                    return;
                } else {
                    sigDiv.parentNode.removeChild(sigDiv);
                    formValues[panelId]['signatureAttached'] = false;
                    htmlEditor.syncValue();
                    return;
                }
            }

            if (!sigDiv && refresh === true) {
                var sig = _getSignatureHtml(
                    rec.get('signature'),
                    formValues[panelId]['signatureAttached']
                );
                htmlEditor.setValue(htmlEditor.getValue()+sig);
            } else if (sigDiv && refresh === true) {
                sigDiv.innerHTML = _prepareSignature(rec.get('signature'));
                htmlEditor.syncValue();
            }
        }
    };

    var _prepareSignature = function(signature)
    {
        signature = signature.replace(/\r\n/g, "<br />");
        signature = signature.replace(/\r/g, "<br />");
        signature = signature.replace(/\n/g, "<br />");

        return '-- <br />' + signature;
    };

    var _getSignatureHtml = function(signature, id)
    {

        return '<br /><br /><div ' +
               'class="signature" ' +
               'id="' +
                id +
               '">' +
               _prepareSignature(signature) +
               '</div>';
    };

    var onMessageEdit = function()
    {
        formValues[activePanel.id].dirty = true;
    };

// ----------------------------Panel listeners----------------------------------
    var onDeactivatePanel = function(panel)
    {
        if (!activePanel) {
            return;
        }
        panel = activePanel;

        //htmlEditor.un('push', onMessageEdit, com.conjoon.groupware.email.EmailEditorManager);
        htmlEditor.un('sync', onMessageEdit, com.conjoon.groupware.email.EmailEditorManager);

        recipientsGrid.stopEditing();
        if (!formValues[panel.id].disabled) {
            cacheFormValues(panel.id);
        }

        htmlEditor.setValue('');
        subjectField.setValue('');
        recipientStore.removeAll();

        //htmlEditor.tb.items.get('sourceedit').pressed = false;
        //htmlEditor.toggleSourceEdit(false);
        //htmlEditor.toggleSourceEdit(false);
        //htmlEditor.tb.items.get('sourceedit').toggle(false);

        activePanel = null;
        var tbarManager = com.conjoon.groupware.workbench.ToolbarController;
        tbarManager.hide('com.conjoon.groupware.email.EmailForm.toolbar');
    }

    var onActivatePanel = function(panel)
    {
        if (!panel.rendered) {
            return;
        }

        activePanel = panel;
        panel.getActionEl().addClass('x-hide-' + panel.hideMode);
        masterPanel.getActionEl().removeClass('x-hide-' + masterPanel.hideMode);

        contentPanel.layout.activeItem = masterPanel;
        contentPanel.layout.layout();

        var tbarManager = com.conjoon.groupware.workbench.ToolbarController;
        tbarManager.show('com.conjoon.groupware.email.EmailForm.toolbar');

        completeForm(panel.id);
        //htmlEditor.on('push', onMessageEdit, com.conjoon.groupware.email.EmailEditorManager);
        htmlEditor.on('sync', onMessageEdit, com.conjoon.groupware.email.EmailEditorManager);
    };

    var onDestroyPanel = function(panel)
    {
        if (activePanel && activePanel.id === panel.id) {
            activePanel = null;
        }

        var reqId = formValues[panel.id].requestId;
        if (reqId) {
            Ext.Ajax.abort(reqId);
        }

        formValues[panel.id] = null;
        delete formValues[panel.id];

        tabCount--;
        if (tabCount == 0) {
            contentPanel.un('beforeremove',  onBeforeClose, com.conjoon.groupware.email.EmailEditorManager);
            contentPanel.remove(masterPanel, true);
            form = null;
            masterPanel = null;
            var tbarManager = com.conjoon.groupware.workbench.ToolbarController;
            tbarManager.destroy('com.conjoon.groupware.email.EmailForm.toolbar');
            controlBar = null;
            formValues = [];
            subjectField = null;
            recipientsGrid = null;
        }
    };

    var showLoadMask = function(type)
    {
        switch (type) {
            case 'loading':
                form.loadMask.msg = messages.loading;
            break;
            case 'saving':
                form.loadMask.msg = messages.saving;
            break;
            case 'sending':
                form.loadMask.msg = messages.sending;
            break;
            case 'outbox':
                form.loadMask.msg = messages.outbox;
            break;
        }

        form.loadMask.show();
    };

    /**
     * Subscribe to the message com.conjoon.groupware.email.Smtp.*
     */
    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.email.Smtp.beforeEmailSent',
        _onBeforeDraftHandle
    );
    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.email.Smtp.emailSent',
        _onDraftHandleSuccess
    );
    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.email.Smtp.emailSentFailure',
        _onDraftHandleFailure
    );
    /**
     * Subscribe to the message com.conjoon.groupware.email.outbox.*
     */
    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.email.outbox.beforeEmailMove',
        _onBeforeDraftHandle
    );
    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.email.outbox.emailMove',
        _onDraftHandleSuccess
    );
    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.email.outbox.emailMoveFailure',
        _onDraftHandleFailure
    );
    /**
     * Subscribe to the message com.conjoon.groupware.email.editor.*
     */
    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.email.editor.beforeDraftSave',
        _onBeforeDraftHandle
    );
    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.email.editor.draftSave',
        _onDraftHandleSuccess
    );
    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.email.editor.draftSaveFailure',
        _onDraftHandleFailure
    );


    return {

        /**
         * Sets the subject for the specified panel id.
         *
         *
         * @param {String} subject
         * @param {String} panelId
         */
        setSubject : function(subject, panelId)
        {
            if (activePanel && activePanel.getId() == panelId) {
                form.subjectField.setValue(subject);
            }

            var m = Ext.getCmp(panelId);
            if (m) {
                m.setTitle(getTitle(subject));
            }

            if (formValues[panelId]) {
                formValues[panelId].subject = subject;
                formValues[panelId].dirty   = true;
            }
        },

        /**
         * Creates a new panel to create/edit an email.
         *
         * @param {Number} id The id of the email to edit. If not supplied,
         * a raw form will be loaded to create a new email. If supplied,
         * corresponding data will be loaded from teh server to fill out the
         * form and edit it.
         * @param {String} type The type of the action, if an id was supplied.
         * Possible values are 'edit', 'reply', 'reply_all', 'forward', 'new'.
         * @param {Object} recipient optional, if supplied the id will be set to -1,
         * and the type to new. Most likely a mailto link was then clicked and the
         * user wants to write an email immediately to the email address. The properties
         * of this object are "name" and "address". Additionally, you might specify
         * the values "subject" and "contentTextPlain", which will be used to prefill the
         * emailForm then
         * @param {Number} position optional, lets you specify the index where to add
         * the panel to the contantPanel
         *
         */
        createEditor : function(id, type, recipient, position)
        {
            init();

            if (recipient) {
                id   = -1;
                type = 'new';
            }

            if (id == undefined || id == null || !id) {
                id = -1;
            }

            if (id == -1) {
                type = 'new';
            }

            createPanel(id, type, recipient, position);
        },

        /**
         * Returns true if the account with the specified id is currently
         * being used by an email form.
         *
         * @param {Number} accountId
         *
         * @return {Boolean} true if the account is currently in use, otherwise
         * false
         */
        isAccountUsed : function(accountId)
        {
            for (var i in formValues) {
                if (formValues[i].accountId == accountId) {
                    return true;
                }
            }

            return false;
        }
    };


}();