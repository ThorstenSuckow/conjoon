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

Ext.namespace('com.conjoon.iphone.groupware.reception');

/**
 * Overrides {com.conjoon.groupware.reception.LoginWindow} to apply custom behavior.
 * The height of an instance of this class will default to the height of the document.
 *
 * @class com.conjoon.iphone.groupware.reception.LoginWindow
 * @extends com.conjoon.groupware.reception.LoginWindow
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.iphone.groupware.reception.LoginWindow = Ext.extend(com.conjoon.groupware.reception.LoginWindow, {

    /**
     * Inits this component.
     *
     */
    initComponent : function()
    {
        this.height = document.body.offsetHeight;

        com.conjoon.iphone.groupware.reception.LoginWindow.superclass.initComponent.call(this);
    }

});