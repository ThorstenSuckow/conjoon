<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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
 * @see Conjoon_Db_Table
 */
require_once 'Conjoon/Db/Table.php';

/**
 * Table data gateway. Models the table <tt>registry_values</tt>.
 *
 * @uses Conjoon_Db_Table
 * @package Conjoon
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Default_Registry_Model_Registry extends Conjoon_Db_Table {

    /**
     * The name of the table in the underlying datastore this
     * class represents, without any prefix defined by Conjoon_Db_Table::setTablePrefix
     * @var string
     */
    protected $_name = 'registry';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = 'id';

    /**
     * Returns the registry in an array with their values. The registry entry's
     * values will be available in the array index "values", which either is an
     * empty array if no values are available or an array indexed with the field
     * names of the registry_values table
     * (see Conjoon_Modules_Default_Registry_Model_RegistryValues).
     *
     * @param integer $userId the id of the user to fetch the registry entries
     * for, or 0 to return system registry settings.
     *
     * @return array
     */
    public function getRegistryForUser($userId)
    {
        $userId = (int)$userId;

        if ($userId < 0) {
            return array();
        }

        $select = $this->select()
                  ->from(array('registry' => self::getTablePrefix() . 'registry'))
                  ->order('key');

        $rows = $this->fetchAll($select)->toArray();

        /**
         * @see Conjoon_Modules_Default_Registry_Model_RegistryValues
         */
        require_once 'Conjoon/Modules/Default/Registry/Model/RegistryValues.php';

        $valuesModel = new Conjoon_Modules_Default_Registry_Model_RegistryValues();

        $values = $valuesModel->getRegistryValuesForUser($userId);

        for ($i = 0, $len = count($rows); $i < $len; $i++) {
            $rows[$i]['values'] = isset($values[$rows[$i]['id']])
                                  ? $values[$rows[$i]['id']]
                                  : array();
        }

        return $rows;
    }

}