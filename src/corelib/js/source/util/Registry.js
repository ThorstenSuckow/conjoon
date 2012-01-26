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

Ext.namespace('com.conjoon.util');

com.conjoon.util._Registry = function()
{
    this.addEvents({
        'register'   : true,
        'unregister' : true
    });

    com.conjoon.util._Registry.superclass.constructor.call(this);
};

Ext.extend(com.conjoon.util._Registry, Ext.util.Observable, {

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

com.conjoon.util.Registry = new com.conjoon.util._Registry();