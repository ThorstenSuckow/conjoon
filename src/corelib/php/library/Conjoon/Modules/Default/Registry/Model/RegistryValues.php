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

        /**
         * @todo put logic in one query
         */

        $select1 = $adapter->select()
                    ->from(
                        array('registry_values' => self::getTablePrefix() . 'registry_values'),
                        array('registry_id', 'name', 'value', 'type', 'is_editable')
                    )
                    ->where('registry_values.user_id=0')
                    ->order('name');

        if ($userId != 0) {
            $select2 = $adapter->select()
                        ->from(
                            array('registry_values' => self::getTablePrefix() . 'registry_values'),
                            array('registry_id', 'name', 'value', 'type', 'is_editable')
                        )
                        ->where('registry_values.user_id='.$userId)
                        ->group('name')
                        ->order('name');
        }

        $rows2 = array();
        $rows1 = $adapter->fetchAll($select1);

        if ($userId != 0) {
            $rows2 = $adapter->fetchAll($select2);
        }

        $values = array();
        $chk    = array();
        for ($i = 0, $len = count($rows1); $i < $len; $i++) {
            if (!isset($values[$rows1[$i]['registry_id']])) {
                $values[$rows1[$i]['registry_id']] = array();
            }

            $u = count($values[$rows1[$i]['registry_id']]);

            if (!isset($chk[$rows1[$i]['registry_id']])) {
                $chk[$rows1[$i]['registry_id']] = array();
            }

            $chk[$rows1[$i]['registry_id']][$rows1[$i]['name']] = $u;

            $values[$rows1[$i]['registry_id']][$u] = array(
                'name'        => $rows1[$i]['name'],
                'value'       => $rows1[$i]['value'],
                'type'        => $rows1[$i]['type'],
                'is_editable' => $rows1[$i]['is_editable']
            );
        }

        if ($userId != 0) {
            for ($i = 0, $len = count($rows2); $i < $len; $i++) {

                if (isset($chk[$rows2[$i]['registry_id']][$rows2[$i]['name']])) {
                    $u = $chk[$rows2[$i]['registry_id']][$rows2[$i]['name']];
                    $values[$rows2[$i]['registry_id']][$u]['value'] = $rows2[$i]['value'];
                } else {
                    $values[$rows2[$i]['registry_id']][] = array(
                        'name'        => $rows2[$i]['name'],
                        'value'       => $rows2[$i]['value'],
                        'type'        => $rows2[$i]['type'],
                        'is_editable' => $rows2[$i]['is_editable']
                    );
                }
            }
        }

        return $values;
    }

    /**
     * Updates the given value name for the specified registry id for tje
     * specified user with the specified value.
     * Note: no casting for values will happen for the specified $type param, so
     * make sure casting happened before.
     *
     * @param integer $registryId
     * @param string  $valueName
     * @param mixed   $value
     * @param string  $type
     * @param integer $userId
     *
     * @return boolean true if the value was updated, otherwise false
     *
     * @throws InvalidArgumentException If any of the passed arguments are invalid
     */
    public function updateValueForUser($registryId, $valueName, $value, $type, $userId)
    {
        $userId = (int)$userId;
        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for userId: $userId"
            );
        }
        $registryId = (int)$registryId;
        if ($registryId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for registryId: $registryId"
            );
        }
        $valueName = trim((string)$valueName);
        if ($valueName == "") {
            throw new InvalidArgumentException(
                "Invalid argument supplied for valueName - valueName was empty"
            );
        }
        $type = strtoupper(trim((string)$type));
        if ($type != "BOOLEAN" && $type != "STRING" && $type != "INTEGER"
            && $type != "FLOAT") {
            throw new InvalidArgumentException(
                "Invalid argument supplied for type: $type"
            );
        }



        $where = "registry_id = $registryId AND user_id = $userId AND name ='$valueName'";
        $this->delete($where);

        $succ = $this->insert(array(
            'registry_id' => $registryId,
            'user_id'     => $userId,
            'name'        => $valueName,
            'type'        => $type,
            'value'       => $value

        ));

        if (!$succ) {
            return false;
        }

        return true;
    }

}