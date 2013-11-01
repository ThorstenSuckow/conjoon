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

/**
 * Exception to be thrown if a component is configured stateful, but state id
 * is missing
 *
 * @class {cudgets.state.MissingStateIdException}
 */
Ext.defineClass('cudgets.state.MissingStateIdException', {

    extend : 'cudgets.base.InvalidPropertyException'

});
