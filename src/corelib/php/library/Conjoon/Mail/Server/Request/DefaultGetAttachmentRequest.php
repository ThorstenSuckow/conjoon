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
 * @see Conjoon\Mail\Server\Request\GetAttachmentRequest
 */
require_once 'Conjoon/Mail/Server/Request/GetAttachmentRequest.php';

/**
 * @see Conjoon\Mail\Server\Request\DefaultRequest
 */
require_once 'Conjoon/Mail/Server/Request/DefaultRequest.php';

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';


/**
 * A default implementation for a GetAttachmentRequest.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @uses DefaultRequest
 * @uses SetFlagsRequest
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultGetAttachmentRequest extends DefaultRequest implements GetAttachmentRequest {

    /**
     * Creates a new instance of this class.
     *
     * Additional to the parent's user config property, an instance of this
     * class needs to be configured with parameters holding an attachme location,
     * providing detailed information about the location of the attachment that
     * is requested.
     *
     * @param Array $options An array of options this request gets configured
     *                       with.
     *                       - user: \Conjoon\User\User
     *                       - attachmentLocation:
     *                         \Conjoon\Mail\Client\Message\AttachmentLocation
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function __construct(Array $options)
    {
        parent::__construct($options);

        ArgumentCheck::check(array(
            'attachmentLocation' => array(
                'type'  => 'instanceof',
                'class' => '\Conjoon\Mail\Client\Message\AttachmentLocation'
            )
        ), $this->parameters);

    }

    /**
     * @inheritdoc
     */
    public function getProtocolCommand()
    {
        return "getAttachment";
    }

}