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
 * @see Zend_Cache
 */
require_once 'Zend/Cache.php';

/**
 * @see Conjoon_Builder
 */
require_once 'Conjoon/Builder.php';


class Conjoon_Modules_Groupware_Email_Folder_FolderRootTypeBuilder extends Conjoon_Builder {

    protected $_validGetOptions = array('folderId');

    protected $_noBuildClassNeeded = true;

    /**
     *
     * @param array $options An associative array with the following
     * key value/pairs:
     *   - folderId: The id of the folder
     */
    protected function _buildId(Array $options)
    {
        return '' . $options['folderId'];
    }

    /**
     * @return null
     */
    protected function _getModel()
    {
        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Model_Folder
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/Folder.php';

        return new Conjoon_Modules_Groupware_Email_Folder_Model_Folder;
    }

    /**
     *
     * @param array $options An associative array with the following
     * key value/pairs:
     *   - folderId: The id of the folder
     *
     * @param Conjoon_BeanContext_Decoratable $model
     *
     * @return String
     */
    protected function _build(Array $options, Conjoon_BeanContext_Decoratable $model)
    {
        $folderId = $options['folderId'];

        $type = $model->getRootTypeForFolderId($folderId);

        if (!$type) {
            return null;
        }

        return $type;
    }

}