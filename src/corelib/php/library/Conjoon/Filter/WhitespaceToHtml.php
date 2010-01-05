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
class Conjoon_Filter_WhitespaceToHtml implements Zend_Filter_Interface {


    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the text with whitespace-pairs replaced by a pair of
     * " &nbsp;".
     *
     * Will not replace whitespaces that are inside of tags, i.e.
     *
     * <div class="test">This  i<br />s   a test</div>
     * will become
     * <div class="test">This &nbsp;i<br />s &nbsp; a test</div>
     *
     * @param  mixed $value
     * @return string
     */
    public function filter($value)
    {
        // change all whitespacs to none breaking spaces, the first
        // one and every second one will be untouched so that browsers
        // are still able to do a line breaks if white-space-wrapping
        // is enabled
        return preg_replace(
            "/((<[^>]*)| *)/ie",
            '"\2"=="\1"? "\1" : str_replace("  ", " &nbsp;", "\1")',
            $value
        );
    }
}