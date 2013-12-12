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
 * @see \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCache
 */
require_once 'Conjoon/Mail/Client/Service/ServiceResult/Cache/GetMessageCache.php';

/**
 * @see \Conjoon\Data\Cache\ZendCache
 */
require_once 'Conjoon/Data/Cache/ZendCache.php';

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

use \Conjoon\Data\Cache\ZendCache,
    \Conjoon\Argument\ArgumentCheck;

/**
 * Default cache for GetMessageServiceResult objects.
 *
 * @package Conjoon
 * @category Conjoon\Service
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultGetMessageCache extends ZendCache implements GetMessageCache {

    /**
     * @inheritdoc
     *
     * @param \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheKey $id
     */
    public function load($id) {

        $args = array('id' => $id);

        ArgumentCheck::check(array(
            'id' => array(
                'type' => 'instanceof',
                'class' => '\Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheKey'
            )
        ), $args);

        $id = $args['id'];

        return parent::load($id->getValue());

    }

    /**
     * @inheritdoc
     *
     * @param \Conjoon\Mail\Client\Service\ServiceResult\GetMessageServiceResult $data
     * @param \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheKey $id
     *
     */
    public function save($data, $id, array $tags = array()) {

        $args = array('id' => $id, 'data' => $data);

        ArgumentCheck::check(array(
            'data' => array(
                'type' => 'instanceof',
                'class' => '\Conjoon\Mail\Client\Service\ServiceResult\GetMessageServiceResult'
            ),
            'id' => array(
                'type' => 'instanceof',
                'class' => '\Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheKey'
            )
        ), $args);

        $id = $args['id'];

        return parent::save($data, $id->getValue(), $tags);

    }

    /**
     * @inheritdoc
     *
     * @param \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheKey $id
     */
    public function remove($id) {

        $args = array('id' => $id);

        ArgumentCheck::check(array(
            'id' => array(
                'type' => 'instanceof',
                'class' => '\Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheKey'
            )
        ), $args);

        $id = $args['id'];

        return parent::remove($id->getValue());


    }

}
