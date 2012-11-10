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

$PATCH_NOTES['0.1.4RC5'] = array(
    'headline'    => 'Patch introduced in 0.1.4RC5',
    'title'       => 'Patch for converting multibyte characters to proper UTF-8
                     characters.',
    'description' => 'This patch will convert multibyte characters to proper
                      UTF-8 characters. This patch is required in order to keep
                      existing data consistent with data written to the data
                      storage from future versions of conjoon.',
    'link'        => array(

    ),
    'warning' => 'Please be aware that later applying this patch than the version '
                 .'it was intended for might lead to data inconsistency.'

);
