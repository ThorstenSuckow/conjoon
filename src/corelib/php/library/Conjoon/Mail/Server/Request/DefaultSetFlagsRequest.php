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


namespace Conjoon\Mail\Server\Request;

use Conjoon\Argument\ArgumentCheck;

/**
 * @see Conjoon\Mail\Server\Request\SetFlagsRequest
 */
require_once 'Conjoon/Mail/Server/Request/SetFlagsRequest.php';

/**
 * @see Conjoon\Mail\Server\Request\DefaultRequest
 */
require_once 'Conjoon/Mail/Server/Request/DefaultRequest.php';

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';


/**
 * A default implementation for a SetFlagsRequest.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @uses DefaultRequest
 * @uses SetFlagsRequest
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultSetFlagsRequest extends DefaultRequest implements SetFlagsRequest {

    /**
     * @var \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection
     */
    protected $folderFlagCollection;

    /**
     * Creates a new instance of this class.
     *
     * Additional to the parent's user config property, an instance of this
     * class needs to be configured with parameters holding a folderFlagCollection,
     * providing detailed information about the flags which have to be set for which
     * messages.
     *
     * @param Array $options An array of options this request gets configured
     *                       with.
     *                       - user: \Conjoon\User\User
     *                       - folderFlagCollection:
     *                         \Conjoon\Mail\Client\Message\Flag\FolderFlagCollection
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function __construct(Array $options)
    {
        parent::__construct($options);

        ArgumentCheck::check(array(
            'folderFlagCollection' => array(
                'type'  => 'instanceof',
                'class' => '\Conjoon\Mail\Client\Message\Flag\FolderFlagCollection'
            )
        ), $this->parameters);

        $this->folderFlagCollection = $this->parameters['folderFlagCollection'];
    }

    /**
     * @inheritoc
     */
    public function getFolderFlagCollection()
    {
        return $this->folderFlagCollection;
    }

    /**
     * @inheritdoc
     */
    public function getProtocolCommand()
    {
        return "setFlags";
    }

}