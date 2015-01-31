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

/**
 * Common overrides for ExtJS when conjoon is used on an iPhone.
 * Make sure to load this file after the ExtJS files and the default Overrides.js
 * file were loaded.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */


/**
 * Windows on the iPhone have no shadow per default, and are neither
 * draggable nor resizable. They will be shown using an animation, sliding out
 * from the bottom of the iPhone screen. Their width will always equal to the
 * width of the screen (browser's width), while their height may vary.
 */
Ext.override(Ext.Window, {

    draggable : false,
    resizable : false,
    shadow    : false,

    animateTarget : Ext.get(document),

    close : function()
    {
        if(this.fireEvent("beforeclose", this) !== false){
            this.el.slideOut("b", {
                duration : .2,
                remove   : true,
                scope    : this,
                callback : function() {
                    this.fireEvent('close', this);
                    this.destroy();
                }
            });
        }
    },

    animShow: function()
    {
        this.setSize(document.body.offsetWidth, this.height);
        this.el.alignTo(document, "br-br");
        this.el.slideIn('b', {
            duration : .2,
            callback : this.afterShow,
            scope    : this
        });
    },

    animHide: function(){
        this.el.slideOut("b", {
            duration : .2,
            scope    : this,
            callback : this.afterHide
        });
    }

});

