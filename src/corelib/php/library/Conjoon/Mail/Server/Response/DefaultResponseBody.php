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


namespace Conjoon\Mail\Server\Response;

use Conjoon\Argument\ArgumentCheck;

/**
 * @see \Conjoon\Mail\Server\Response\ResponseBody
 */
require_once 'Conjoon/Mail/Server/Response/ResponseBody.php';

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * A default response implementation.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultResponseBody implements ResponseBody {

    /**
     * @var array
     */
    protected $data;

    /**
     * @inheritdoc
     */
    public function __construct(Array $data = array())
    {
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return $this->data;
    }

}