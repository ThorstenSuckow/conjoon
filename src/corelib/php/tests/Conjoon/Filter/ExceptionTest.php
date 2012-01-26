<?php
/**
 * conjoon
 * (c) 2002-2011 siteartwork.de/conjoon.org
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
 * @see Conjoon_Filter_Exception
 */
require_once 'Conjoon/Filter/Exception.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Filter
 * @subpackage UnitTests
 * @group      Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_ExceptionTest extends PHPUnit_Framework_TestCase {

    /**
     * Conjoon_Filter_Exception object
     *
     * @var Conjoon_Filter_Exception
     */
    protected $_exception;

    /**
     * Creates a new Conjoon_Filter_Exception object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_exception = new Conjoon_Filter_Exception();
    }

    /**
     * Ensures that the exception derives from Conjoon_Exception
     *
     * @return void
     */
    public function testParentClass()
    {
        $this->assertTrue(
            $this->_exception instanceof Conjoon_Exception
        );
    }
}
