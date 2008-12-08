<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
 * @see Intrabuild_Dto
 */
require_once 'Intrabuild/Dto.php';

class Intrabuild_Modules_Groupware_Email_Folder_Dto extends Intrabuild_Dto {

    public $id;
    public $name;
    public $isChildAllowed;
    public $isLocked;
    public $type;
    public $childCount;
    public $pendingCount;

}