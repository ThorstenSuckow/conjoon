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
 * The DispatchHelper's main purpose is to catch any exception that was thrown by the
 * FrontController directly and transform this exception (along with any other exceptions
 * that were found in the front controller's current response to Json data.
 * It does also provide convinient methods for merging exceptions for the Ext.Direct-API,
 * based on the availability of the ExtRequest-Plugin.
 * ErrorControllers are advised to utilize the "transformExceptions" method to apply
 * a proper json datastructure for the client.
 *
 * @package Conjoon_Controller
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Controller_DispatchHelper {

    /**
     * Constructor.
     *
     * Private to enforce static behavior.
     *
     */
    private function __construct()
    {

    }

    private function __clone()
    {

    }

    /**
     * Calls front controller's dispatch method and intercepts any exception thrown
     * if front contoller's throwExceptions was set to true.
     * Exception will be passed to _getExceptionResponse which will return an array
     * of exceptions that have to be returned to the client.
     * This method will json-encode those exceptions and based on the current context
     * (extRequest yes? no?) send those exceptions properly back to the client.
     * Script processing will be stopped if an exception has been caught so no further
     * proccesing of any logic will be possible afterwards.
     *
     */
    public static function dispatch()
    {
        $front = Zend_Controller_Front::getInstance();

        try {
            $front->dispatch();
        } catch (Exception $e) {

            $exceptions   = self::getCurrentExceptions($front->getResponse());
            $exceptions[] = $e;
            $result = self::mergeExceptions($exceptions);

            $isJson = true;

            try {
                $isJson = strpos(strtolower(
                            $front->getRequest()->getHeader('Content-Type')
                          ), 'json') !== false
                          ? true : false;

            } catch (Exception $e) {
                // ignore
            }

            if ($isJson) {
                /**
                 * @see Zend_Json
                 */
                require_once 'Zend/Json.php';

                $result = Zend_Json::encode($result);
                header("Content-Type: application/json");

                echo $result;
            } else {
                // we can assume that this happens during a none-json request
                echo "<h1>Error</h1>";
                echo "<pre>";
                print_r($result);
                echo "<hr>";
                debug_print_backtrace();
                echo "<hr>";
                print_r(debug_backtrace());
                echo "</pre>";
            }

            die();
        }
    }

    /**
     * Returns all exceptions that have been registered to the specified response
     * object.
     *
     * @param Zend_Controller_Response_Abstract $response The response object to check
     * for exceptions
     *
     * @return Array
     */
    public static function getCurrentExceptions(Zend_Controller_Response_Abstract $response)
    {
        // check for exceptions that have already been added to the
        // response
        $exceptions = $response->getException();

        return $exceptions;
    }

    /**
     * Merges the specified exceptions into an array that is prepared based
     * on the current context the request was dispatched in.
     *
     * @param Array $exceptions
     *
     * @return Array
     */
    public static function mergeExceptions(Array $exceptions)
    {
        $exceptions = self::transformExceptions($exceptions);

        // check whether procedure ends here. we have to add additional parameters
        // if the request was done via the Ext.Direct-API
        $isExtRequest      = false;
        $isExtMultiRequest = false;
        $extRequest   = null;

        /**
         * @see Zend_Registry
         */
        require_once 'Zend/Registry.php';

        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        try {
            $extRequest = Zend_Registry::get(Conjoon_Keys::EXT_REQUEST_OBJECT);
        } catch (Zend_Exception $e) {
            // ignore
        }

        if ($extRequest) {
            $isExtRequest      = $extRequest->isExtRequest();
            $isExtMultiRequest = $extRequest->isExtMultiRequest();
        }


        if (!$isExtRequest || ($extRequest && $extRequest->getConfigValue('singleException'))) {
            return $exceptions;
        } else {

            $extParameter = $extRequest->getConfigValue('extParameter');

            if ($isExtMultiRequest) {
                $stack = $extRequest->getRequestStack();
                $i     = count($stack)-1;
                $resArray = array();
                do {
                    $request = $stack[$i];

                    $params = $request->getParam($extParameter);
                    $resArray[] = array(
                        'type'    => 'exception',
                        'tid'     => $params['tid'],
                        'method'  => $params['method'],
                        'action'  => $params['action'],
                        'message' => $exceptions
                    );

                } while ($i--);

                return $resArray;
            }

            // a single request, but made with Ext.Direct-API. Get the latest request
            // object of the front controller
            $request = Zend_Controller_Front::getInstance()->getRequest();
            $params  = $request->getParam($extParameter);
            $org = $exceptions;
            $exceptions = array();
            $exceptions['message'] = $org;
            $exceptions['tid']     = $params['tid'];
            $exceptions['method']  = $params['method'];
            $exceptions['action']  = $params['action'];
            $exceptions['type']    = 'exception';

            return $exceptions;
        }
    }

    /**
     * Transforms an array of exceptions to an array that can be easily handled
     * by the frontend.
     * The following keys are available:
     *
     * success: always set to false
     * error  : either an array of Conjoon_ErrorDto's, or a single object of the
     *          type Conjoon_ErrorDto
     *
     * @param Array $exceptions
     *
     * @return Array
     */
    public static function transformExceptions(Array $exceptions = array())
    {
        /**
         * @see Conjoon_Error
         */
        require_once 'Conjoon/Error.php';

        $exList = array();
        if (count($exceptions) > 1) {
            for ($i = 0, $len = count($exceptions); $i < $len; $i++) {
                $ex       = Conjoon_Error::fromException($exceptions[$i]);
                $exList[] = $ex->getDto();
            }
        } else {
            $exception = Conjoon_Error::fromException($exceptions[0]);
            $exList    = $exception->getDto();
        }

        return array(
            'success' => false,
            'error'   => $exList
        );

    }

}