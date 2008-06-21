<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
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