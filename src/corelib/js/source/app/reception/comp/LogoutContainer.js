/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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
 * $Author: T. Suckow $
 * $Id: LogoutWindow.js 1457 2012-10-28 18:55:17Z T. Suckow $
 * $Date: 2012-10-28 19:55:17 +0100 (So, 28 Okt 2012) $
 * $Revision: 1457 $
 * $LastChangedDate: 2012-10-28 19:55:17 +0100 (So, 28 Okt 2012) $
 * $LastChangedBy: T. Suckow $
 * $URL: http://svn.conjoon.org/trunk/src/corelib/js/source/groupware/reception/LogoutWindow.js $
 */

/**
 * @class conjoon.reception.comp.LogoutContainer
 * @extends Ext.Container
 * A container for showing various logout/lock options related to the workbench
 * @constructor
 * @param {Object} config The configuration options.
 */
Ext.defineClass('conjoon.reception.comp.LogoutContainer', {

    extend : 'Ext.Container',

    cls   : 'cn-reception-logoutContainer',

    /**
     * Overrides parent implementation by taking care of rendering the
     * container.
     */
    show : function() {

        var me = this;

        if (!this.rendered) {
            this.render(document.body);

            this.el.fadeIn('t', {
                duration : 0.2
            });
        }

        conjoon.reception.comp.LogoutContainer.superclass.show.call(this);
    },

    /**
     * Destroys this container with its child elements.
     *
     * @param {String} closeContext The context the login container is closed in.
     * Can be 'login' or 'exit'
     */
    close : function(closeContext) {

        var me = this;

        if (closeContext) {
            var slideOutTo = 't';
            if (closeContext === 'login') {
                // okay
            } else if (closeContext === 'exit') {
                slideOutTo = 'tt';
            }

            this.el.ghost(slideOutTo, {
                duration : 0.8,
                easing: 'easeOut',
                remove   : true,
                callback : function() {
                    this.destroy();
                },
                scope : me
            });


            return;
        }



        this.destroy();
    },


    /**
     * Inits this component.
     *
     */
    initComponent : function()
    {

        var me = this;

        me.items = [
            new Ext.BoxComponent({
                autoEl : {
                    tag : 'div',
                    cls : 'labelCnt',
                    children : [{
                            tag : 'div',
                            cls : 'label',
                            html : com.conjoon.Gettext.gettext("Lock")
                        }, {
                            tag : 'div',
                            cls : 'label',
                            html : com.conjoon.Gettext.gettext("Sign Out")
                        }, {
                            tag : 'div',
                            cls : 'label',
                            html : com.conjoon.Gettext.gettext("Restart")
                    }]

                }

            })
        ];


        conjoon.reception.comp.LogoutContainer.superclass.initComponent.call(this);
    }
});