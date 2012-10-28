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
class Conjoon_Filter_PlainToHtml implements Zend_Filter_Interface {


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
     * Whitespaces will not be replaced with &nbsp;, as this would blow up
     * traffic by a factor of 6. Instead, if you are looking to replace
     * whitespaces properly, use Conjoon_Filter_WhitespaceToHtml.
     *
     * @param  mixed $value
     * @return string
     */
    public function filter($value)
    {
        $value = preg_replace(
            array(
               //   1       2             3               4
                "/(^|\(| )(\*[a-zA-Z])(.*?[a-zA-Z]\*|\*)(\)|-|,|\.|:|;|\s)/im",
                "/(^|\(| )(_[a-zA-Z])(.*?[a-zA-Z]_|_)(\)|-|,|\.|:|;|\s)/im",
                "/(^|\(| )(\/[a-zA-Z])(.*?[a-zA-Z]\/|\/)(\)|-|,|\.|:|;|\s)/im",
            ),
            array (
                "\\1<b>\\2\\3</b>\\4",
                "\\1<u>\\2\\3</u>\\4",
                "\\1<i>\\2\\3</i>\\4"
            ),
            $value
        );

        return str_replace(
            array("\t", "\n"),
            array("    ", "<br />"),
            rtrim($value)
        );
    }
}