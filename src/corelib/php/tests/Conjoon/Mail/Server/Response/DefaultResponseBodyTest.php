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


namespace Conjoon\Mail\Server\Response;

/**
 * @see DefaultResponseBody
 */
require_once 'Conjoon/Mail/Server/Response/DefaultResponseBody.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultResponseBodyTest extends \PHPUnit_Framework_TestCase {


    public function testOk()
    {
        new DefaultResponseBody();

        new DefaultResponseBody(array());

        $arr = array("data");

        $body = new DefaultResponseBody($arr);

        $this->assertSame(
            $arr,
            $body->getData()
        );
    }

}