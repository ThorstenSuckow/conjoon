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


namespace Conjoon\Mail\Client\Service\ServiceResult\Cache;

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

use \Conjoon\Argument\ArgumentCheck;

/**
 * Class representing the id for a cached GetMessageServiceResult.
 * Do not use this class directly. Instances of this class
 * must be created by GetMessageCacheKeyGen.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class GetMessageCacheKey {

    /**
     * @type string $value
     */
    protected $value;

    /**
     * Creates a new instance of this class.
     *
     * @param string $value
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function __construct($value) {

        $args = array('value' => $value);

        ArgumentCheck::check(array(
            'value' => array(
                'type' => 'string',
                'allowEmpty' => false,
                'strict' => true
            )
        ), $args);

        $this->value = $args['value'];
    }

    /**
     * Returns the string value of this instance.
     *
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Returns a textual representation of this instance.
     *
     * @return string
     */
    public function __toString() {
        return $this->getValue();
    }

}
