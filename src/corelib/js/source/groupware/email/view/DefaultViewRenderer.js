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

Ext.namespace('com.conjoon.groupware.email.view');

/**
 * @class com.conjoon.groupware.email.view.DefaultViewRenderer
 *
 * A view implementation for the {@see com.conjoon.groupware.email.EmailViewPanel}.
 * Takes care of additional preparing the data received by the ViewPanel for
 * displaying, though it assumes that encoding etc. is already done server
 * side.
 */
com.conjoon.groupware.email.view.DefaultViewRenderer = function(config){

    this.addEvents(
        /**
         * @event refreshdatarequest
         * Event gets fired whenever this renderer requests refreshing the data
         * @param {DefaultViewRenderer} this
         * @param {Object} data An object literal with at least path and uId of the message
         * which gets requested.
         */
        'refreshdatarequest'
    );

    var DownloadManager = com.conjoon.groupware.DownloadManager;

    DownloadManager.on('request', this.onDownloadRequest, this);
    DownloadManager.on('error',   this.onDownloadFinish,  this);
    DownloadManager.on('failure', this.onDownloadFinish,  this);
    DownloadManager.on('cancel',  this.onDownloadFinish,  this);
    DownloadManager.on('success', this.onDownloadFinish,  this);

    this.attachmentKeys = {};

    Ext.apply(this, config);
};

