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
 * Patch notes to display in wizard
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

$PATCH_NOTES['0.1.5RC2'] = array(
    'headline'    => 'Patch introduced in 0.1.5RC2',
    'title'       => 'Patch for creating hashes out of Feed Item Guids and update '
                     . 'the groupware_feeds_items_flags table accordingly.',
    'description' => 'This patch will md5 all guid values of the field '
                     . 'groupware_feeds_items_flags.guid. This patch is required '
                     . 'in order to keep existing data consistent with data '
                     . 'written to the data storage from future versions of conjoon.',
    'link'        => array(
        "http://conjoon.org/issues/browse/CN-618"
    ),
    'warning' => 'Please be aware that this patch should be applied before you '
                 . 'update to any newer version of the conjoon 0.1.5 branch!'

);
