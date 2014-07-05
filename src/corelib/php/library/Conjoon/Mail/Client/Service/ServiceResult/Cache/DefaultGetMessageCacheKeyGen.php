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


namespace Conjoon\Mail\Client\Service\ServiceResult\Cache;

/**
 * @see \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheKeyGen
 */
require_once 'Conjoon/Mail/Client/Service/ServiceResult/Cache/GetMessageCacheKeyGen.php';

/**
 * @see \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheKey;
 */
require_once 'Conjoon/Mail/Client/Service/ServiceResult/Cache/GetMessageCacheKey.php';

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

use \Conjoon\Argument\ArgumentCheck;

/**
 * Default implementation for a message cache key generator.
 *
 * @package Conjoon
 * @category Conjoon\Service.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultGetMessageCacheKeyGen implements GetMessageCacheKeyGen {

    /**
     * @inheritdoc
     */
    public function generateKey($data) {

        ArgumentCheck::check(array(
            'userId' => array(
                'type' => 'int',
                'greaterThan' => 0,
                'allowEmpty' => false
            ),
            'messageId' => array(
                'type' => 'string',
                'greaterThan' => 0,

                'allowEmpty' => false
            ),
            'path' => array(
                'type' => 'string',
                'strict' => true,
                'allowEmpty' => false
            ),
            'format' => array(
                'type' => 'string',
                'strict' => true,
                'allowEmpty' => false
            ),
            'externalResources' => array(
                'type' => 'bool',
                'allowEmpty' => false
            )
        ), $data);

        $path = json_decode($data['path']);

        if ($path === null || !is_array($path)) {
            throw new \Conjoon\Argument\InvalidArgumentException(
                "\"path\" does not seem to be a valid json encoded path"
            );
        }

        $strPath = implode('_', $path);

        if (preg_match('~^[a-zA-Z0-9_]+$~D', $strPath) === 0) {
            $strPath = md5($strPath);
        }

        $arrStr = array(
            $data['userId'],
            $data['messageId'],
            $strPath,
            $data['format'],
            (string) ((int) $data['externalResources'])
        );

        return new \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheKey(
            implode('_', $arrStr)
        );
    }
}
