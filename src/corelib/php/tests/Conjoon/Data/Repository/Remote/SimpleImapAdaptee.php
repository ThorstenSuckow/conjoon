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



}