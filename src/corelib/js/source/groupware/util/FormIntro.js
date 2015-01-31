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

Ext.namespace('com.conjoon.groupware.util');

/**
 * @class com.conjoon.groupware.form.FormIntro
 * @extends Ext.BoxComponent
 * Utility Formintro class.
 * @constructor
 */
com.conjoon.groupware.util.FormIntro = Ext.extend(Ext.BoxComponent, {

    /**
     * @cfg {String} imageClass
     */

    /**
     * @cfg {Boolean} justify Whether to add "text-align:justify the text of the formIntro".
     * Defaults to false.
     */
    justify : false,

    _textEl : null,

    _labelEl : null,

    autoEl : {
        tag   : 'div',
        children : [{
            tag      : 'div',
            cls      : 'com-conjoon-groupware-util-FormIntro-container',
            children : [{
                tag  : 'div',
                cls  :  'com-conjoon-groupware-util-FormIntro-label',
                html : ''
            }, {
                tag      : 'div',
                cls      : 'com-conjoon-groupware-util-FormIntro-outersep',
                children : [{
                    tag  : 'div',
                    cls  : 'com-conjoon-groupware-util-FormIntro-sepx',
                    html : '&nbsp;'
                }]
            }]
        }, {
            tag  : 'div',
            html : '',
            cls  : 'com-conjoon-groupware-util-FormIntro-description'
        }]
    },

    setLabel : function(label)
    {
        this.labelText = label;

        if (this.rendered) {
            this._labelEl.update(label);
        }
    },

    setText : function(text)
    {
        this.text = text;

        if (this.rendered) {
            if (this.text != undefined) {
                this._textEl.update(this.text);
                this._textEl.setDisplayed(true);
            } else {
                this._textEl.setDisplayed(false);
            }
        }
    },

    onRender : function(ct, position)
    {
        com.conjoon.groupware.util.FormIntro.superclass.onRender.call(this, ct, position);

        this._labelEl = new Ext.Element(this.el.dom.firstChild.firstChild);

        if (this.justify) {
            this.el.dom.lastChild.style.textAlign ="justify";
        }

        this._textEl  = new Ext.Element(this.el.dom.lastChild);

        if (this.labelText) {
            this._labelEl.update(this.labelText);
        }

        if (this.text != undefined) {
            this._textEl.update(this.text);
        } else {
            this._textEl.setDisplayed(false);
        }

        if (this.imageClass) {
            this._textEl.addClass(this.imageClass);
        }

    }
});