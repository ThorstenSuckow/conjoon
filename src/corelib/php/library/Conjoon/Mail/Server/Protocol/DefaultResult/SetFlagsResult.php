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


namespace Conjoon\Mail\Server\Protocol\DefaultResult;

/**
 * @see \Conjoon\Mail\Server\Protocol\SuccessResult
 */
require_once 'Conjoon/Mail/Server/Protocol/SuccessResult.php';

/**
 * A default implematation of an SetFlags result.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class SetFlagsResult implements \Conjoon\Mail\Server\Protocol\SuccessResult {

    /**
     * @inheritdoc
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return array('setFlags' => true);
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->toJson();
    }

}