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
 * @see Conjoon_Text_Template_Resource
 */
require_once 'Conjoon/Text/Template/Resource.php';

/**
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface Conjoon_Text_ParseStrategy {


    /**
     * Parses the given text and replaces placeholders identified by the
     * associative array#s keys with the specific values.
     *
     * @param Conjoon_Text_Template_Resource $resource
     * @param array $vars
     *
     * @throws Conjoon_Text_Template_Exception
     */
    public function parse(Conjoon_Text_Template_Resource $resource, Array $vars);

}