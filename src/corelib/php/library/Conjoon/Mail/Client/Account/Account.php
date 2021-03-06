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
 * $URL: http://svn.conjoon.org/trunk/src/corelib/php/library/Conjoon/Mail/Client/Folder/Folder
 */


namespace Conjoon\Mail\Client\Account;

use \Conjoon\Argument\ArgumentCheck;

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';


/**
 * Represents a client site mail account.
 *
 * @category   Conjoon\Mail
 * @package    Conjoon\Mail\Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Account {

    /**
     * @var integer
     */
    protected $id;

    /**
     * Constructs a new instance
     *
     * @param integer $id The id of the mail account
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function __construct($id)
    {
        $data = array('id' => $id);

        ArgumentCheck::check(array(
            'id' => array(
                'type'        => 'integer',
                'allowEmpty'  => false,
                'strict'      => true,
                'greaterThan' => 0
            )
        ), $data);

        $this->id = $data['id'];
    }

    /**
     * Returns the id of this account.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a textual representation of an instanceof this class.
     */
    public function __toString() {
        return json_encode(
            array(
                'id' => $this->getId()
            )
        );
    }
}

