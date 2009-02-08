<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
 * @see Zend_Controller_Action_Helper_ContextSwitch
 */
require_once 'Zend/Controller/Action/Helper/ContextSwitch.php';

/**
 * Simplify IPhone context switching based on HTTP_USER_AGENT
 *
 * @uses       Zend_Controller_Action_Helper_Abstract
 * @category   Conjoon
 * @package    Conjoon_Controller
 * @subpackage Conjoon_Controller_Action_Helper
 */
class Conjoon_Controller_Action_Helper_ConjoonContext extends Zend_Controller_Action_Helper_ContextSwitch
{
    /**
     * Constructor
     *
     * Add HTML context
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->addContext('iphone', array('suffix' => 'iphone'));
    }

    /**
     * Initialize Iphone context switching
     *
     * Checks if HTTP_USER_AGENT contains "iphone" or "ipod". if detected,
     * attempts to perform context switch.
     *
     * @param  string $format
     * @return void
     */
    public function initContext($format = null)
    {
        // give paret's implementation presedence, in case format
        // parameter was passed
        parent::initContext($format);

        // context found, skip iphone detection
        if ($this->_currentContext != null) {
            return;
        }

        $ipod   = strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'ipod');
        $iphone = strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'iphone');

        if ($ipod === false && $iphone === false) {
            // nope, no iphone
            return;
        }

        $suffix = $this->getSuffix('iphone');
        $this->_getViewRenderer()->setViewSuffix($suffix);

        $this->_currentContext = 'iphone';
    }
}
