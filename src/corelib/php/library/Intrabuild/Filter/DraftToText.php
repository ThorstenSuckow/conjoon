<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: SortDirection.php 2 2008-06-21 10:38:49Z T. Suckow $
 * $Date: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $Revision: 2 $
 * $LastChangedDate: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/php/library/Intrabuild/Filter/SortDirection.php $
 */

/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';


/**
 * @category   Filter
 * @package    Intrabuild_Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Filter_DraftToText implements Zend_Filter_Interface
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

        // ...and convert all html entities back!
        $value = htmlspecialchars_decode($value);

        return $value;
    }
}
