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
 * @see Conjoon_Filter_Input
 */
require_once 'Conjoon/Filter/Input.php';

/**
 * @see Zend_Filter_Htmlentities
 */
require_once 'Zend/Filter/Htmlentities.php';


/**
 * An input-filter class defining all validators and filters needed when
 * processing input data for mutating or creating data in the attachment-table.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_Attachment_Filter_AttachmentResponse extends Conjoon_Filter_Input {

    protected $_presence = array(
         self::CONTEXT_RESPONSE => array(
            'id',
            'mimeType',
            'fileName'
         )
    );



    protected function _init()
    {
        $this->_defaultEscapeFilter = new Zend_Filter_Htmlentities(ENT_COMPAT, 'UTF-8');
    }



}