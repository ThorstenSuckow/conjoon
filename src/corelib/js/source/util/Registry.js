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

Ext.namespace('de.intrabuild.util');

de.intrabuild.util._Registry = function()
{
    this.addEvents({
        'register'   : true,
        'unregister' : true
    });

    de.intrabuild.util._Registry.superclass.constructor.call(this);
};

Ext.extend(de.intrabuild.util._Registry, Ext.util.Observable, {

    map : {},

    register : function(name, object, registerBeforeDestroy)
    {
        this.map[name] = object;
        this.fireEvent('register', name, object);
        if (registerBeforeDestroy === true) {
            object.on('destroy', this.unregister.createDelegate(this, [name]));
        }
    },

    unregister : function(name)
    {
        delete this.map[name];
        this.fireEvent('unregister', name);
    },

    get : function(name)
    {
        if (this.map[name] == undefined) {
            return null;
        }

        return this.map[name];
    }

});

de.intrabuild.util.Registry = new de.intrabuild.util._Registry();