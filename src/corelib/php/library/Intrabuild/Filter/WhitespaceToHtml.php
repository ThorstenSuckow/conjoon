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
class Intrabuild_Filter_WhitespaceToHtml implements Zend_Filter_Interface {


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