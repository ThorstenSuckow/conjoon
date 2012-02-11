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
 * @see Conjoon_Mail
 */
require_once 'Conjoon/Mail.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_MailTest extends PHPUnit_Framework_TestCase {

    /**
     * Conjoon_Mail object
     *
     * @var Conjoon_Mail
     */
    protected $_mail;

    /**
     * Creates a new Conjoon_Mail object for each test
     *
     * @return void
     */
    public function setUp()
    {
        $this->_mail = new Conjoon_Mail();
    }

    /**
     * Creates a new Conjoon_Mail object for each test
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->_mail);
    }

// +---------------------------------------------------------------------------
// | Tests
// +---------------------------------------------------------------------------


    public function testSetAndGetReference()
    {
        $referenceString =
            "<e557c8cf463273ec624b3967a09017e43639a851@branches.01fix.conjoon> "
                . "<4741d30f08afa27ea7aec32dedfa2c2ec777310e@branches.01fix.conjoon>";

        $expected =
            "<e557c8cf463273ec624b3967a09017e43639a851@branches.01fix.conjoon>\n\t"
                . "<4741d30f08afa27ea7aec32dedfa2c2ec777310e@branches.01fix.conjoon>";


        $this->assertNull($this->_mail->getReferences());

        $this->_mail->setReferences($referenceString);

        $resPlain  = $this->_mail->getReferences();
        $resPlain2  = $this->_mail->getReferences(true);
        $resEncode = $this->_mail->getReferences(false);

        $this->assertSame($expected, $resEncode);
        $this->assertSame($resPlain, $referenceString);
        $this->assertSame($resPlain2, $referenceString);


    }

    /**
     * @link http://conjoon.org/issues/browse/CN-351
     */
    public function testSetReferences_CN351()
    {
        $referenceString =
            "<f702633c366c90324ac1ec723786980aa76aa3f3@dev.conjoon.de> "
            ."<1360469F1169454BBA50482748C1CD00025D52EF3188@EX-MAILBOX01.bbb.rwth-aachen.de> "
            ."<35bd62d8de4ff8bb4b59fe99c02910aedb7de773@dev.conjoon.de> "
            ."<1360469F1169454BBA50482748C1CD00025D52EF3189@EX-MAILBOX01.bbb.rwth-aachen.de> "
            ."<45eaed74d94cff998fb52e15d57ffdf2888e959d@dev.conjoon.de> "
            ."<1360469F1169454BBA50482748C1CD00025D52EF318A@EX-MAILBOX01.bbb.rwth-aachen.de> "
            ."<e1d26ba984ada141ed98daaf99af0a0d4797dd98@dev.conjoon.de> "
            ."<1360469F1169454BBA50482748C1CD00025D52EF318B@EX-MAILBOX01.bbb.rwth-aachen.de> "
            ."<6a919fb278560c9a0a31bc774eaea5344efcb2a6@dev.conjoon.de> "
            ."<1360469F1169454BBA50482748C1CD00025D52EF318C@EX-MAILBOX01.bbb.rwth-aachen.de> "
            ."<1c62d8bcff9a36fa2b5c5d59e074dfc593daba55@dev.conjoon.de> "
            ."<1360469F1169454BBA50482748C1CD00025D52EF318D@EX-MAILBOX01.bbb.rwth-aachen.de> "
            ."<ccbee384973c4f3d34f2b934be9e3ebd1db84cdc@dev.conjoon.de> "
            ."<1360469F1169454BBA50482748C1CD00025D52EF318E@EX-MAILBOX01.bbb.rwth-aachen.de>";


        $expected =
            "<f702633c366c90324ac1ec723786980aa76aa3f3@dev.conjoon.de>\n\t"
            ."<1360469F1169454BBA50482748C1CD00025D52EF3188@EX-MAILBOX01.bbb.rwth-aachen.de>\n\t"
            ."<35bd62d8de4ff8bb4b59fe99c02910aedb7de773@dev.conjoon.de>\n\t"
            ."<1360469F1169454BBA50482748C1CD00025D52EF3189@EX-MAILBOX01.bbb.rwth-aachen.de>\n\t"
            ."<45eaed74d94cff998fb52e15d57ffdf2888e959d@dev.conjoon.de>\n\t"
            ."<1360469F1169454BBA50482748C1CD00025D52EF318A@EX-MAILBOX01.bbb.rwth-aachen.de>\n\t"
            ."<e1d26ba984ada141ed98daaf99af0a0d4797dd98@dev.conjoon.de>\n\t"
            ."<1360469F1169454BBA50482748C1CD00025D52EF318B@EX-MAILBOX01.bbb.rwth-aachen.de>\n\t"
            ."<6a919fb278560c9a0a31bc774eaea5344efcb2a6@dev.conjoon.de>\n\t"
            ."<1360469F1169454BBA50482748C1CD00025D52EF318C@EX-MAILBOX01.bbb.rwth-aachen.de>\n\t"
            ."<1c62d8bcff9a36fa2b5c5d59e074dfc593daba55@dev.conjoon.de>\n\t"
            ."<1360469F1169454BBA50482748C1CD00025D52EF318D@EX-MAILBOX01.bbb.rwth-aachen.de>\n\t"
            ."<ccbee384973c4f3d34f2b934be9e3ebd1db84cdc@dev.conjoon.de>\n\t"
            ."<1360469F1169454BBA50482748C1CD00025D52EF318E@EX-MAILBOX01.bbb.rwth-aachen.de>";

        $this->_mail->setReferences($referenceString);

        $res = $this->_mail->getReferences(false);

        $this->assertSame($expected, $res);
    }

    public function testSetAndGetMessageId()
    {
        $this->assertNull($this->_mail->getMessageId());

        $this->_mail->setMessageId(true);

        $id = $this->_mail->getMessageId();

        $this->assertNotSame(substr($id, 0, 1), "<");
        $this->assertNotSame(substr($id, strlen($id)-1, 1), ">");

        $this->assertSame(trim($id, "<>"), $id);

    }

    /**
     * @link http://conjoon.org/issues/browse/CN-445
     */
    public function testCreateMessageId_CN445()
    {
        $id = $this->_mail->createMessageId();

        $this->assertNotSame(substr($id, 0, 1), "<");
        $this->assertNotSame(substr($id, strlen($id)-1, 1), ">");

        $this->assertSame(trim($id, "<>"), $id);
    }

}
