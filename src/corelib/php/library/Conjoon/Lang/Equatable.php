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


namespace Conjoon\Lang;

/**
 * Interface all class loaders have to implement.
 *
 * @category   Conjoon_Lang
 * @package    Lang
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
interface Equatable  {

    /**
     * Checks whether some other object is equal to this one.
     *
     * This contract represents in general the the following  equivalence
     * relations on non-null objects:
     *
     * Reflexive: x.equals(x) must always be true
     * Symmetric: x.equals(y) is true, if and only if y.equals(x) is also true
     * Transitive: x.equals(y):true => y.equals(z):true => x.equals(z):true
     * Consistent: Multiple invocations of x.equals(y) consistently return true
     * or consistently return false, as long as no information on x or y are
     * modified.
     *
     * Null-behavior:
     * x.equals(null) always returns false.
     *
     * The most basic implementation for two objects x and y would be to check
     * for the same type and value using PHP's === comparison operator. x and
     * y are definitely equal if x === y evaluates to true.
     * The underlying API will most likely compare objects based on their values,
     * such as ids retrieved from DB-operations. The API is advised to implement
     * this contract appropriately.
     *
     * Implementing classes are advised to check for Equatable-type of the
     * passed object and return false if $obj is not equatable:
     *
     * Example:
     * x.equals(y) should be false if the following condition is satisfied:
     * (y instanceof \Conjoon\Lang\Equatable) === false
     *
     * @param $obj The reference object with which to check for equality.
     *
     * @return boolean true if $obj is equal to this object.
     */
    public function equals($obj);

}