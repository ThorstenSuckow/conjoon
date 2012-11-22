<?php
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
 * @see Conjoon_Controller_Action_InvalidContextException
 */
require_once 'Conjoon/Controller/Action/InvalidContextException.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Filter
 * @subpackage UnitTests
 * @group      Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Controller_Action_InvalidContextExceptionTest
    extends PHPUnit_Framework_TestCase {


    /**
     * Ensures everything works as expected
     *
     * @expectedException Conjoon_Controller_Action_InvalidContextException
     *
     * @return void
     */
    public function testException()
    {
        throw new Conjoon_Controller_Action_InvalidContextException();
    }

}
