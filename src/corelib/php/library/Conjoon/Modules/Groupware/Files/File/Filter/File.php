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
 * @see Conjoon_Filter_Input
 */
require_once 'Conjoon/Filter/Input.php';

/**
 * @see Conjoon_Filter_Raw
 */
require_once 'Conjoon/Filter/Raw.php';


/**
 * An input-filter class defining all validators and filters needed when
 * processing files.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Files_File_Filter_File extends Conjoon_Filter_Input {

    const CONTEXT_DOWNLOAD_REQUEST = 'download_request';

    protected $_presence = array(
        self::CONTEXT_DOWNLOAD_REQUEST => array(
            'id',
            'key',
            'downloadCookieName',
            'type',
            'name'
        )
    );

    protected $_filters = array(
        'id' => array(
            'Int'
        ),
         'key' => array(
            'StringTrim'
         ),
         'downloadCookieName' => array(
            'StringTrim'
         ),
         'type' => array(
            'StringTrim'
         ),
         'name' => array(
            'StringTrim',
            'Urldecode'
         )
    );

    protected $_validators = array(
        'id' => array(
            'allowEmpty' => false,
            'default'    => 0
         ),
         'key' => array(
            'allowEmpty' => false,
            array('StringLength', 32, 32)
         ),
         'downloadCookieName' => array(
            'allowEmpty' => false
         ),
         'type' => array(
            'allowEmpty' => false,
            array('InArray', array('file', 'emailAttachment'))
         ),
         'name' => array(
            'allowEmpty' => true
         )

    );

    protected function _init()
    {
        $this->_defaultEscapeFilter = new Conjoon_Filter_Raw();
    }



}