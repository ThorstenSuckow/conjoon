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
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';


/**
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_DraftToText implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the input text formatted, suited for a text plain message
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        // first off, replace all <br> with line breaks
        $value = str_replace(
            array('<br>', '<br/>', '<br />', '<BR>', '<BR/>', '<BR />'),
            "\n",
            $value
        );

        // now strip all tags!
        $value = strip_tags($value);

        // ...and convert all special html entities back!
        return htmlspecialchars_decode($value);
    }
}
