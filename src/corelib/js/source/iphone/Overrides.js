/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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

/**
 * Common overrides for ExtJS when conjoon is used on an iPhone.
 * Make sure to load this file after the ExtJS files and the default Overrides.js
 * file were loaded.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
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

