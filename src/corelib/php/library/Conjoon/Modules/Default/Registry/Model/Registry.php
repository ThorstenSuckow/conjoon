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
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
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