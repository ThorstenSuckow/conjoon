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


namespace Conjoon\Mail\Client\Service\ServicePatron;

/**
 * A service patron is responsible for changing data retrieved from a service
 * server response to data applicable fro the client.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface ServicePatron {

    /**
     * Returns the data for the specified key.
     * Clients should use this method when querying the array for specific
     * keys and throw the exception if the key does not exist to put the
     * object in an invalid state.
     *
     * @param array $data
     *
     * @return array
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     * @throws \Conjoon\Lang\MissingKeyException
     */
    public function getValueFor($key, array $data);

    /**
     * Returns the data prepared to be used by the client.
     *
     * @param array $data
     *
     * @return array
     *
     * @throws \Conjoon\Mail\Client\Service\ServicePatron\ServicePatronException
     */
    public function applyForData(array $data);

}