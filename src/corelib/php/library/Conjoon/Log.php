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
 * @see Zend_Log
 */
require_once 'Zend/Log.php';

/**
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Log {

    /**
     * @var Zend_log
     */
    private static $_log = null;

    /**
     * @var boolean
     */
    private static $_initCalled = false;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * Returns true if this logger instance was already configured,
     * otherwise false.
     *
     * @return bool
     *
     * @static
     */
    public static function isConfigured() {
        return self::$_initCalled == true;
    }

    /**
     * Inits this logger with the configuration provided
     * from $options.
     *
     * @param Array $options An configuration array with
     * the following properties:
     *  - enabled: 1/0 1 for enabled, otherwise 0
     *  - writer: the type of write to use. Available writers:
     *      - firebug: to write directly to the firebug console
     *
     * @throws Conjoon_Exception if init was already called,
     * assuming this method may only be called once.
     */
    public static function init(Array $options)
    {
        if (self::isConfigured()) {
            /**
             * @see Conjoon_Exception
             */
            require_once 'Conjoon/Exception.php';

            throw new Conjoon_Exception("init may only be called once.");
        }

        if (!$options['enabled']) {
            self::$_initCalled = true;
            return;
        }

        self::$_log = new Zend_Log();

        switch ($options['writer']) {
            case 'firebug':
                /**
                 * @see Zend_Log_Writer_Firebug
                 */
                require_once 'Zend/Log/Writer/Firebug.php';

                self::addWriter(new Zend_Log_Writer_Firebug());
            break;
        }

        self::$_initCalled = true;
    }


    /**
     * Log a message at a priority
     *
     * @param  mixed    $message   Message to log
     * @param  integer  $priority  Priority of message
     * @return void
     * @throws Zend_Log_Exception
     */
    public static function log($message, $priority)
    {
        if (!self::$_log) {
            return;
        }

        self::$_log->log($message, $priority);
    }

    /**
     * Add a writer.  A writer is responsible for taking a log
     * message and writing it out to storage.
     *
     * @param  Zend_Log_Writer_Abstract $writer
     * @return void
     */
    public static function addWriter(Zend_Log_Writer_Abstract $writer)
    {
        if (!self::$_log) {
            return;
        }

        self::$_log->addWriter($writer);
    }

}
