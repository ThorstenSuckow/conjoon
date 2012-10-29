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
 * @see Conjoon_Text_Template_PhpParseStrategy
 */
require_once 'Conjoon/Text/Template/PhpParseStrategy.php';

/**
 * @see Conjoon_Text_Template_PhpFileResource
 */
require_once 'Conjoon/Text/Template/PhpFileResource.php';

/**
 * @see Conjoon_Text_Template
 */
require_once 'Conjoon/Text/Template.php';



/**
 *
 *
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_PhpTemplate extends Conjoon_Text_Template {

    const PATH = 'path';

    /**
     *
     * @param array $config
     *
     * @throws Conjoon_Argument_Exception
     */
    public function __construct(Array $config = array())
    {
        Conjoon_Argument_Check::check(
            array(
                self::PATH => array(
                    'allowEmpty' => false,
                    'type'       => 'string'
                ),
                self::VARS => array(
                    'allowEmpty' => false,
                    'type'       => 'isset'
                )
            ), $config
        );

        $config[self::TEMPLATE_RESOURCE] =
            new Conjoon_Text_Template_PhpFileResource(
            $config[self::PATH]
        );

        $config[self::PARSE_STRATEGY] =
            new Conjoon_Text_Template_PhpParseStrategy();

        parent::__construct($config);
    }

    protected function _getDefaultParseStrategy()
    {
        return new Conjoon_Text_Template_PhpParseStrategy();
    }

}