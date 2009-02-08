<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
 * licensing@conjoon.org
 *
 * $Author: T. Suckow $
 * $Id: Error.php 324 2008-12-08 21:26:43Z T. Suckow $
 * $Date: 2008-12-08 22:26:43 +0100 (Mo, 08 Dez 2008) $
 * $Revision: 324 $
 * $LastChangedDate: 2008-12-08 22:26:43 +0100 (Mo, 08 Dez 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/live/vs170240.vserver.de/svn_repository/conjoon/trunk/src/corelib/php/library/Conjoon/Error.php $
 */

/**
 * @see Conjoon_Error
 */
require_once 'Conjoon/Error.php';


/**
 * A static class for creating Conjoon_Error-objects.
 *
 * @package    Conjoon
 * @subpackage Error
 * @category   Error
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Error_Factory {




    /**
     * Creates a Conjoon_Error object based on the passed arguments and
     * return the error object.
     *
     * @param string $message
     * @param string $level
     * @param string $code
     * @param string $file
     * @param string $line
     *
     * @return Conjoon_Error
     */
    public static function createError($message = "[no message]", $level = null,
        $type = null, $code = null, $file = null, $line = null)
    {
        if ($level === null) {
            $level = Conjoon_Error::LEVEL_ERROR;
        }

        if ($type === null) {
            $type = Conjoon_Error::UNKNOWN;
        }

        $error = new Conjoon_Error();
        $error->setMessage($message);
        $error->setLevel($level);
        $error->setType($type);
        $error->setCode($code);
        $error->setFile($file);
        $error->setLine($line);

        return $error;
    }
}