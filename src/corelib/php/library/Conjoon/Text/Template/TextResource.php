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
 *
 * @category   Template
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Template_TextResource implements Conjoon_Text_Template_Resource {

    protected $_text;

    public function __construct($text = "")
    {
        $this->_text = $text;
    }

    /**
     *
     *
     * @return string
     * @throws Conjoon_Text_Template_Exception
     */
    public function getContent()
    {
        return $this->_text;
    }
}