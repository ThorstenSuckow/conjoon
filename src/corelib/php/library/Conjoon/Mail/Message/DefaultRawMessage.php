<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
