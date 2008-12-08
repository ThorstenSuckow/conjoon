<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Address
 */
require_once 'Conjoon/Modules/Groupware/Email/Address.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Address_List
 */
require_once 'Conjoon/Modules/Groupware/Email/Address/List.php';

/**
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_Address_Filter_EmailRecipientsToAddressList implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     *
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        $value = (array)$value;

        $addr = array();
        foreach ($value as $address) {
            $addr[] = new Conjoon_Modules_Groupware_Email_Address($address);
        }

        return new Conjoon_Modules_Groupware_Email_Address_List($addr);
    }


}
