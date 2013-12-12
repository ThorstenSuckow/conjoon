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
