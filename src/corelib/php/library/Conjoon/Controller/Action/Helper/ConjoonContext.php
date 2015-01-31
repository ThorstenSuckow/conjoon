<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
 * @see Zend_Registry
 */
require_once 'Zend/Registry.php';

/**
 * @see Conjoon_Keys
 */
require_once 'Conjoon/Keys.php';

/**
 * Simplify IPhone context switching based on HTTP_USER_AGENT
 *
 * This plugin works together with the ExtDirectRequest plugin which automates processing
 * of merged requests.
 * You are strongly advised never to use the baseclass of this helper together with this
 * helper, since the ContextSwitch attaches silently "context" properties to the given
 * ActionController without further identifying which helper set this property.
 *
 * @uses       Zend_Controller_Action_Helper_Abstract
 * @category   Conjoon
 * @package    Conjoon_Controller
 * @subpackage Conjoon_Controller_Action_Helper
 */
class Conjoon_Controller_Action_Helper_ConjoonContext extends Zend_Controller_Action_Helper_ContextSwitch
{
    /**
     * @var Conjoon_Controller_Plugin_ExtRequest $_extRequest
     */
    protected $_extRequest = null;

    /**
     * Constructor
     *
     * Add HTML context
     *
     * @return void
     */
    public function __construct()
    {
        try {
            $this->_extRequest = Zend_Registry::get(Conjoon_Keys::EXT_REQUEST_OBJECT);
        } catch (Zend_Exception $e) {
            $this->_extRequest = null;
        }

        parent::__construct();
        $this->addContext('iphone', array('suffix' => 'iphone'));

        // this context will json enocde the view variables, but send the generated
        // string as text/html instead of application/json
        $this->addContext('jsonHtml', array(
            'suffix'    => 'jsonHtml',
            'headers'   => array('Content-Type' => 'text/html'),
            'callbacks' => array(
                'init' => 'initJsonContext',
                'post' => 'postJsonContext'
            )
        ));
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


    /**
     * Processes view variables before the parent implementation serializes
     * to JSON.
     *
     * @return void
     */
    public function postJsonContext()
    {
        if (!$this->_extRequest || !$this->getAutoJsonSerialization()) {
            return parent::postJsonContext();
        }

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $view = $viewRenderer->view;
        if (!($view instanceof Zend_View_Interface)) {
            return parent::postJsonContext();
        }


        $request      = $this->getRequest();
        $params       = $request->getParams();
        $extParameter = $this->_extRequest->getConfigValue('extParameter');

        if (isset($params[$extParameter])) {
            $params = $params[$extParameter];

            $vars = $view->getVars();
            $view->clearVars();

            if (is_array($params)) {
                if (isset($params['action'])) {
                    $view->action = $params['action'];
                }
                if (isset($params['method'])) {
                    $view->method = $params['method'];
                }
                if (isset($params['tid'])) {
                    $view->tid = $params['tid'];
                }

                if ($this->getResponse()->isException()) {
                    $view->type    = 'exception';
                    $view->message = $vars;

                } else {
                    if (isset($params['type'])) {
                        $view->type = $params['type'];
                    }

                    $view->result = $vars;
                }

            } else {
                // can only be an exception then
                if ($this->getResponse()->isException()) {
                    $view->type    = 'exception';
                    $view->message = $vars;
                } else {
                    $view->result = $vars;
                }
            }

        }

        parent::postJsonContext();
    }

}
