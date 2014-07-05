/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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

Ext.Direct.addProvider({
    enableUrlEncode : 'extDirectData',
    url             : './default',
    format          : 'json',
    type            : 'zend',
    actions         : {
        applicationCache : [{
            name : 'setClearFlag',
            len  : 1
        }],
        registry : [{
            name : 'getEntries',
            len  : 0
        }, {
            name : 'setEntries',
            len  : 1
        }],
        reception : [{
            name : 'getUser',
            len  : 0
        }, {
            name : 'ping',
            len  : 0
        }, {
            name : 'logout',
            len  : 0
        }, {
            name : 'lock',
            len  : 0
        }]
    },
    namespace : 'com.conjoon.defaultProvider'
});