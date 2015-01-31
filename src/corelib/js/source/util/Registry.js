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