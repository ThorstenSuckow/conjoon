/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */

Ext.namespace('de.intrabuild.groupware.email');

de.intrabuild.groupware.email.EmailViewPanel = Ext.extend(Ext.Panel, {

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
     * @cfg {de.intrabuild.groupware.email.EmailItemRecord} emailItem The email item this
     * panel represents. Will load the message body according to the record's id.
     * If provided, the title will be automatically set to the value of the record's
     * subject field.
     */

    /**
     * @param {de.intrabuild.groupware.email._EmailView} view the view for this panel
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
     * @param {de.intrabuild.groupware.email.EmailRecord} emailRecord The last loaded
     * email record as specified in the id of emailItem
     */
    emailRecord : null,

    initComponent : function()
    {
        Ext.apply(this, {
            closable  : true,
            iconCls   : this.iconCls || 'de-intrabuild-groupware-email-EmailView-Icon',
            hideMode  : 'offsets',
            listeners : de.intrabuild.groupware.util.LinkInterceptor.getListener()
        });

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
            this.title = this.emailItem.data.subject;
        }

        if (this.refreshFrame === true && !Ext.isIE) {
            this.on('hide', this._onHide, this);
            this.on('show', this._onShow, this);
        }

        de.intrabuild.groupware.email.EmailViewPanel.superclass.initComponent.call(this);
    },

    getView : function()
    {
        if (!this.view) {
            this.view = new de.intrabuild.groupware.email.view.DefaultViewRenderer(
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
        de.intrabuild.groupware.email.EmailViewPanel.superclass.onRender.apply(this, arguments);

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

        de.intrabuild.groupware.email.EmailViewPanel.superclass.onDestroy.call(this);
    },

    // private
    onResize : function()
    {
        de.intrabuild.groupware.email.EmailViewPanel.superclass.onResize.apply(this, arguments);

        if(this.viewReady){
            this.view.layout();
        }
    },

    /**
     * Sets a new email item to for this panel to display
     *
     * @param {de.intrabuild.groupware.email.EmailItemRecord} emailItem The
     * new emailItem this panel should represent
     * @param {Boolean} suspendAutoLoad If set to true, the according message will not be loaded
     *
     */
    setEmailItem : function(emailItem, suspendAutoLoad)
    {
        this.emailItem = emailItem;
        this.setTitle(emailItem.data.subject);

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

        this.view.clear();

        if (this.fireEvent('beforeemailload', id) === false) {
            return;
        }

        this.setTitle(
            de.intrabuild.Gettext.gettext("Loading...")
        );

        this.emailRecord = null;


        this.requestId = Ext.Ajax.request({
            url            : '/groupware/email/get.email/format/json',
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
        var data = de.intrabuild.groupware.ResponseInspector.isSuccess(response);
        if (data === null) {
            this.onEmailLoadFailure(response, parameters);
            return;
        }

        var record = de.intrabuild.util.Record.convertTo(
            de.intrabuild.groupware.email.EmailRecord,
            data.item,
            data.item.id
        );

        this.emailRecord = record;

        this.setTitle(record.data.subject);
        this.renderView();

        this.fireEvent('emailload', record);

        this.requestId = null;
    },

    /**
     * Callback for any error that occured during loading an email
     */
    onEmailLoadFailure : function(response, parameters)
    {
        this.fireEvent('emailloadfailure', response, parameters);

        this.requestId = null;
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


