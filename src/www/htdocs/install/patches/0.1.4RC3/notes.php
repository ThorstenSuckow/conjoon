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
 * Patch notes to display in wizard
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
 
$PATCH_NOTES['0.1.4RC3'] = array(
    'headline'    => 'Patch introduced in 0.1.4RC3',
    'title'       => 'Patch for converting existing date time values to the UTC timezone',
    'description' => 'This patch will convert existing date time values in the email/feed items '
                     .'table to the UTC timezone. This patch is required in order to keep existing '
                     .'data consistent with data written to the data storage from future versions '
                     .'of conjoon.',
    'link'        => array(
        'http://conjoon.org/issues/browse/CN-396',
        'http://conjoon.org/issues/browse/CN-405'
    )
    
);
 