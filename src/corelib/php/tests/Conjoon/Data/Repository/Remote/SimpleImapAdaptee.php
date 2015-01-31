<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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


namespace Conjoon\Data\Repository\Remote;

/**
 * @see Conjoon\Data\Repository\Remote\AbstractImapAdaptee
 */
require_once 'Conjoon/Data/Repository/Remote/AbstractImapAdaptee.php';

/**
 * Test Mock class for Imapad Apaptee
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class SimpleImapAdaptee extends AbstractImapAdaptee {

    protected $isConnected = false;

    /**
     * @inheritdoc
     */
    protected function establishConnection($host, $port, $user, $password, $ssl = false)
    {
        $this->isConnected = true;

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function addFlagToMessage($flag, $id)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function removeFlagFromMessage($flag, $id)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function _selectFolder($path)
    {
        return $path;
    }

    /**
     * @inheritdoc
     */
    public function _getFolderDelimiter()
    {
        return '/';

    }

    /**
     * @inheritdoc
     */
    public function disconnect()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isConnected()
    {
        return $this->isConnected;
    }

    /**
     * @inheritdoc
     */
    protected function _getMessage($uId)
    {
        return array(
            'header' => "HEADER",
            'body'   => "BODY"
        );
    }

}