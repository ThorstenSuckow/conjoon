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
