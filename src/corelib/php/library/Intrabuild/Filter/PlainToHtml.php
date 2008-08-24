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
class Intrabuild_Filter_PlainToHtml implements Zend_Filter_Interface {


    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the text which is special formatted with html tags replaced. The
     * following formats will be taken into account:
     *
     * _text_ will be replaced with <u>_text_</u>
     * /text/ will be replaced with <i>/text/</i>
     * *text* will be replaced with <b>*text*</b>
     *
     * @param  mixed $value
     * @return string
     */
    public function filter($value)
    {
        $value = preg_replace(
            array(
               //   1       2             3               4
                "/(^| )(\*[a-zA-Z])(.*?[a-zA-Z]\*|\*)(-|,|\.|:|;|\s)/im",
                "/(^| )(_[a-zA-Z])(.*?[a-zA-Z]_|_)(-|,|\.|:|;|\s)/im",
                "/(^| )(\/[a-zA-Z])(.*?[a-zA-Z]\/|\/)(-|,|\.|:|;|\s)/im",
            ),
            array (
                "\\1<b>\\2\\3</b>\\4",
                "\\1<u>\\2\\3</u>\\4",
                "\\1<i>\\2\\3</i>\\4"
            ),
            $value
        );

        return $value;
    }
}