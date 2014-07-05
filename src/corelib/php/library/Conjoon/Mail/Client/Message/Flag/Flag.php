<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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


namespace Conjoon\Mail\Client\Message\Flag;

/**
 * A client message flag is a oo representation of a message flag. A message
 * flag exists of an id for the message, and a boolean value clear which
 * tells whether the flag is about to be set or unset.
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class Flag implements \Conjoon\Mail\Message\Flag\MessageFlag{

    /**
     * @var string
     */
    protected $_uId;

    /**
     * @var bool
     */
    protected $_clear;

    /**
     * Constructs a new instance of this class.
     *
     * @param string $uId
     * @param bool $clear whether the flag represented by this class
     * should be removed, or not
     *
     * @throws Conjoon_Argument_Exception if either $meesageId or $clear
     * did not evaluate to the expected types.
     */
    public function __construct($uId, $clear = false)
    {
        $data = array('uId' => $uId, 'clear' => $clear);

        /**
         * @see Conjoon_Argument_Check
         */
        require_once 'Conjoon/Argument/Check.php';

        \Conjoon_Argument_Check::check(array(
            'uId' => array(
                'type'       => 'string',
                'allowEmpty' => false
            ),
            'clear' => array(
                'type'       => 'bool',
                'allowEmpty' => false
            ),
        ), $data);

        $this->_uId = $data['uId'];
        $this->_clear     = $data['clear'];
    }

    /**
     * @inheritdoc
     */
    public function getUId()
    {
        return $this->_uId;
    }

    /**
     * @inheritdoc
     */
    public function isClear()
    {
        return $this->_clear;
    }

}

