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
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';


/**
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Filter_NormalizeLineFeeds implements Zend_Filter_Interface
{

    /**
     * Normalizes the line feeds of a text, replacing them with "\n"
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        // normalize line ends
        return preg_replace("/(\r\n|\r|\n)/", "\n", $value);
    }
}