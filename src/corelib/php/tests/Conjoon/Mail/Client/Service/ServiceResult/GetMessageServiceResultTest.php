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


namespace Conjoon\Mail\Client\Service\ServiceResult;

/**
 * @see DefaultServiceResult
 */
require_once 'Conjoon/Mail/Client/Service/DefaultServiceResultTest.php';

use \Conjoon\Mail\Client\Service\DefaultServiceResultTest;

/**
 * @category   Conjoon
 * @package    Conjoon_Service
 * @subpackage UnitTests
 * @group      Conjoon_Service
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class GetMessageServiceResultTest extends DefaultServiceResultTest {

    protected $clName = "\Conjoon\Mail\Client\Service\ServiceResult\GetMessageServiceResult";

}
