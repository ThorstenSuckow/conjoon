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

namespace Conjoon\Data\EntityCreator;

/**
 * @see Conjoon\Data\EntityCreator\Mail\MailEntityCreatorException
 */
require_once 'Conjoon/Data/EntityCreator/Mail/MailEntityCreatorException.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class MailEntityCreatorExceptionTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException Conjoon\Data\EntityCreator\Mail\MailEntityCreatorException
     */
    public function testException()
    {
        throw new \Conjoon\Data\EntityCreator\Mail\MailEntityCreatorException();
    }

}
