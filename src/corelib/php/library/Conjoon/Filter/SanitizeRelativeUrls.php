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
 * Replaces relative links in tags by prepending the given
 * string.
 *
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_SanitizeRelativeUrls implements Zend_Filter_Interface
{
    protected $_link = "";

    protected $_valid = array();

    /**
     * Constructor.
     *
     * @param string $link The strink to prepend to relative urls
     * @param array $valid An array of strings which should not get prepended
     * with $link if found.
     *
     */
    public function __construct($link, Array $valid = array())
    {
        $this->_link  = $link;
        $this->_valid = $valid;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        $link = rtrim($this->_link, '/').'/';

        $valid = implode('|', $this->_valid);

        $value = preg_replace(
            array(
                ',<a([^>]+)href="(?!https?://|ftp://|mailto:|news:'.($valid ? '|' . $valid : '').')([^>"\s]+)",i',
                ',<img([^>]+)src="(?!https?://|ftp://|mailto:|news:'.($valid ? '|' . $valid : '').')([^>"\s]+)",i',
                ',<a([^>]+)href=\'(?!https?://|ftp://|mailto:|news:'.($valid ? '|' . $valid : '').')([^>\'\s]+)\',i',
                ',<img([^>]+)src=\'(?!https?://|ftp://|mailto:|news:'.($valid ? '|' . $valid : '').')([^>\'\s]+)\',i'
            ),
            array(
                '<a\1href="'.$link.'\2"',
                '<img\1src="'.$link.'\2"',
                '<a\1href=\''.$link.'\2\'',
                '<img\1src=\''.$link.'\2\''
            ),
            $value
        );

        return $value;
    }
}
