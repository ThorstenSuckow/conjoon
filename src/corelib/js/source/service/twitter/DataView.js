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

Ext.namespace('com.conjoon.service.twitter');

/**
 * A special implementation of {Ext.DataView} for use with views that need to render
 * data related to the Twitter service.
 *
 * @class com.conjoon.service.twitter.DataView
 * @extends Ext.DataView
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.service.twitter.DataView = Ext.extend(Ext.DataView, {

    /**
     * @cfg {Boolean} maskToParent Whether to attach the {Ext.LoadMask} used
     * by this view to its container or to the view itself. Defaults to true.
     *
     */
    maskToParent : true,

    /**
     * @cfg {Boolean} clearBeforeLoad If set to true, the views contents
     * will be whiped before the store loads, i.e. rendering it as empty.
     * Defaults to false.
     */
    clearBeforeLoad : false,

    /**
     * Overwrites parent's implementation by attaching/detaching the
     * specific listeners for the store.
     *
     * @param {Ext.data.Store} store
     * @param {Boolean} initial
     */
    setStore : function(store, initial)
    {
        if (!initial && this.store){
            this.store.un("load", this.onLoad, this);
        }
        if (store) {
            Ext.StoreMgr.lookup(store).on("load", this.onLoad, this);
        }

        com.conjoon.service.twitter.DataView.superclass.setStore.call(this, store, initial);
    },

    /**
     * Overwrites parent's implementation by clearing this view's contents
     * if clearBeforeLoad was set to true and showing an {Ext.LoadMask}, which
     * will be either added to this view's container or to the view itself, based
     * on the maskToParent property.
     * Note, that the loadmask will only be shown if this view's property "loadingText"
     * equals to anything but undefined or null.
     *
     * @param {Ext.data.Store} store
     * @param {Object} options
     *
     */
    onBeforeLoad : function(store, options)
    {
        if (this.clearBeforeLoad) {
            this.clearSelections(false, true);
            this.el.update("");
            this.all.clear();
        } else if (this.el.dom.innerHTML == this.emptyText) {
            this.el.update("");
        }

        if(this.loadingText){
            if (!this.loadMask) {
                var el = this.maskToParent ? this.el.dom.parentNode : this.el;
                this.loadMask = new Ext.LoadMask(el, {
                    msg : this.loadingText
                });
            }
            this.loadMask.show();
        }
    },

    /**
     * Listens to the attached store's load event and hide the loadMask
     * if one was rendered during firing of the beforeload-event.
     *
     */
    onLoad : function()
    {
        if (this.loadMask) {
            this.loadMask.hide();
        }
    }

});

