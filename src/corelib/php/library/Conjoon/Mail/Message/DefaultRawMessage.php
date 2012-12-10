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

namespace Conjoon\Mail\Message;

use Conjoon\Argument\ArgumentCheck;

/**
 * @see \Conjoon\Mail\Message\RawMessage
 */
require_once 'Conjoon/Mail/Message/RawMessage.php';

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * Default implmentation of a RawMessage.
 *
 * @uses RawMessage
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultRawMessage implements RawMessage {

    /**
     * @var string
     */
    protected $header;

    /**
     * @var string
     */
    protected $body;

    /**
     * Creates a new instance of this class.
     *
     * @param string $header
     * @param string $body
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function __construct($header, $body)
    {
        $data = array('header' => $header, 'body' => $body);

        ArgumentCheck::check(array(
            'header' => array(
                'type'       => 'string',
                'allowEmpty' => false
            ),
            'body' => array(
                'type'       => 'string',
                'allowEmpty' => true
            )
        ), $data);

        $this->header = $data['header'];
        $this->body   = $data['body'];
    }

    /**
     * @inheritdoc
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return $this->body;
    }

}