Ext.extend(com.conjoon.groupware.email.view.DefaultViewRenderer, Ext.util.Observable, {

    /**
     * @cfg {String} toValue
     */
    toValue : 'To',

    /**
     * @cfg {String} replyToValue
     */
    replyToValue : 'Reply to',

    /**
     * @cfg {String} ccValue
     */
    ccValue : 'CC',

    /**
     * @cfg {String} bccValue
     */
    bccValue : 'BCC',

    /**
     * @cfg {String} fromValue
     */
    fromValue : 'From',

    /**
     * @cfg {String} loadExternalsValue
     */
    loadExternalsValue : 'To protect your privacy, external resources in this message are blocked.',

    /**
     * @cfg {com.conjoon.groupware.email.EmailViewPanel} panel The panel
     * to which this view is bound
     */

    /**
     * @cfg {Object} templates An object containing the templates for the message.
     * This view needs the following templates:
     * <ul>
     *  <li><strong>master</strong>: The body template in which the other templates get nested</li>
     *  <li><strong>header</strong>: The header template in which subject, to, cc et.c get nested</li>
     *  <li><strong>loadExternalsContainer</strong>: The template in which an option to reload the message
     *  with external resources,if any, is provided</li>
     *  <li><strong>loadExternalsControl</strong>: The control used to show the message with external resources</li>
     *  <li><strong>subject</strong>: The template for the subject-data of the email</li>
     *  <li><strong>cc</strong>: The template for the cc addresses of the email</li>
     *  <li><strong>bcc</strong>: The template for the bcc addresses of the email</li>
     *  <li><strong>footer</strong>: The footer template in which the attachments get nested</li>
     *  <li><strong>attachments</strong>: The template body for the attachment items</li>
     *  <li><strong>attachmentItem</strong>: The template for a single attachment item</li>
     *  </ul>
     */

    /**
     *  @cfg {String} emptyMarkup The default content of the iframe when there
     *  is no message to display, and to which body-element a plain text message
     *  gets appended. Overwrite getEmptyMarkup to return a custom markup for the
     *  iframe or submit it via the config-object.
     */

    /**
     * The iframe that displays the message body.
     * @type {Ext.Element}
     */
    iframe : null,

    /**
     * The document of the iframe
     * @type {HTMLElement}
     */
    doc : null,

    /**
     * false if the message was rendered
     * @type {Boolean}
     */
    cleared : true,

    /**
     * @type {String}
     */
    viewId : null,

    /**
     * @type {Boolean}
     */
    isPlainTextView : false,

    /**
     * @type {Object} attachments An array that holds all attachment id/keys
     * if this view has to display them
     */
    attachmentKeys : null,

    /**
     * @type {Object} attachmentData
     */
    attachmentData : null,

    /**
     * @type {Ext.Element} attachmentContainer
     */
    attachmentContainer : null,

    /**
     * @type {Ext.Element} loadExternalsContainer
     */
    loadExternalsContainer : null,

    /**
     * An object literal containing both uId and path of the currently
     * rendered message.
     * @type {Objec} messageLocationData
     */
    messageLocationData : null,

    /**
     * @type {String} downloadTypeEmailAttachment
     */
    downloadTypeEmailAttachment : 'emailAttachment',

    /**
     * @type {Ext.SplitBar} splitBar
     */
    splitBar : null,

    /**
     * Returns the html-code for the iframe which displays the message content.
     *
     * @return {String}
     */
    getDocMarkup : function()
    {
        var utilDom = com.conjoon.util.Dom;

        var getCssTextFromStyleSheet = utilDom.getCssTextFromStyleSheet;

        var cssBase = utilDom.getHrefFromStyleSheet('conjoon-all.css');

        var cblockquote = getCssTextFromStyleSheet(
             '.com-conjoon-groupware-email-EmailView-body blockquote'
        );

        var smileys = [
            'smile',
            'laughing',
            'frown',
            'embarassed',
            'wink',
            'undecided',
            'tongue',
            'surprise',
            'kiss',
            'yell',
            'cool',
            'money',
            'foot',
            'innocent',
            'cry',
            'sealed'
        ];

        var emoticons = getCssTextFromStyleSheet(
            '.com-conjoon-groupware-email-EmailView-body .emoticon'
        );

        var emo = "";
        for (var i = 0, len = smileys.length; i < len; i++) {
            emo = getCssTextFromStyleSheet(
                        '.com-conjoon-groupware-email-EmailView-body '
                        /**
                         * Strange behavior: IE delivers the styles in reverse order,
                         * but we need to query then aggain reverse, i.e. in css is
                         * .smile.emoticon, but we have to query for .emoticon.smile
                         */
                        + (Ext.isIE
                            ? '.emoticon.'+smileys[i]
                            : '.'+smileys[i]+'.emoticon')
            );

            if (Ext.isIE || Ext.isGecko) {
                // Safari 3.1 (Windows) seems to automatically resolve ../ in
                // the css
                emo = emo.replace(/..\//, cssBase+'../');
            }

            emoticons += emo;
        }

        var abs = [];
        for (var i = 0; i < 10; i++) {
            abs.push('blockquote');
            cblockquote += getCssTextFromStyleSheet(
                '.com-conjoon-groupware-email-EmailView-body '+abs.join(' ')
            );
        }

        return '<html>'
               + '<head>'
               + '<META http-equiv="Content-Type" content="text/html; charset=UTF-8">'
               + '<title></title>'
               + '<style type="text/css">'
               + getCssTextFromStyleSheet(
                    '.com-conjoon-groupware-email-EmailView-body'
                )
               + ' '
               + getCssTextFromStyleSheet(
                    '.com-conjoon-groupware-email-EmailView-body '
                    +(Ext.isIE ? 'div.viewBodyWrap' : 'pre')
                )
               + ' '
               + getCssTextFromStyleSheet(
                    '.com-conjoon-groupware-email-EmailView-body a'
                )
               + ' '
               + cblockquote
               + ' '
               + getCssTextFromStyleSheet(
                    '.com-conjoon-groupware-email-EmailView-body div.signature'
                )
               + ' '
               + getCssTextFromStyleSheet(
                    '.com-conjoon-groupware-email-EmailView-body div.signature a'
                )
               + ' '
               + emoticons
               + '</style></head>'
               + '<body class="com-conjoon-groupware-email-EmailView-body">'
               + '</body></html>';
    },

    // private
    init: function(panel)
    {
        if (!this.viewId) {
            this.viewId = Ext.id();
        }

        if (!this.emptyMarkup) {
            this.emptyMarkup = this.getDocMarkup();
        }

        this.initTemplates();
        this.initData(panel);
    },

    // private
    render : function()
    {
        this.renderUI();
    },

    // private
    renderUI : function()
    {
        var ts = this.templates;

        var header = "";
        var footer = "";
        var html = ts.master.apply({
            header : header,
            footer : footer
        });

        var p = this.panel;
        p.body.dom.innerHTML = html;



        this.initElements();
    },

    /**
     * Determines height of the iframe and sets style-attribute accordingly.
     */
    layout : function()
    {
        var height = this.panel.body.getHeight(true);

        var iframe = this.iframe.dom;
        var ifrAnchor = document.getElementById(this.viewId);

        var prevSibling = ifrAnchor.previousSibling
                          ? ifrAnchor.previousSibling
                          : null,
            heightPrev = 0;

        while(prevSibling) {
            heightPrev += Ext.fly(prevSibling).getHeight();
            prevSibling = prevSibling.previousSibling;
        }

        var heightNext = 0;
        if (this.splitBar) {
            var pn = ifrAnchor.parentNode;
            heightNext = Ext.fly(pn.lastChild).getHeight();
            this.splitBar.maxSize = (Ext.fly(pn).getHeight()
                                    - Ext.fly(ifrAnchor.previousSibling).getHeight())
                                    - this.splitBar.el.getHeight();

            var m = Ext.fly(pn.lastChild).getHeight(true)
                    - this.splitBar.el.getHeight(true);

            pn.lastChild.firstChild.style.height = (m > 0 ? m : 0)+ "px";
        }

        var nHeight = height - (heightPrev + heightNext);

        ifrAnchor.style.height = (nHeight < 0 ? 0 : nHeight) + "px";
        iframe.style.height    = (nHeight < 0 ? 0 : nHeight) + "px";
    },

    /**
     * Renders a list of recipients into the recipient template.
     *
     * @param {Array} recipients
     *
     * @return {String}
     */
    _renderAddresses : function(recipients)
    {
        if (!recipients) {
            return "";
        }
        var addresses = recipients.addresses;

        if (!addresses || addresses.length == 0) {
            return "";
        }

        var ret = [];
        var template = this.templates.addresses
        for (var i = 0, len = addresses.length; i < len; i++) {
            ret.push(
                template.apply({
                    address : addresses[i]['address'],
                    name    : addresses[i]['name'] || addresses[i]['address']
                })
            );
        }

        return ret.join(', ');
    },

    /**
     *
     * @param {mixed} uId The unique identifier of the message for which external resources
     * should get loaded
     */
    renderLoadExternalsControl : function(uId) {

        return this.templates.loadExternalsControl.apply({
            uId : uId
        })

    },

    /**
     * Renders the view with the given data
     *
     * @param {Object}
     *
     * @private
     */
    doRender : function(data)
    {
        if (!this.iframe) {
            return;
        }

        this.clear();

        var subject     = data.subject,
            from        = data.from,
            to          = data.to,
            cc          = data.cc,
            bcc         = data.bcc,
            replyTo     = data.replyTo,
            date        = data.date,
            body        = data.body,
            attachments = data.attachments,
            isPlainText = data.isPlainText,
            path        = data.path,
            uId         = data.uId,
            loadExternals = data.loadExternals;

        var ts = this.templates,
            loadExternalsHtml;

        loadExternalsHtml = loadExternals ? ts.loadExternalsContainer.apply({
            loadExternalsControl : this.renderLoadExternalsControl(uId)
        }) : null;

        var cc = this._renderAddresses(cc);
        var ccHtml = cc ? ts.cc.apply({
            cc : cc
        }) : "";

        var bcc = this._renderAddresses(bcc);
        var bccHtml = bcc ? ts.bcc.apply({
            bcc : bcc
        }) : "";

        var to = this._renderAddresses(to);
        var toHtml = to ? ts.to.apply({
            to : to
        }) : "";

        var from = this._renderAddresses(from);
        var fromHtml = from ? ts.from.apply({
            from : from
        }) : "";

        var replyTo = this._renderAddresses(replyTo);
        var replyToHtml = replyTo ? ts.replyTo.apply({
            replyTo : replyTo
        }) : "";

        var subjectHtml = ts.subject.apply({
            subject : subject
        });

        var header = ts.header.apply({
            from    : fromHtml,
            to      : toHtml,
            cc      : ccHtml,
            bcc     : bccHtml,
            replyTo : replyToHtml,
            date    : date,
            subject : subjectHtml
        });

        var attachItemsHtml = "";
        var attachHtml = "";
        var len = attachments.length;

        this.attachmentData = {};
        this.messageLocationData = {
            uId : uId,
            path : path
        };

        for (var i = 0; i < len; i++) {
            var attachmentTemplateId = Ext.id(),
                attachmentId         = attachments[i].id || -1,
                attachmentPath       = path;

                this.attachmentData[attachmentTemplateId] = {
                    attachmentId    : attachmentId,
                    attachmentKey   : attachments[i].key,
                    attachmentPath  : attachmentPath,
                    uId             : uId,
                    name            : attachments[i].fileName
                };

                attachItemsHtml   += ts.attachmentItem.apply({
                mimeIconCls          : com.conjoon.util.MimeIconFactory
                                       .getIconCls(attachments[i].mimeType),
                name                 : attachments[i].fileName,
                attachmentTemplateId : attachmentTemplateId,
            });

            this.attachmentKeys[attachmentId+'_'+attachments[i].key]
                = attachmentTemplateId;
        }

        var DomHelper = Ext.DomHelper;
        var ifrAnchor = document.getElementById(this.viewId);

        DomHelper.insertHtml('beforeBegin', ifrAnchor, header);
        if (loadExternals) {
            DomHelper.insertHtml('beforeBegin', ifrAnchor, loadExternalsHtml);
            this.loadExternalsContainer = new Ext.Element(ifrAnchor.previousSibling);
            this.installListenerForLoadExternalsControl();
        }


        if (len > 0) {
            attachHtml = ts.attachments.apply({
                attachmentItems : attachItemsHtml
            });

            var footer = ts.footer.apply({
                attachments : attachHtml
            });

            var foot = DomHelper.insertAfter(ifrAnchor, footer);

            this.createSplitBar(ifrAnchor);

            Ext.fly(ifrAnchor.parentNode).addClass('attachment');

            this.attachmentContainer = new Ext.Element(ifrAnchor.parentNode);

            this.attachmentContainer.on('click', function(e, t) {
                e.stopEvent();

                if (!this.attachmentData[t.id]) {
                    return;
                }

                var data = this.attachmentData[t.id];

                com.conjoon.groupware.DownloadManager.downloadEmailAttachment(
                        data.attachmentId, data.attachmentKey,
                        data.name, data.uId, data.attachmentPath
                );

            }, this, {
                delegate : 'div.com-conjoon-groupware-email-EmailView-attachmentItem'
            });

        }

        var doc = this.doc;

        if (isPlainText === false) {
            doc.open();
            doc.write(body)
            doc.close();
        } else {
            doc.body.innerHTML = this.decoratePlainText(body);
        }

        this.cleared = false;
    },

    /**
     * Decorates a message text if needed.
     * Depending on the browser, the text will be either wrapped in pre tags
     * (mozilla/ webkit based browsers) or in a div tag for IE with special css
     * rules, as IE has problems displaying blockquotes within a pre-tag.
     * Additionally, all whitespace-pairs get replaced with " &nbsp; "pairs in IE,
     * so automatic word wrapping is possible.
     *
     * @param {String} text
     *
     * @return {String}
     */
    decoratePlainText : function(text)
    {
        if (!text) {
            return text;
        }

        return Ext.isIE
               ? '<div class="viewBodyWrap">'+com.conjoon.util.Format.replaceWhitespacePairs(text)+'</div>'
               : '<pre>'+text+'</pre>';
    },

    clear : function()
    {
        if (this.cleared) {
            return;
        }

        this.attachmentData = {};
        this.messageLocationData = {};
        this.attachmentKeys = {};

        var dom = document.getElementById(this.viewId);

        var doc = this.doc;
        if (doc) {
            if (this.isPlainTextView) {
                doc.body.innerHTML = "";
            } else {
                doc.open();
                doc.write(this.emptyMarkup)
                doc.close();
            }
        }

        Ext.fly(dom.parentNode).removeClass('attachment');

        if (this.loadExternalsContainer) {
            this.loadExternalsContainer.removeAllListeners();
            this.loadExternalsContainer = null;
        }

        var prev = dom.previousSibling,
            prevTmp,
            next = dom.nextSibling;

        while (prev) {
            prevTmp = prev.previousSibling;
            prev.parentNode.removeChild(prev);
            prev = prevTmp;
        }

        if (this.attachmentContainer) {
            this.attachmentContainer.removeAllListeners();
            this.attachmentContainer = null;
        }

        if (this.splitBar) {
            this.splitBar.destroy();
            this.splitBar = null;

            var n = next.nextSibling;
            n.parentNode.removeChild(n);
        }
        if (next) {
            next.parentNode.removeChild(next);
        }

        this.cleared = true;
    },

    // private
    initElements : function()
    {
        this.el = new Ext.Element(this.panel.body.dom.firstChild);
        this._createIframe();
    },

    _removeIframe : function()
    {
        if (this.iframe) {
            com.conjoon.groupware.util.LinkInterceptor.removeListener(Ext.fly(this.doc.body));
            document.getElementById(this.viewId).removeChild(this.iframe.dom);
            this.doc = null;
            this.iframe = null;
        }
    },

    _createIframe : function()
    {
        var iframe = document.getElementById(this.viewId).firstChild;

        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.style.width = '100%';
            iframe.style.border = '0px';
            iframe.setAttribute('frameBorder', "0");
            iframe.src = (Ext.SSL_SECURE_URL || "javascript:false");
            document.getElementById(this.viewId).appendChild(iframe);
        }

        var doc;
        if(Ext.isIE){
            doc = iframe.contentWindow.document;
        } else {
            doc = (iframe.contentDocument || window.frames[iframe.name].document);
        }
        doc.open();
        doc.write(this.emptyMarkup)
        doc.close();

        this.doc = doc;
        this.iframe = new Ext.Element(iframe);
        com.conjoon.groupware.util.LinkInterceptor.addListener(Ext.fly(this.doc.body));
    },

    // private
    initTemplates : function()
    {
        var ts = this.templates || {};

        if (!ts.master){
            ts.master = new Ext.Template(
                     '<div class="com-conjoon-groupware-email-EmailView">',
                     '{header}',
                     '<div id="'+this.viewId+'">',
                     '<iframe name="'+Ext.id()+'"',
                     ' style="width:100%;border:0px;" frameborder="0" ',
                     'src="'+(Ext.SSL_SECURE_URL || "javascript:false")+'">',
                     '</iframe></div>',
                     '{footer}</div>'
            );
        }

        if (!ts.header) {
            ts.header = new Ext.Template(
                    '<div class="wrap">',
                       '<div class="dataInset">',
                        '<span class="date">{date:date("d.m.Y H:i")}</span>',
                        '{subject}',
                        '<table border="0" cellspacing="0" cellpadding="0" ',
                        'class="headerTable">',
                        '{from}',
                        '{replyTo}',
                        '{to}',
                        '{cc}',
                        '{bcc}',
                        '</table>',
                       '</div>',
                    '</div>'
            );
        }

        if (!ts.loadExternalsContainer) {
            ts.loadExternalsContainer = new Ext.Template(
                '<div class="loadExternalsContainer">',
                    '<span>', this.loadExternalsValue, '</span>',
                    '<span>{loadExternalsControl}</span>',
                '</div>'
            );
        }

        if (!ts.loadExternalsControl) {
            ts.loadExternalsControl = new Ext.Template(
                '<span class="control" cn_id="{uId}">',
                    'Show message with external resources',
                '</span>'
            );
        }

        if (!ts.addresses) {
            ts.addresses = new Ext.Template(
                '<a href="mailto:{address:htmlEncode}">{name:htmlEncode}</a>'
            );
        }

        if (!ts.subject) {
            ts.subject = new Ext.Template(
                '<div class="subject">{subject}</div>'
            );
        }

        if (!ts.from) {
            ts.from = new Ext.Template(
                '<tr><td class="headerField">',this.fromValue,':</td>',
                '<td class="headerValue">{from}</td></tr>'
            );
        }

        if (!ts.to) {
            ts.to = new Ext.Template(
                '<tr><td class="headerField">',this.toValue,':</td>',
                '<td class="headerValue">{to}</td></tr>'
            );
        }

        if (!ts.replyTo) {
            ts.replyTo = new Ext.Template(
                '<tr><td class="headerField">',this.replyToValue,':</td>',
                '<td class="headerValue">{replyTo}</td></tr>'
            );
        }

        if (!ts.cc) {
            ts.cc = new Ext.Template(
                '<tr><td class="headerField">',this.ccValue,':</td>',
                '<td class="headerValue">{cc}</td></tr>'
             );
        }

        if (!ts.bcc) {
            ts.bcc = new Ext.Template(
                '<tr><td class="headerField">',this.bccValue,':</td>',
                '<td class="headerValue">{bcc}</td></tr>'
            );
        }

        if (!ts.footer) {
            ts.footer = new Ext.Template(
                '<div class="attachmentContainer">',
                '{attachments}',
                '</div>'
            );
        }

        if (!ts.attachments) {
            ts.attachments = new Ext.Template(
                    '<div class="attachmentWrap">',
                    '{attachmentItems}',
                    '</div>'
            );
        }

        if (!ts.attachmentItem) {
            ts.attachmentItem = new Ext.XTemplate(
                '<div id="{attachmentTemplateId}" ',
                ' tabindex="0" ',
                'class="com-conjoon-groupware-email-EmailView-attachmentItem ',
                '{mimeIconCls}" ',
                ' qtip="{[this.qtipName(values.name)]}"',
                '>{[this.renderName(values.name)]}</div>', {
                    qtipName : function(value) {
                        return value.replace(/"/g, '&quot;');
                    },
                    renderName : function(value) {
                        return value;
                    }
                }
            );
        }

        for(var k in ts){
            var t = ts[k];
            if(t && typeof t.compile == 'function' && !t.compiled){
                t.disableFormats = false; // needed for correct date parsing
                t.compile();
            }
        }

        this.templates = ts;

    },


    // private
    initData : function(panel)
    {
        this.panel = panel;
    },

    // private
    destroy : function()
    {
        if (this.splitBar) {
            this.splitBar.destroy();
        }

        var DownloadManager = com.conjoon.groupware.DownloadManager;

        DownloadManager.un('request', this.onDownloadRequest, this);
        DownloadManager.un('error',   this.onDownloadFinish,  this);
        DownloadManager.un('failure', this.onDownloadFinish,  this);
        DownloadManager.un('cancel',  this.onDownloadFinish,  this);
        DownloadManager.un('success', this.onDownloadFinish,  this);

        this.initData(null);
    },

    /**
     * Gets called when the loadExternalsContainer and the control was added
     * to the DOM and it's secure to add the event listener to the control.
     * This method should be called whenever a new message gets rendered
     * with this view,
     */
    installListenerForLoadExternalsControl : function() {

        var me = this;

        if (!me.loadExternalsContainer) {
            return;
        }

        me.loadExternalsContainer.on('click', function(e, t) {
            e.stopEvent();

            var uId = t.getAttribute('cn_id');

            if (!this.messageLocationData || this.messageLocationData.uId != uId) {
                return;
            }

            this.fireEvent(
                'refreshdatarequest', this, {
                    id : uId,
                    path : this.messageLocationData.path,
                    allowExternals : true
            });


        }, me, {
            delegate : 'span.control'
        });
    },

    onEmailLoad : function(record)
    {
        var data = record.data;

        var subject     = data.subject     || '',
            from        = data.from        || '',
            to          = data.to          || '',
            cc          = data.cc          || '',
            replyTo     = data.replyTo     || '',
            bcc         = data.bcc         || '',
            date        = data.date        || '',
            body        = data.body        || '',
            attachments = data.attachments || '',
            isPlainText = data.isPlainText || false,
            path        = data.path,
            uId         = data.uId,
            loadExternals = true//data.hasExternalResources && data.externalResourcesDisabled;

        this.doRender({
            subject     : subject,
            from        : from,
            to          : to,
            cc          : cc,
            bcc         : bcc,
            replyTo     : replyTo,
            date        : date,
            body        : body,
            attachments : attachments,
            isPlainText : isPlainText,
            path        : path,
            uId         : uId,
            loadExternals : loadExternals
        });
        this.layout();
        this.isPlainTextView = isPlainText;
    },

    onDownloadRequest : function(download, type, options)
    {
        if (type != this.downloadTypeEmailAttachment) {
            return;
        }

        var key = options.attachmentId+'_'+options.attachmentKey;
        if (this.attachmentKeys && this.attachmentKeys[key]) {
            Ext.fly(document.getElementById(this.attachmentKeys[key]))
                .addClass('request');
        }
    },

    onDownloadFinish : function(download, type, options)
    {
        if (type != this.downloadTypeEmailAttachment) {
            return;
        }

        var key = options.attachmentId+'_'+options.attachmentKey;
        if (this.attachmentKeys && this.attachmentKeys[key]) {
            Ext.fly(document.getElementById(this.attachmentKeys[key]))
                .removeClass('request');
        }
    },

    createSplitBar : function(ifrAnchor)
    {
        var splitter = Ext.fly(ifrAnchor.parentNode).createChild({
            cls  : 'x-layout-split x-layout-split-south',
            html : "&#160;"
        }, ifrAnchor.nextSibling);

        this.splitBar = new Ext.SplitBar(
            splitter, ifrAnchor, Ext.SplitBar.TOP
        );

        this.splitBar.on('beforeresize', this.onBeforeSplitBarResize, this);

        this.splitBar.on('moved', this.onSplitBarMoved, this);

        this.splitBar.dd.getTargetCoord = this.getTargetCoordForDD;
    },

    /**
     * Called in the scope of this.splitBar.dd
     *
     */
    getTargetCoordForDD : function(iPageX, iPageY)
    {
        var coord = Ext.dd.DDProxy.prototype.getTargetCoord.call(
            this, iPageX, iPageY
        );

        if (this._mode == 'top' && this.maxY-coord.y <= 35) {
            coord.y        = this.maxY;
            this._showCont = false;
        } else if (this._mode == 'bottom') {
            if (this.maxY-coord.y <= 35 && this.maxY-coord.y > 5) {
                coord.y = this.maxY-35;
                this._showCont = true;
            } else if (this.maxY-coord.y <= 5) {
                coord.y = this.maxY;
                this._showCont = false;
            }
        }
        return coord;
    },

    onBeforeSplitBarResize : function(splitBar)
    {
        var dd   = splitBar.dd;
        dd._mode = 'top';

        if (dd.lastPageY == dd.maxY) {
            dd._mode = 'bottom';
        }
    },

    onSplitBarMoved : function(splitBar, newSize)
    {
        var ifrAnchor = document.getElementById(this.viewId);
        var dd        = splitBar.dd;

        if (splitBar.maxSize-newSize <= 35) {
            if (!dd._showCont) {
                newSize = splitBar.maxSize;
            } else {
                newSize = splitBar.maxSize-35;
            }
        }

        var pn = ifrAnchor.parentNode;

        pn.lastChild.style.height = (Ext.fly(pn).getHeight()
                                     - Ext.fly(pn.firstChild).getHeight()
                                     - newSize
                                     - splitBar.el.getHeight(true))
                                     + "px";

        this.layout();
    }
});
