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
abstract class DefaultResponseBody implements ResponseBody {

    /**
     * @var string
     */
    protected $text;

    /**
     * @inheritdoc
     */
    public function __construct($text = "")
    {
        $data = array('text' => $text);

        ArgumentCheck::check(array(
            'text' => array(
                'type'       => 'string',
                'allowEmpty' => true
            )
        ), $data);

        $text = $data['text'];

        $this->text = $text;

    }

    /**
     * @inheritdoc
     */
    public function getText()
    {
        return $this->text;
    }

}