<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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