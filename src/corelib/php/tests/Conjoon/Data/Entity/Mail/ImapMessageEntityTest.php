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
 * @see Conjoon\Data\Entity\Mail\ImapMessageEntity
 */
require_once 'Conjoon/Data/Entity/Mail/ImapMessageEntity.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ImapMessageEntityTest extends \PHPUnit_Framework_TestCase {

    protected $input;

    protected function setUp()
    {
        $this->input = array(
            'date'                  => new \DateTime,
            'subject'               => "subject",
            'from'                  => "from",
            'replyTo'               => "replyTo",
            'to'                    => "To",
            'cc'                    => "cc",
            'bcc'                   => "bcc",
            'inReplyTo'             => "in_reply_to",
            'references'            => "References",
            'contentTextPlain'      => "content text plain",
            'contentTextHtml'       => "content text html",
            'recipients'            => "Recipients",
            'sender'                => "Sender"
        );
    }

    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        $user = new DefaultMessageEntity();

        foreach ($this->input as $field => $value) {
            $methodSet = "set" . ucfirst($field);
            $methodGet = "get" . ucfirst($field);
            $user->$methodSet($value);

            $this->assertSame($value, $user->$methodGet());
        }
    }
}