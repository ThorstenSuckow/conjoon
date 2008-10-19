<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
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
 * @see Intrabuild_Filter_Input
 */
require_once 'Intrabuild/Filter/Input.php';

/**
 * @see Zend_Filter_HtmlEntities
 */
require_once 'Zend/Filter/HtmlEntities.php';


/**
 * A filter used for preparing data fetched from the database for sending as
 * a response to the client.
 *
 * @uses Intrabuild_Filter_Input
 * @package    Intrabuild_Modules_Groupware_Email
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Modules_Groupware_Email_Item_Filter_ItemResponse extends Intrabuild_Filter_Input {

    protected $_presence = array(
         self::CONTEXT_RESPONSE => array(
            'id',
            'recipients',
            'sender',
            'subject',
            'date',
            'isRead',
            'isAttachment',
            'isSpam',
            'isDraft',
            'isOutboxPending',
            'referencedAsTypes',
            'groupwareEmailFoldersId'
        )
    );

    protected function _init()
    {
        /**
         * @see Intrabuild_Modules_Groupware_Email_Item_Filter_ReferenceTypes
         */
        require_once 'Intrabuild/Modules/Groupware/Email/Item/Filter/ReferenceTypes.php';

        $this->_filters['referencedAsTypes'] = array(
            new Intrabuild_Modules_Groupware_Email_Item_Filter_ReferenceTypes()
        );

        $this->_defaultEscapeFilter = new Zend_Filter_HtmlEntities(ENT_COMPAT, 'UTF-8');
    }


}