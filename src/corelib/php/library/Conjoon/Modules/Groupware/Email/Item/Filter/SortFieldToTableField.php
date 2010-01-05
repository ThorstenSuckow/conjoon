<?php
/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_Item_Filter_SortFieldToTableField implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the appropriate table's field name based on the passed sort field's
     * name.
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        switch (trim(strtolower($value))) {
            case 'id':
                return 'id';
            case 'isattachment':
                return 'is_attachment';
            case 'recipients':
                return 'recipients';
            case 'sender':
                return 'sender';
            case 'isread':
                return 'is_read';
            case 'cc':
                return 'cc';
            case 'date':
                return 'date';
            case 'to':
                return 'to';
            case 'to':
                return 'to';
            case 'subject':
                return 'subject';
            case 'from':
                return 'from';
            case 'isspam':
                return 'is_spam';
            default:
                return 'subject';
        }
    }
}
