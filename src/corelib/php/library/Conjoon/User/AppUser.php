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

namespace Conjoon\User;

use Conjoon\Argument\ArgumentCheck;

/**
 * @see Conjoon_User_User
 */
require_once 'Conjoon/User/DefaultUser.php';

/**
 * An implementation of Conjoon_User_User.
 *
 * @category   Conjoon
 * @package    Conjoon_User
 * @subpackage User
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

class AppUser extends DefaultUser {

    /**
     * @inheritdoc
     */
    public function __construct($options)
    {
        /**
         * @see \Conjoon\Argument\Check
         */
        require_once 'Conjoon/Argument/ArgumentCheck.php';

        $data = array('user' => $options);

        ArgumentCheck::check(array(
            'user' => array(
                'type'  => 'instanceof',
                'class' => 'Conjoon_Modules_Default_User'
            )
        ), $data);

        $options = $data['user'];

        if ($options->getId() == ""
            || $options->getFirstName()  == ""
            || $options->getLastName() == ""
            || $options->getUsername() == ""
            || $options->getEmailAddress() == "") {
            /**
             * @see Conjoon\User\UserException
             */
            require_once 'Conjoon/User/UserException.php';

            throw new UserException(
                "Cannot use instance of Conjoon_Modules_Default_User - "
                . "object data is not valid"
            );
        }

        $this->_id           = (string) $options->getId();
        $this->_firstName    = (string) $options->getFirstName();
        $this->_lastName     = (string) $options->getLastName();
        $this->_userName     = (string) $options->getUsername();
        $this->_emailAddress = (string) $options->getEmailAddress();
    }
}