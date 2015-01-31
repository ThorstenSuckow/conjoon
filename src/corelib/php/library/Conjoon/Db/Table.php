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
 * @see Zend_Db_Table
 */
require_once 'Zend/Db/Table.php';

/**
 * Class for SQL table interface. Overrides Zend_Db_Table by allowing to set a
 * default prefix for tables that can be accessed in the concrete implementations.
 *
 * @category   Conjoon
 * @package    Conjoon_Db
 * @subpackage Table
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Db_Table extends Zend_Db_Table {

    /**
     * Default table prefix.
     *
     * @var String
     */
    protected static $_tblPrefix = "";

    /**
     * __construct() - For concrete implementation of Conjoon_Db_Table
     *
     * This constructor automatically prepends the prefix as defined using setTablePrefix()
     * to this table's name.
     *
     * @param string|array $config string can reference a Zend_Registry key for a db adapter
     *                             OR it can refernece the name of a table
     * @param unknown_type $definition
     */
    public function __construct($config = array(), $definition = null)
    {
        $this->_name = self::getTablePrefix() . $this->_name;

        parent::__construct($config, $definition);
    }

    /**
     * Sets the table prefix.
     *
     * @param String $prefix
     */
    public static function setTablePrefix($prefix)
    {
        self::$_tblPrefix = $prefix;
    }

    /**
     * Returns the table prefix.
     *
     * @return String
     */
    public static function getTablePrefix()
    {
        return self::$_tblPrefix;
    }

}