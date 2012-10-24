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