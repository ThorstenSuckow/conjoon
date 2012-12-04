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

use Conjoon\Argument\ArgumentCheck,
    Conjoon\Argument\InvalidArgumentException;

/**
 * @see Conjoon\Mail\Server\Request\Request
 */
require_once 'Conjoon/Mail/Server/Request/Request.php';

/**
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * @see Conjoon\Argument\InvalidArgumentException
 */
require_once 'Conjoon/Argument/InvalidArgumentException.php';

/**
 * A default request implementation.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class DefaultRequest implements Request {

    /**
     * @var \Conjoon\User\User
     */
    protected $user;

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * - user: Conjoon_User_User
     *
     * @param Array $options An array with options this request should be
     *                       configured with:
     *                       - user: \Conjoon\User\User
     *
     * @throws Conjoon\Argument\InvalidArgumentException
     */
    public function __construct(Array $options)
    {
        $data = array('options' =>  $options);

        ArgumentCheck::check(array(
            'options' => array(
                'type'       => 'array',
                'allowEmpty' => false
            )
        ), $data);

        ArgumentCheck::check(array(
            'user' => array(
                'type'  => 'instanceof',
                'class' => '\Conjoon\User\User'
            )
        ), $options);

        if (isset($options['parameters'])) {
            if (!is_array($options['parameters'])) {
                throw new InvalidArgumentException(
                    "\"parameters\" must be of type array"
                );
            }

            $this->parameters = $options['parameters'];
        }

        $this->user = $options['user'];
    }

    /**
     * @inheritdoc
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @inheritdoc
     */
    public function getUser()
    {
        return $this->user;
    }

}