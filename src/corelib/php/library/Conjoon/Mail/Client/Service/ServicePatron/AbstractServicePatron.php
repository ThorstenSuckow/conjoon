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