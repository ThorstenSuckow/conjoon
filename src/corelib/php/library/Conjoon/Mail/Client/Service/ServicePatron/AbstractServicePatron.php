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


namespace Conjoon\Mail\Client\Service\ServicePatron;

use Conjoon\Argument\ArgumentCheck,
    Conjoon\Lang\MissingKeyException;

/**
 * @see \Conjoon\Lang\MissingKeyException
 */
require_once 'Conjoon/Lang/MissingKeyException.php';

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * @see \Conjoon\Mail\Client\Service\ServicePatron\ServicePatron
 */
require_once 'Conjoon/Mail/Client/Service/ServicePatron/ServicePatron.php';

/**
 * A service patron is responsible for changing data retrieved from a service
 * server response to data applicable fro the client.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class AbstractServicePatron
    implements \Conjoon\Mail\Client\Service\ServicePatron\ServicePatron {

    /**
     * @inheritdoc
     */
    public function getValueFor($key, array $data)
    {
        $keydata = array('key' => $key);

        ArgumentCheck::check(array(
            'key' => array(
                'type'       => 'string',
                'allowEmpty' => false
            )
        ), $keydata);

        $key = $keydata['key'];

        if (!array_key_exists($key, $data)) {
            throw new MissingKeyException(
                "key \"$key\" does not exist in data"
            );
        }

        return $data[$key];
    }

    /**
     * Alias for getValueFor()
     *
     * @see getValueFor
     */
    protected function v($key, array $data)
    {
        return $this->getValueFor($key, $data);
    }
}