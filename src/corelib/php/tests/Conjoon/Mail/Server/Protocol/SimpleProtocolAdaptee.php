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

    /**
     * @inheritdoc
     */
    public function getMessage(
        \Conjoon\Mail\Client\Message\MessageLocation $messageLocation,
        \Conjoon\User\User $user)
    {
        if ($this->alwaysSucceed) {
            return new \Conjoon\Mail\Server\Protocol\DefaultResult\GetMessageResult(
                new \Conjoon\Data\Entity\Mail\ImapMessageEntity(),
                new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
                    new \Conjoon\Mail\Client\Folder\Folder(
                        new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                            '["1", "2"]'
                        )
                    ),
                    "1"
                )
            );
        }

        throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
            "Unexpected Protocol Exception"
        );
    }

    /**
     * @inheritdoc
     */
    public function getAttachment(
        \Conjoon\Mail\Client\Message\AttachmentLocation $attachmentLocation,
        \Conjoon\User\User $user)
    {
        if ($this->alwaysSucceed) {
            return new \Conjoon\Mail\Server\Protocol\DefaultResult\GetAttachmentResult(
                new \Conjoon\Data\Entity\Mail\DefaultAttachmentEntity(),
                    new \Conjoon\Mail\Client\Message\DefaultAttachmentLocation(
                     new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
                    new \Conjoon\Mail\Client\Folder\Folder(
                        new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                            '["1", "2"]'
                        )
                    ),
                    "1"
                ), "1")
            );
        }

        throw new \Conjoon\Mail\Server\Protocol\ProtocolException(
            "Unexpected Protocol Exception"
        );
    }

}
