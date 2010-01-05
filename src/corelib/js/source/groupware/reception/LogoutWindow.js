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

Ext.namespace('com.conjoon.groupware.reception');


/**
 * @class com.conjoon.groupware.reception.LogoutWindow
 * @extends Ext.Window
 * A window showing buttons for logout/lock workbench functionality.
 * @constructor
 * @param {Object} config The configuration options.
 */
com.conjoon.groupware.reception.LogoutWindow = Ext.extend(Ext.Window, {

    initComponent : function()
    {
        Ext.apply(this, {
            cls        : 'com-conjoon-groupware-reception-LogoutWindow',
            height     : 160,
            modal      : true,
            width      : 260,
            draggable  : false,
            modal      : true,
            closable   : false,
            resizable  : false,
            layout     : 'column',
            items      : [
                new com.conjoon.groupware.reception._LogoutWindowButton({
                    cls         : 'com-conjoon-groupware-reception-LogoutWindow-lockButton',
                    overCls     : 'over',
                    columnWidth : .33,
                    text        : com.conjoon.Gettext.gettext("Standby"),
                    handler     : function () {
                        com.conjoon.groupware.Reception.lockWorkbench();
                        this.close();
                    },
                    scope : this
                }),
                new com.conjoon.groupware.reception._LogoutWindowButton({
                    cls         : 'com-conjoon-groupware-reception-LogoutWindow-logoutButton',
                    overCls     : 'over',
                    columnWidth : .33,
                    text        : com.conjoon.Gettext.gettext("Sign out"),
                    handler     : function () {
                        com.conjoon.groupware.Reception.logout();
                    }
                }),
                new com.conjoon.groupware.reception._LogoutWindowButton({
                    cls         : 'com-conjoon-groupware-reception-LogoutWindow-restartButton',
                    overCls     : 'over',
                    columnWidth : .33,
                    text        : com.conjoon.Gettext.gettext("Restart"),
                    handler     : function () {
                        com.conjoon.groupware.Reception.restart();
                    }
                })
            ],
            buttons : [{
                text    : 'Cancel',
                handler : function() {
                    this.close();
                },
                scope : this
            }]
        });

        com.conjoon.groupware.reception.LogoutWindow.superclass.initComponent.call(this);
    }
});

// private
com.conjoon.groupware.reception._LogoutWindowButton = Ext.extend(Ext.BoxComponent, {

    /**
     * @cfg {String} text
     * The text to display below the button icon
     */

    /**
     * @cfg {Function} handler
     * The handler for the onclick event of the button
     */

    /**
     * @cfg {Object} scope
     * The scope the handler gets called in
     */

    /**
     * Inits this component.
     */
    initComponent : function()
    {
        Ext.apply(this, {
            handler : this.handler || Ext.emptyFn,
            scope   : this.scope   || window,
            autoEl : {
                tag      : 'div',
                cls      : 'com-conjoon-groupware-reception-LogoutWindow-button',
                children : [{
                    tag : 'div',
                    cls : 'button'
                }, {
                    tag  : 'div',
                    cls  : 'text',
                    html : this.text
                }]
            }
        });

        this.on('render', this._onRender, this, {single : true});

        com.conjoon.groupware.reception._LogoutWindowButton.superclass.initComponent.call(this);
    },

    _onRender : function()
    {
        Ext.fly(this.el.dom.firstChild).on('click', this.handler, this.scope);
    }

});

