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
 * Provides common override functionality for Ext components with bugs
 * that have not yet been fixed in a maintenance release.
 */


/**
 * Solves a bug with ext.menu.Menu not firing the "beforeshow" event.
 * - found in version 3.0.0
 * see http://extjs.com/forum/showthread.php?t=73378
 */
Ext.menu.Menu.prototype.showAt = function(xy, parentMenu, /* private: */_e){
    if(this.fireEvent('beforeshow', this) !== false){
        this.parentMenu = parentMenu;
        if(!this.el){
            this.render();
        }
        this.el.setXY(xy);
        if(this.enableScrolling){
            this.constrainScroll(xy[1]);
        }
        this.el.show();
        Ext.menu.Menu.superclass.onShow.call(this);
        if(Ext.isIE){
            this.layout.doAutoSize();
            if(!Ext.isIE8){
                this.el.repaint();
            }
        }
        this.hidden = false;
        this.focus();
        this.fireEvent('show', this);
    }
};