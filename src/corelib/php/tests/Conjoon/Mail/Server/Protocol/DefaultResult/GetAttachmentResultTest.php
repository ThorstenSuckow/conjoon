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


namespace Conjoon\Mail\Server\Protocol\DefaultResult;

/**
 * @see SetFlagsResult
 */
require_once 'Conjoon/Mail/Server/Protocol/DefaultResult/GetAttachmentResult.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class GetAttachmentResultTest extends \PHPUnit_Framework_TestCase {

    protected $input;

    /**
     * Ensures everathing works as expected
     */
    public function testOk()
    {
        $entity = new \Conjoon\Data\Entity\Mail\DefaultMessageAttachmentEntity();

        $entity->setMimeType('text/html');
        $entity->setFileName('fileName');

        $successResult = new GetAttachmentResult(
            $entity,
            new \Conjoon\Mail\Client\Message\DefaultAttachmentLocation(
            new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
                new \Conjoon\Mail\Client\Folder\Folder(
                    new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                        '["1", "2"]'
                    )
                ), "1"
            ), "1")
        );

        $this->assertEquals(
            array(
                'content'   => "",
                'contentId' => "",
                'mimeType'  => 'text/html',
                'fileName'  => 'fileName',
                'key'       => '',
                'encoding'  => ''
            ),
            $successResult->toArray()
        );

    }

}
