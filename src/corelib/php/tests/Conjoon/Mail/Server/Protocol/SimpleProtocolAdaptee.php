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


namespace Conjoon\Mail\Server\Protocol;

/**
 * @see ProtocolAdaptee
 */
require_once 'Conjoon/Mail/Server/Protocol/ProtocolAdaptee.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class SimpleProtocolAdaptee implements ProtocolAdaptee {

    protected $alwaysSucceed;

    public function __construct($alwaysSucceed = true)
    {
        $this->alwaysSucceed = $alwaysSucceed;
    }

    /**
     * @inheritdoc
     */
    public function setFlags(
        \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection $flagCollection,
        \Conjoon\User\User $user)
    {
        if ($this->alwaysSucceed) {
            return new \Conjoon\Mail\Server\Protocol\DefaultResult\SetFlagsResult();
        }

        throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
            "Unexpected Protocol Exception"
        );
    }

}
