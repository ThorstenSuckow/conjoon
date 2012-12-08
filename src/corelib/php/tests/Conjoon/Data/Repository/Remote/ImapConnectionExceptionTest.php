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


namespace Conjoon\Data\Repository\Remote;

/**
 * @see Conjoon\Data\Repository\Remote\ImapConnectionException
 */
require_once 'Conjoon/Data/Repository/Remote/ImapConnectionException.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ImapConnectionExceptionTest extends \PHPUnit_Framework_TestCase {


    /**
     * Ensures everything works as expected
     *
     * @expectedException \Conjoon\Data\Repository\Remote\ImapConnectionException
     *
     * @return void
     */
    public function testException()
    {
        throw new ImapConnectionException();
    }

}
