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
 * @see Conjoon_Dto
 */
require_once 'Conjoon/Dto.php';

class Conjoon_Modules_Groupware_Email_Attachment_Dto extends Conjoon_Dto {

    public $id;
    public $fileName;
    public $mimeType;

}