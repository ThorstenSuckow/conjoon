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

        $arrStr = array(
            $data['userId'],
            $data['messageId'],
            implode('_', $path),
            $data['format'],
            (string) ((int) $data['externalResources'])
        );

        return new \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheKey(
            implode('_', $arrStr)
        );
    }
}
