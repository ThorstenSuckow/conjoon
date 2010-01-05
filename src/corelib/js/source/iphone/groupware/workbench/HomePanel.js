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

Ext.namespace('com.conjoon.iphone.groupware.workbench');

/**
 * This panel is capable of rendering a list of icons into the viewport, representing
 * the available modules. Clicking such an icon will trigger the navclick event, which will
 * tell which associated panel should be activated. Listeners are advised to render/hide components
 * on this event accordingly.
 * The HomePanel contains a toolbar which should be used to place globally
 * navigation elements (e.g. Sign Out button etc.).
 *
 * @class com.conjoon.iphone.groupware.workbench.HomePanel
 * @extends Ext.Viewport
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.iphone.groupware.workbench.HomePanel = Ext.extend(Ext.Panel, {

    /**
     * @cfg {Object} appIconEl Ext.TheDomHelper object used to render
     * the entries on this panel.
     */
    appIconEl : {
        tag : 'div',
        cls : 'app-icon'
    },

    /**
     * @type {Array} _navEntries
     * @protected
     */
    _navEntries : null,


    /**
     * Inits this component.
     */
    initComponent : function()
    {
        this._navEntries = [];

        this.addEvents(
            /**
             * @event navlick
             * @param {com.conjoon.iphone.groupware.workbench.HomePanel} homePanel
             * @param {Ext.Panel} referencedPanel
             */
            'navclick'
        );

        Ext.apply(this, {
            cls    : 'homePanel',
            border : false,
            bbar   : this.getToolbar()
        });

        com.conjoon.iphone.groupware.workbench.HomePanel.superclass.initComponent.call(this);
    },

    /**
     * Called immediately after the component has been rendered.
     * Will call parent's implementation and query the _navEntries property
     * for looking up navigation entries to render.
     */
    afterRender : function()
    {
        com.conjoon.iphone.groupware.workbench.HomePanel.superclass.afterRender.call(this);

        var body = this.body;

        var panel = null;
        var nEl   = null;

        for (var i = 0, len = this._navEntries.length; i < len; i++) {
            panel = this._navEntries[i];
            nEl = Ext.DomHelper.append(this.body, this.appIconEl, true)

            nEl.addClass(panel.appIconCls);
            nEl.set({title : panel.appTitle});
            nEl.on('click', function() {
                this.fireEvent('navclick', this, panel);
            }, this);
        }
    },


// -------- public api

    /**
     * Adds a navigatione entry which will be rendered in the panel as a clickable
     * element. Clicking the entry will trigger the navclick event.
     * The added panel should contain the properties "appIconCls" and "appTitle",
     * holding the icon class used to render into the element and the title of
     * the navigation entry.
     *
     * @param {Ext.Panel} panel
     */
    addNavPanel : function(panel)
    {
        this._navEntries.push(panel);
    },

    /**
     * Returns the toolbar used for this panel.
     *
     * @return {Ext.Toolbar}
     */
    getToolbar : function()
    {
        if (!this._toolbar) {
            this._toolbar = this._getToolbar();
        }

        return this._toolbar;
    },


// -------- builders

    /**
     * Overwrite this to return a custom toolbar for this panel.
     *
     * @protected
     */
    _getToolbar : function()
    {
        return new Ext.Toolbar({
            items : [
                new Ext.Button({
                    iconCls : 'homeButton-icon',
                    menu    : new Ext.menu.Menu({
                        items : [{
                            text    : com.conjoon.Gettext.gettext("Sign out..."),
                            handler : function() {
                                com.conjoon.groupware.Reception.showLogout();
                            }
                        }]
                    })
                })
            ]
        });
    }

});