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

namespace Conjoon\Data\Entity\Mail;

/**
 * @see Conjoon\Data\Entity\Mail\DefaultFolderMappingEntity
 */
require_once 'Conjoon/Data/Entity/Mail/DefaultFolderMappingEntity.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultFoldermappingEntityTest extends \PHPUnit_Framework_TestCase {

    protected $input;

    protected function setUp()
    {
        $this->input = array(
            'globalName' => '/global/name',
            'type' => 'INBOX',
            'mailAccount' => new \Conjoon\Data\Entity\Mail\DefaultMailAccountEntity()
        );
    }

    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        $folder = new DefaultFolderMappingEntity();

        foreach ($this->input as $input => $value) {

            $set = 'set' . ucfirst($input);
            $get = 'get' . ucfirst($input);

            $folder->$set($value);

            $this->assertSame($value, $folder->$get());

        }
    }

}