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
 * Interface for classes creating instances of GetMessageCacheKey.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface GetMessageCacheKeyGen {

    /**
     * Returns an instance of GetMessageCacheKey based on the passed data.
     *
     *
     * @param mixed $data
     *
     * @return \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheKey
     *
     * @throws \Conjoon\Argument\InvalidArgumentException if $data was incmplete or
     * invalid
     */
    public function generateKey($data);
}
