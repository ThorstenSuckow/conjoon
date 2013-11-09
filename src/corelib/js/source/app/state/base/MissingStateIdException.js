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
 * @class {conjoon.state.MissingStateIdException}
 */
Ext.defineClass('conjoon.state.base.MissingStateIdException', {

    extend : 'cudgets.state.MissingStateIdException'

});
