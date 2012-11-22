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

/**
 * @see Conjoon_User_User
 */
require_once 'Conjoon/User/User.php';

/**
 * An implementation of Conjoon_User_User.
 *
 * @category   Conjoon
 * @package    Conjoon_User
 * @subpackage User
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

class Conjoon_User_AppUser extends Conjoon_User_User {

    /**
     * @inheritdoc
     */
    public function __construct($options)
    {
        /**
         * @see Conjoon_Argument_Check
         */
        require_once 'Conjoon/Argument/Check.php';

        $data = array('user' => $options);

        Conjoon_Argument_Check::check(array(
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
             * @see Conjoon_User_UserException
             */
            require_once 'Conjoon/User/UserException.php';

            throw new Conjoon_User_UserException(
                "Cannot use instance of Conjoon_Modules_Default_User - "
                . "object data is not valid"
            );
        }

        $this->_id           = (string) $options->getId();
        $this->_firstName    = (string) $options->getFirstName();
        $this->_lastName     = (string) $options->getLastName();
        $this->_username     = (string) $options->getUsername();
        $this->_emailAddress = (string) $options->getEmailAddress();
    }
}