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
 * @see Conjoon_Dto
 */
require_once 'Conjoon/Dto.php';

class Conjoon_Modules_Groupware_Email_Folder_Dto extends Conjoon_Dto {

    public $id;
    public $idForPath;
    public $name;
    public $isChildAllowed;
    public $isLocked;
    public $type;
    public $childCount;
    public $pendingCount;
    public $isSelectable;

}