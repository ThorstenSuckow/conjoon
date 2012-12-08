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


namespace Conjoon\Data\Repository;

/**
 * Interface all RemoteRepositories have to implement.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface RemoteRepository extends DataRepository {


    /**
     * Returns the connection object used for this remote repository.
     *
     * @param array $options an array of configuration options the
     *              connection might be configured with if no
     *              connection is available yet.
     *
     * @return \Conjoon\Data\Repository\Remote\RemoteConnection
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function getConnection(array $options = array());


}