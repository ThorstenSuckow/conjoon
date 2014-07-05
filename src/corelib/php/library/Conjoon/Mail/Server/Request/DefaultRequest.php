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
    public function getParameter($key)
    {
        if (!array_key_exists($key, $this->parameters)) {
            return null;
        }

        return $this->parameters[$key];
    }

    /**
     * @inheritdoc
     */
    public function getUser()
    {
        return $this->user;
    }

}