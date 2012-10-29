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
 * @see Conjoon_Text_Template_DefaultParseStrategy
 */
require_once 'Conjoon/Text/Template/DefaultParseStrategy.php';

/**
 * @see Conjoon_Argument_Check
 */
require_once 'Conjoon/Argument/Check.php';

/**
 * A simple Template class providing replacing placeholders with specific
 * values.
 *
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Template {

    const TEMPLATE_RESOURCE = 'template';

    const VARS = 'vars';

    const PARSE_STRATEGY = 'parseStrategy';

    protected $_vars = array();

    protected $_templateResource = null;

    /**
     * @type Conjoon_Text_ParseStrategy
     */
    protected $_parseStrategy = null;

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
                self::TEMPLATE_RESOURCE => array(
                    'allowEmpty' => false,
                    'type'       => 'instanceof',
                    'class'      => 'Conjoon_Text_Template_Resource'
                ),
                self::VARS => array(
                    'allowEmpty' => false,
                    'type'       => 'isset'
                )
            ), $config
        );

        if (isset($config[self::PARSE_STRATEGY])) {

            if (!($config[self::PARSE_STRATEGY] instanceof Conjoon_Text_ParseStrategy)) {

                /**
                 * @see Conjoon_Argument_Exception
                 */
                require_once 'Conjoon/Argument/Exception.php';

                throw new Conjoon_Argument_Exception(
                    'parseStrategy must be of type "Conjoon_Text_ParseStrategy"'
                );
            }

        }

        $this->_vars             = $config[self::VARS];
        $this->_templateResource = $config[self::TEMPLATE_RESOURCE];

        if (isset($config[self::PARSE_STRATEGY])) {
            $this->_parseStrategy = $config[self::PARSE_STRATEGY];
        } else {
            $this->_parseStrategy = $this->_getDefaultParseStrategy();
        }

    }

    public function getParsedTemplate()
    {
        return $this->_parseStrategy->parse($this->_templateResource, $this->_vars);
    }

    public function getVars()
    {
        return $this->_vars;
    }

    public function getTemplateResource()
    {
        return $this->_templateResource;
    }

    public function getParseStrategy()
    {
        return $this->_parseStrategy;
    }

    protected function _getDefaultParseStrategy()
    {
        return new Conjoon_Text_Template_DefaultParseStrategy();
    }

    public function __toString()
    {
        return $this->getParsedTemplate();
    }


}