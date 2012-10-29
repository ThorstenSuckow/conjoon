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
class Conjoon_Text_Template_PhpFileResource implements Conjoon_Text_Template_Resource {

    protected $_path;

    public function __construct($path)
    {
        $this->_path = $path;
    }

    /**
     *
     *
     * @return string
     * @throws Conjoon_Text_Template_Exception
     */
    public function getContent()
    {
        if (!file_exists($this->_path)) {
            /**
             * @see Conjoon_Text_Template_Exception
             */
            require_once 'Conjoon/Text/Template/Exception.php';

            throw new Conjoon_Text_Template_Exception(
                "\"" . $this->_path ."\" does not exist"
            );
        }

        include $this->_path;
    }
}