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

    Ext.apply(this, config);
};

com.conjoon.groupware.email.view.DefaultViewRenderer.prototype = {

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
     * @cfg {String} attachmentValue
     */
    attachmentValue : 'Attachments',

    /**
     * @cfg {String} bccValue
     */
    bccValue : 'BCC',

    /**
     * @cfg {String} fromValue
     */
    fromValue : 'From',

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

        var heightPrev = ifrAnchor.previousSibling ? Ext.fly(ifrAnchor.previousSibling).getHeight() : 0;
        var heightNext = ifrAnchor.nextSibling ? Ext.fly(ifrAnchor.nextSibling).getHeight() : 0;

        var nHeight = height - (heightPrev + heightNext);

        ifrAnchor.style.height = (nHeight < 0 ? 0 : nHeight) + "px";
        iframe.style.height = (nHeight < 0 ? 0 : nHeight) + "px";
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

        var subject     = data.subject;
        var from        = data.from;
        var to          = data.to;
        var cc          = data.cc;
        var bcc         = data.bcc;
        var replyTo     = data.replyTo;
        var date        = data.date;
        var body        = data.body;
        var attachments = data.attachments;
        var isPlainText = data.isPlainText;

        var ts = this.templates;

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

        for (var i = 0; i < len; i++) {
            attachItemsHtml   += ts.attachmentItem.apply({
                mimeIconCls   : com.conjoon.util.MimeIconFactory.getIconCls(attachments[i].mimeType),
                name          : attachments[i].fileName,
                attachmentId  : attachments[i].id,
                attachmentKey : attachments[i].key
            });
        }

        var DomHelper = Ext.DomHelper;
        var ifrAnchor = document.getElementById(this.viewId);

        DomHelper.insertHtml('beforeBegin', ifrAnchor, header);

        if (len > 0) {
            attachHtml = ts.attachments.apply({
                attachmentItems : attachItemsHtml
            });

            var footer = ts.footer.apply({
                attachments : attachHtml
            });

            DomHelper.insertAfter(ifrAnchor, footer);
        }

        var doc = this.doc;
        Ext.fly(doc.body).swallowEvent("click", true);
        com.conjoon.groupware.util.LinkInterceptor.removeListener(Ext.fly(doc.body));
        com.conjoon.groupware.util.LinkInterceptor.addListener(Ext.fly(doc.body));

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

        var dom  = document.getElementById(this.viewId);

        var doc = this.doc;
        if (doc) {
            if (this.isPlainTextView) {
                doc.body.innerHTML = "";
            } else {
                com.conjoon.groupware.util.LinkInterceptor.removeListener(Ext.fly(doc.body));
                doc.open();
                doc.write(this.emptyMarkup)
                doc.close();
            }
        }


        var prev = dom.previousSibling;
        var next = dom.nextSibling;
        if (prev) {
            prev.parentNode.removeChild(prev);
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
    },

    // private
    initTemplates : function()
    {
        var ts = this.templates || {};

        if (!ts.master){
            ts.master = new Ext.Template(
                     '<div style="height:100%">{header}<div id="'+this.viewId+'">',
                     '<iframe name="'+Ext.id()+'" style="width:100%;border:0px;" frameborder="0" src="'+(Ext.SSL_SECURE_URL || "javascript:false")+'"></iframe></div>',
                     '{footer}</div>'
            );
        }

        if (!ts.header) {
            ts.header = new Ext.Template(
                    '<div class="com-conjoon-groupware-email-EmailView-wrap">',
                       '<div class="com-conjoon-groupware-email-EmailView-dataInset">',
                        '<span class="com-conjoon-groupware-email-EmailView-date">{date:date("d.m.Y H:i")}</span>',
                        '{subject}',
                        '<table border="0" cellspacing="0" cellpadding="0" class="com-conjoon-groupware-email-EmailView-headerTable">',
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

        if (!ts.addresses) {
            ts.addresses = new Ext.Template(
                '<a href="mailto:{address:htmlEncode}">{name:htmlEncode}</a>'
            );
        }

        if (!ts.subject) {
            ts.subject = new Ext.Template(
                '<div class="com-conjoon-groupware-email-EmailView-subject">{subject}</div>'
            );
        }

        if (!ts.from) {
            ts.from = new Ext.Template(
                '<tr><td class="headerField">',this.fromValue,':</td><td class="headerValue">{from}</td></tr>'
            );
        }

        if (!ts.to) {
            ts.to = new Ext.Template(
                '<tr><td class="headerField">',this.toValue,':</td><td class="headerValue">{to}</td></tr>'
            );
        }

        if (!ts.replyTo) {
            ts.replyTo = new Ext.Template(
                '<tr><td class="headerField">',this.replyToValue,':</td><td class="headerValue">{replyTo}</td></tr>'
            );
        }

        if (!ts.cc) {
            ts.cc = new Ext.Template(
                '<tr><td class="headerField">',this.ccValue,':</td><td class="headerValue">{cc}</td></tr>'
             );
        }

        if (!ts.bcc) {
            ts.bcc = new Ext.Template(
                '<tr><td class="headerField">',this.bccValue,':</td><td class="headerValue">{bcc}</td></tr>'
            );
        }

        if (!ts.footer) {
            ts.footer = new Ext.Template(
                '<table  cellspacing="0" cellpadding="0" border="0" style="width:100%;"><tr><td style="padding:2px;background-color:#F5F5F5;border-top:1px solid #99BBE8">',
                '{attachments}',
                '</td></tr></table>'
            );
        }

        if (!ts.attachments) {
            ts.attachments = new Ext.Template(
                    '<table cellspacing="0" cellpadding="0" border="0" style="width:100%"><tr>',
                        '<td style="width:60px;vertical-align:top;"><span style="font-family:Tahoma,Helvetica,Arial;font-size:11px;float:left;padding:2px;font-weight:bold;color:#15428B;">',this.attachmentValue,':</span></td>',
                        '<td style="background:white;border:1px solid #767676;padding:2px;">',
                        '{attachmentItems}',
                    '</td></tr></table>'
            );
        }

        if (!ts.attachmentItem) {
            ts.attachmentItem = new Ext.Template(
                '<div onclick="com.conjoon.groupware.DownloadManager.downloadEmailAttachment({attachmentId}, \'{attachmentKey}\', \'{name}\', this);" tabindex="0" class="com-conjoon-groupware-email-EmailView-attachmentItem {mimeIconCls}">{name}</div>'
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
        this.initData(null);
    },

    onEmailLoad : function(record)
    {
        var data = record.data;

        var subject     = data.subject     || '';
        var from        = data.from        || '';
        var to          = data.to          || '';
        var cc          = data.cc          || '';
        var replyTo     = data.replyTo     || '';
        var bcc         = data.bcc         || '';
        var date        = data.date        || '';
        var body        = data.body        || '';
        var attachments = data.attachments || '';
        var isPlainText = data.isPlainText || false;

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
            isPlainText : isPlainText
        });
        this.layout();
        this.isPlainTextView = isPlainText;
    }

};