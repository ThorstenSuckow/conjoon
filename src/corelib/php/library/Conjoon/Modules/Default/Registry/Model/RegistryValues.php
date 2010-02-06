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
class Conjoon_Modules_Default_Registry_Model_RegistryValues extends Conjoon_Db_Table {

    /**
     * The name of the table in the underlying datastore this
     * class represents, without any prefix defined by Conjoon_Db_Table::setTablePrefix
     * @var string
     */
    protected $_name = 'registry_values';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = array('name', 'user_id');

    /**
     * Returns the registry values for a specific user. Registry values with
     * user-id "0" are treated as system settings and will not be returned if
     * another entry exists with the same name, but user-id set to $userId.
     * The array will be indexed be the values "registry_id" key.
     *
     * @param integer $userId the id of the user to fetch the registry values
     * for, or 0 to return system registry values.
     *
     * @return array
     */
    public function getRegistryValuesForUser($userId)
    {
        $userId = (int)$userId;

        if ($userId < 0) {
            return array();
        }

        $adapter = self::getDefaultAdapter();

        $select = $adapter->select()
                    ->from(
                        array('registry_values' => self::getTablePrefix() . 'registry_values'),
                        array('registry_id', 'name', 'value', 'type', 'is_editable')
                    )
                    ->where('registry_values.user_id='.$userId)
                    ->group('name')
                    ->order('name');

        if ($userId != 0) {
            $select = $select
                      ->join(
                          array('rv' => self::getTablePrefix() . 'registry_values'),
                          'rv.name!=registry_values.name',
                          array()
                      )
                      ->orWhere('rv.user_id = 0');
        }


        $rows = $adapter->fetchAll($select);

        $values = array();
        for ($i = 0, $len = count($rows); $i < $len; $i++) {
            if (!isset($values[$rows[$i]['registry_id']])) {
                $values[$rows[$i]['registry_id']] = array();
            }

            $values[$rows[$i]['registry_id']][] = array(
                'name'        => $rows[$i]['name'],
                'value'       => $rows[$i]['value'],
                'type'        => $rows[$i]['type'],
                'is_editable' => $rows[$i]['is_editable']
            );
        }

        return $values;
    }

}