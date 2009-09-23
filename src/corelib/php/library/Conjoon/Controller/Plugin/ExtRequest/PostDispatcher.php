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
 * @see Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 *
 * @uses Zend_Controller_Plugin_Abstract
 * @package Conjoon_Controller
 * @subpackage Plugin
 * @category Plugins
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Controller_Plugin_ExtDirect_PostDispatcher extends Zend_Controller_Plugin_Abstract {

    /**
     * @var Conjoon_Controller_Plugin_ExtRequest $_extDirect
     */
    protected $_extDirect = null;

    /**
     * Constructor.
     *
     *
     * @param Conjoon_Controller_Plugin_ExtRequest $extDirect
     */
    public function __construct(Conjoon_Controller_Plugin_ExtRequest $extDirect)
    {
        $this->_extDirect = $extDirect;
    }

    /**
     * Called when the fonts dispatchLoop shuts down.
     *
     * @return void
     */
    public function dispatchLoopShutdown()
    {
        $this->_extDirect->notifyDispatchLoopShutdown();
    }

}