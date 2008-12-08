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

Ext.namespace('de.intrabuild.groupware.reception');


/**
 * @class de.intrabuild.groupware.reception.LogoutWindow
 * @extends Ext.Window
 * A window showing buttons for logout/lock workbench functionality.
 * @constructor
 * @param {Object} config The configuration options.
 */
de.intrabuild.groupware.reception.LogoutWindow = Ext.extend(Ext.Window, {

    initComponent : function()
    {
        Ext.apply(this, {
            cls        : 'de-intrabuild-groupware-reception-LogoutWindow',
            height     : 160,
            modal      : true,
            width      : 260,
            draggable  : false,
            modal      : true,
            closable   : false,
            resizable  : false,
            layout     : 'column',
            items      : [
                new de.intrabuild.groupware.reception._LogoutWindowButton({
                    cls         : 'de-intrabuild-groupware-reception-LogoutWindow-lockButton',
                    overCls     : 'over',
                    columnWidth : .33,
                    text        : de.intrabuild.Gettext.gettext("Standby"),
                    handler     : function () {
                        de.intrabuild.groupware.Reception.lockWorkbench();
                        this.close();
                    },
                    scope : this
                }),
                new de.intrabuild.groupware.reception._LogoutWindowButton({
                    cls         : 'de-intrabuild-groupware-reception-LogoutWindow-logoutButton',
                    overCls     : 'over',
                    columnWidth : .33,
                    text        : de.intrabuild.Gettext.gettext("Logout"),
                    handler     : function () {
                        de.intrabuild.groupware.Reception.logout();
                    }
                }),
                new de.intrabuild.groupware.reception._LogoutWindowButton({
                    cls         : 'de-intrabuild-groupware-reception-LogoutWindow-restartButton',
                    overCls     : 'over',
                    columnWidth : .33,
                    text        : de.intrabuild.Gettext.gettext("Restart"),
                    handler     : function () {
                        de.intrabuild.groupware.Reception.restart();
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

        de.intrabuild.groupware.reception.LogoutWindow.superclass.initComponent.call(this);
    }
});

// private
de.intrabuild.groupware.reception._LogoutWindowButton = Ext.extend(Ext.BoxComponent, {

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
                cls      : 'de-intrabuild-groupware-reception-LogoutWindow-button',
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

        this.on('render', this._onRender, this);

        de.intrabuild.groupware.reception._LogoutWindowButton.superclass.initComponent.call(this);
    },

    _onRender : function()
    {
        Ext.fly(this.el.dom.firstChild).on('click', this.handler, this.scope);
    }

});

