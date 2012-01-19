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
 * @see Conjoon_Filter_Input
 */
require_once 'Conjoon/Filter/Input.php';

/**
 * @see Zend_Filter_HtmlEntities
 */
require_once 'Zend/Filter/HtmlEntities.php';


/**
 * A filter used for preparing data fetched from the database for sending as
 * a response to the client.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Modules_Groupware_Email
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_Item_Filter_ItemResponse extends Conjoon_Filter_Input {

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

    protected $_filters = array(
         'date' => array(
            'DateUtcToLocal'
         )
    );



    protected function _init()
    {
        /**
         * @see Conjoon_Modules_Groupware_Email_Item_Filter_ReferenceTypes
         */
        require_once 'Conjoon/Modules/Groupware/Email/Item/Filter/ReferenceTypes.php';

        $this->_filters['referencedAsTypes'] = array(
            new Conjoon_Modules_Groupware_Email_Item_Filter_ReferenceTypes()
        );

        $this->_defaultEscapeFilter = new Zend_Filter_HtmlEntities(
            array(
                'quotestyle' => ENT_COMPAT,
                'charset'    => 'UTF-8'
            )
        );
    }


}