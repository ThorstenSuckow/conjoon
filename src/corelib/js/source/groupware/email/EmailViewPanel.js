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

com.conjoon.groupware.email.EmailViewPanel = Ext.extend(Ext.Panel, {

    /**
     * @cfg {Object} viewConfig
     * Used only if no custom view is specified using the config.
     */
    viewConfig : {},

    /**
     * @cfg {Boolean} refreshFrame
     * Will totally rebuild the iframe when the panel gets hidden/shown for any
     * other browser than IE.
     */

    /**
     * @cfg {Boolean} autoLoad
     * Overrides completely the functionality of the parents implementation.
     * If autoLoad is true, the according message will be loaded immediately
     * after this component was rendered, otherwise a blank panel will be rendered.
     * Set this to <tt>false</tt> to load messages manually for this panel.
     */

    /**
     * @cfg {com.conjoon.groupware.email.EmailItemRecord} emailItem The email item this
     * panel represents. Will load the message body according to the record's id.
     * If provided, the title will be automatically set to the value of the record's
     * subject field.
     */

    /**
     * @param {com.conjoon.groupware.email._EmailView} view the view for this panel
     * to render the message
     */
    view : null,

    /**
     * @param {Object} requestId The id of the current ajax request in progress
     */
    requestId : null,

    /**
     * @param {Ext.data.JsonReader} reader The reader for the email message loaded
     */
    reader : null,

    /**
     * @param {Boolean} viewReady Flag to help determine wether the view is rendered
     * or not.
     */
    viewReady : false,

    /**
     * @param {com.conjoon.groupware.email.EmailRecord} emailRecord The last loaded
     * email record as specified in the id of emailItem
     */
    emailRecord : null,

    /**
     * @type {String} _orgIconCls stores the original icon class that was used so that
     * it can be restored with iconCls after the pending icon class was showed
     */
    _orgIconCls : null,

    initComponent : function()
    {
        this.addEvents(
            /**
             * Gets fired before the ajax request for this panel gets started.
             */
            'beforeemailload',
            /**
             * Gets fired before when loading the emails contents failed.
             */
            'emailloadfailure',
            /**
             * Gets fired when the ajax request for this panel successfully
             * loaded the email message which is about to be displayed.
             */
            'emailload'
        );

        if (this.emailItem) {
            this.title = this.autoLoad
                         ? '&#160;'
                         : (this.emailItem.data.subject || '&#160;');
        }

        if (this.refreshFrame === true && !Ext.isIE) {
            this.on('hide', this._onHide, this);
            this.on('show', this._onShow, this);
        }

        this._orgIconCls = this.iconCls || 'com-conjoon-groupware-email-EmailView-Icon';
        this.iconCls     = this.autoLoad
                           ? 'com-conjoon-groupware-pending-icon'
                           : (this.iconCls || 'com-conjoon-groupware-email-EmailView-Icon');


        Ext.apply(this, {
            closable  : true,
            hideMode  : 'offsets'
            /**
             * @bug adding listeners via listeners property does not work in initComponent!!!
             */

        });

        Ext.ux.util.MessageBus.subscribe(
            'com.conjoon.groupware.email.editor.draftSave',
            this._onDraftSave,
            this
        );

        this.on('render',  com.conjoon.groupware.util.LinkInterceptor.getListener().render, {single : true});
        this.on('destroy', this.abortRequest, this, {single : true});

        com.conjoon.groupware.email.EmailViewPanel.superclass.initComponent.call(this);
    },

    getView : function()
    {
        if (!this.view) {
            this.view = new com.conjoon.groupware.email.view.DefaultViewRenderer(
                this.viewConfig
            );
        }

        return this.view;
    },

    /**
     * Renders the view to display the current message.
     *
     */
    renderView : function()
    {
        if (this.emailRecord != null && this.viewReady) {
            this.view.onEmailLoad(this.emailRecord);
        }
    },

    clearView : function()
    {
        if (this.viewReady) {
            this.view.clear();
        }

        if (this.requestId) {
            Ext.Ajax.abort(this.requestId);
            this.requestId = null;
        }
    },

    onRender : function(ct, position)
    {
        com.conjoon.groupware.email.EmailViewPanel.superclass.onRender.apply(this, arguments);

        var view = this.getView()
        view.init(this);

        this.view.render();

        this.viewReady = true;
    },

    // private
    onDestroy : function()
    {
        if(this.rendered){
            var c = this.body;
            c.removeAllListeners();
            this.view.destroy();
            c.update("");
        }

        com.conjoon.groupware.email.EmailViewPanel.superclass.onDestroy.call(this);
    },

    // private
    onResize : function()
    {
        com.conjoon.groupware.email.EmailViewPanel.superclass.onResize.apply(this, arguments);

        if(this.viewReady){
            this.view.layout();
        }
    },

    /**
     * Sets a new email item to for this panel to display
     *
     * @param {com.conjoon.groupware.email.EmailItemRecord} emailItem The
     * new emailItem this panel should represent
     * @param {Boolean} suspendAutoLoad If set to true, the according message will not be loaded
     *
     */
    setEmailItem : function(emailItem, suspendAutoLoad)
    {
        // check first if the requested email item is already being loaded
        if (emailItem && this.emailItem && this.requestId != null && emailItem.id == this.emailItem.id) {
            return;
        }

        this.emailItem = emailItem;

        if (emailItem != null)  {
            this.setTitle((emailItem.data.subject || '&#160;'));
        } else {
            this.emailRecord = null;
            this.view.clear();
        }

        this.abortRequest();

        if (suspendAutoLoad === true) {
            return;
        }

        this.load();
    },

    /**
     * Loads an email message into this panel.
     *
     * @param {Number} id The id of the email to load. If not provided,
     * the id of the emailItem that was passed to the constriuctor will be used
     * instead
     */
    load : function(id)
    {
        if (id == undefined) {
            if (this.emailItem) {
                id = this.emailItem.id;
            } else {
                return;
            }
        }

        this.abortRequest();

        this.view.clear();

        if (this.fireEvent('beforeemailload', id) === false) {
            return;
        }

        this.setTitle(
            com.conjoon.Gettext.gettext("Loading...")
        );

        this.setIconClass('com-conjoon-groupware-pending-icon');

        this.emailRecord = null;


        this.requestId = Ext.Ajax.request({
            url            : './groupware/email.item/get.email/format/json',
            params         : {
                id  : id
            },
            success        : this.onEmailLoadSuccess,
            failure        : this.onEmailLoadFailure,
            scope          : this,
            disableCaching : true
        });
    },

    abortRequest : function()
    {
        if (this.requestId) {
            Ext.Ajax.abort(this.requestId);
        }
        this.requestId = null;
    },

    // private
    doAutoLoad : function()
    {
        this.load();
    },

    /**
     * Callback for successfully retrieving an email
     */
    onEmailLoadSuccess : function(response, parameters)
    {
        var data = com.conjoon.groupware.ResponseInspector.isSuccess(response);
        if (!data || (data && !data.item)) {
            this.onEmailLoadFailure(response, parameters);
            return;
        }

        var record = com.conjoon.util.Record.convertTo(
            com.conjoon.groupware.email.EmailRecord,
            data.item,
            data.item.id
        );

        this.emailRecord = record;

        this.setTitle((record.data.subject || '&#160;'));
        this.setIconClass(this._orgIconCls);
        this.renderView();

        this.fireEvent('emailload', record);

        Ext.ux.util.MessageBus.publish('com.conjoon.groupware.email.view.onEmailLoad', {
            emailRecord : record,
            panelId     : this.id
        });

        this.requestId = null;
    },

    /**
     * Callback for any error that occured during loading an email
     */
    onEmailLoadFailure : function(response, options)
    {
        this.fireEvent('emailloadfailure', response, options);

        Ext.ux.util.MessageBus.publish('com.conjoon.groupware.email.view.onEmailLoadFailure', {
            response : response,
            options  : options,
            panelId  : id
        });

        this.requestId = null;
    },

    _onDraftSave : function(subject, message)
    {
        var emailItem = message.itemRecord;

        if (!this.emailItem || emailItem.get('id') != this.emailItem.get('id')) {
            return;
        }

        this.setEmailItem(emailItem.copy(), true);
        this.emailRecord = message.emailRecord.copy();
        this.renderView();
    },

    _onHide : function()
    {
        if (this.viewReady) {
            this.view._removeIframe();
            this.view.clear();
            this.viewReady = false;
        }
    },

    _onShow : function()
    {
        if (!this.view) {
            return;
        }
        this.view._createIframe();
        this.viewReady = true;
        this.renderView();
    }

});


