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
            'to',
            'cc',
            'from',
            'subject',
            'date',
            'isRead',
            'isAttachment',
            'isSpam',
            'isDraft',
            'groupwareEmailFoldersId'
        )
    );

    protected $_filters = array(
        'from' => array(
            'EmailRecipients'
        )
    );

    protected function _init()
    {
        $this->_defaultEscapeFilter = new Zend_Filter_HtmlEntities(ENT_COMPAT, 'UTF-8');
    }

    public function getProcessedData()
    {
        $data = parent::getProcessedData();

        $data['from'] = isset($data['from'][0][1]) ? $data['from'][0][1] : $data['from'][0][0];

        return $data;
    }



}