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
        this.on('initialize', this._onEditorInitialized, this);

        com.conjoon.groupware.email.form.EmailEditor.superclass.initEditor.call(this);
    },



    _onEditorInitialized : function()
    {
        var doc = this.getDoc();

        // unbind the Ext default fixKeeys implementation and use custom one so that
        // blockqouotes will be quoted properly
        if (Ext.isIE) {

            Ext.EventManager.un(doc, 'keydown', this.fixKeys, this);

            Ext.EventManager.on(doc, 'keydown', function(e){
                var k = e.getKey(), r;
                if(k == e.TAB){
                    e.stopEvent();
                    r = this.getDoc().selection.createRange();
                    if(r){
                        r.collapse(true);
                        r.pasteHTML('&nbsp;&nbsp;&nbsp;&nbsp;');
                        this.deferFocus();
                    }
                }else if(k == e.ENTER){

                    r = this.getDoc().selection.createRange();

                    if(r) {
                        var target     = r.parentElement(),
                            targetName = target
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