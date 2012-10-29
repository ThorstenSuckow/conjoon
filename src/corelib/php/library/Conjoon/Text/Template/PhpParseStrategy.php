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
 * @see Conjoon_Text_ParseStrategy
 */
require_once 'Conjoon/Text/ParseStrategy.php';

/**
 * @see Conjoon_Argument_Check
 */
require_once 'Conjoon/Argument/Check.php';


/**
 * Simple algorithm for replacing {TEXT}-like placeholders with specific values
 * in a given string.
 *
 * @category   Template
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Template_PhpParseStrategy
    implements Conjoon_Text_ParseStrategy {


    /**
     * @param Conjoon_Text_Template_Resource $resource
     * @param array $vars
     *
     * @throws Conjoon_Text_Template_Exception
     */
    public function parse(Conjoon_Text_Template_Resource $resource, Array $vars)
    {
        $old = array();

        foreach ($vars as $key => $value) {
            if (isset ($resource->$key)) {
                $old[$key] = $resource->$key;
            }

            $resource->$key = $value;
        }

        ob_start();

        $resource->getContent();

        $result = ob_get_clean();

        foreach ($vars as $key => $value) {
            if (isset($old[$key])) {
                $resource->$key = $old[$key];
            } else {
                unset($resource->$key);
            }
        }

        return $result;
    }

}