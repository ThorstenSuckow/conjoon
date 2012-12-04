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