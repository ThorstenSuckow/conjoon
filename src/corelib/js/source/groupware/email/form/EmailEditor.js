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

com.conjoon.groupware.email.form.EmailEditor = Ext.extend(Ext.form.HtmlEditor, {

    initComponent : function()
    {
        Ext.apply(this, {
            hideMode          : 'offsets',
            hideLabel         : true,
            name              : 'msg',
            anchor            : '100% -0',
            enableLinks       : false,
            defaultFont       : 'courier new',
            enableSourceEdit  : false,
            defaultAutoCreate : {
                tag          : 'textarea',
                style        : 'width:500px;height:300px;font-family:Courier New;font-size:14px;',
                autocomplete : 'off'
            }
        });

        com.conjoon.groupware.email.form.EmailEditor.superclass.initComponent.call(this);
    },

    initEditor : function()
    {
        com.conjoon.groupware.email.form.EmailEditor.superclass.initEditor.call(this);

        if (Ext.isSafari) {
            Ext.EventManager.on(this.doc, 'keydown', function(e){
                if (e.getKey() == e.ENTER) {
                    // adjust behavior of webkit based browsers.
                    // we need a simple br tag inserted for linebreaks
                    // overrides the standard behavior of inserting
                    // div elements
                    e.stopEvent();
                    var r = this.win.getSelection().getRangeAt(0);
                    var br = this.doc.createElement('br');
                    r.insertNode(br);
                    this.win.getSelection().collapse(br, 2);
                    this.deferFocus();
                }
            }, this);
        }

        // unbind the Ext default fixKeeys implementation and use custom one so that
        // blockqouotes will be quoted properly
        if (Ext.isIE) {
            Ext.EventManager.un(this.doc, 'keydown', this.fixKeys, this);

            Ext.EventManager.on(this.doc, 'keydown', function(e){
                var k = e.getKey(), r;
                if(k == e.TAB){
                    e.stopEvent();
                    r = this.doc.selection.createRange();
                    if(r){
                        r.collapse(true);
                        r.pasteHTML('&nbsp;&nbsp;&nbsp;&nbsp;');
                        this.deferFocus();
                    }
                }else if(k == e.ENTER){
                    r = this.doc.selection.createRange();
                    if(r){
                        var target = r.parentElement();
                        var targetName = target
                                         ? target.tagName.toLowerCase()
                                         : '';
                        if(!target || (targetName != 'li' && targetName != 'blockquote')){
                            e.stopEvent();
                            r.pasteHTML('<br />');
                            r.collapse(false);
                            r.select();
                        }
                    }
                }
            }, this);

        }
    },

    getDocMarkup : function()
    {
        if (!this.__doc_markup__) {

           var getCssTextFromStyleSheet = com.conjoon.util.Dom.getCssTextFromStyleSheet;

            var body = getCssTextFromStyleSheet(
                '.com-conjoon-groupware-email-EmailForm-htmlEditor-body'
            );

            var insertDiv = getCssTextFromStyleSheet(
                '.com-conjoon-groupware-email-EmailForm-htmlEditor-body div.text'
            );

            var signature = getCssTextFromStyleSheet(
                '.com-conjoon-groupware-email-EmailForm-htmlEditor-body div.signature'
            );

            var blockquote = "";

            var abs = [];
            for (var i = 0; i <10; i++) {
                abs.push('blockquote');
                blockquote += getCssTextFromStyleSheet(
                     '.com-conjoon-groupware-email-EmailForm-htmlEditor-body '+abs.join(' ')
                );
            }

            this.__doc_markup__ = '<html>'
                                  + '<head>'
                                  + '<META http-equiv="Content-Type" content="text/html; charset=UTF-8">'
                                  + '<title></title>'
                                  + '<style type="text/css">'
                                  + body
                                  + ' '
                                  + blockquote
                                  + ' '
                                  + getCssTextFromStyleSheet(
                                       '.com-conjoon-groupware-email-EmailForm-htmlEditor-body '
                                       + (Ext.isIE ? 'div.editorBodyWrap' : 'pre')
                                   )
                                  + ' '
                                  + getCssTextFromStyleSheet(
                                       '.com-conjoon-groupware-email-EmailForm-htmlEditor-body a'
                                   )
                                  + ' '
                                  + insertDiv
                                  + ' '
                                  + signature
                                  + '</style>'
                                  + '</head>'
                                  + '<body class="com-conjoon-groupware-email-EmailForm-htmlEditor-body">'
                                  + '</body></html>';
        }

        return this.__doc_markup__;
    }

});