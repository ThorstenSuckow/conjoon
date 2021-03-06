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
 * Provides functionality for caching the configuration file and reading out
 * cached versions of the configuration file.
 *
 * Functionality in this file depends on the autoloader as defined by the conjoon
 * project.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

    /**
     * Tries to cache the config and load cached instances of the configuration.
     *
     * @return mixed Array or Conjoon_Config_Array
     *
     * @see conjoon_parseConfig
     */
    function conjoon_initConfigCache()
    {
        $conjoonSet = false;

        if (class_exists('Conjoon_Config_Array', true)) {
            $conjoonSet = true;
        } else {
            $conjoonSet = @include_once 'Conjoon/Config/Array.php';
        }

        if (!$conjoonSet) {
            // autoloader does not seem to work or we are currently
            // working in a dev environment where include_path has not
            // been set in teh webserver config.
            // Return parsed config.
            return conjoon_parseConfig();
        }

        $handle = null;

        // include path should be set using the webserver. Load the config!

        $didFileExist = file_exists('./_configCache/config.ini.php');

        if ($didFileExist) {
            $lastCacheModified  = filemtime('./_configCache/config.ini.php');
            $lastConfigModified = filemtime('./config.ini.php');

            if ($lastCacheModified === false || $lastConfigModified === false) {
                return conjoon_parseConfig();
            }
        }

        if (!$didFileExist || ($lastConfigModified > $lastCacheModified)) {

            $initialConfig = conjoon_parseConfig();

            $config = new Conjoon_Config_Array($initialConfig);

            $serialized = serialize($config);

            file_put_contents('./_configCache/config.ini.php',
                "<?php die(\"Forbidden!\"); ?>\n" .
                $serialized
            );

            return $config;

        } else {

            $serialized = file_get_contents('./_configCache/config.ini.php');

            $lines = explode("\n", $serialized, 2);

            $config = unserialize($lines[1]);

            // check if the library_path is set, and adjust the include_path if necessary
            // this should be obsolete since the autoloader should work
            if (($incPath = $config->environment->include_path) != null) {
                set_include_path(get_include_path() . PATH_SEPARATOR . $incPath);
            }

            return $config;
        }

    }

    /**
     * Parses the configuration file and sets the include path if found in the
     * configuration file.
     *
     */
    function conjoon_parseConfig()
    {
        // config failed to init, so we assume we have to load the config and parse it
        $initialConfig = parse_ini_file('./config.ini.php', true);

        // check if the library_path is set, and adjust the include_path if necessary
        if (($incPath = $initialConfig['environment']['include_path']) != null) {
            set_include_path(get_include_path() . PATH_SEPARATOR . $incPath);
        }

        // check whether we need the logging options
        if (isset($initialConfig['log']) && !$initialConfig['log']['enabled']) {
            unset($initialConfig['log']);
        } else if (isset($initialConfig['log'])) {
            /**
             * @see Conjoon_Log
             */
            include_once 'Conjoon/Log.php';

            // load Zend/Log for log constants so we don't have to
            // require this later on
            /**
             * @see Zend_Log
             */
            include_once 'Zend/Log.php';

            Conjoon_Log::init($initialConfig['log']);
        }

        /*@REMOVE@*/
        // check whether we need the application.connection options
        if (isset($initialConfig['application']) && !$initialConfig['application']['connection_check.enabled']) {
            unset($initialConfig['application']['connection_check.ip']);
            unset($initialConfig['application']['connection_check.timeout']);
            unset($initialConfig['application']['connection_check.port']);
        }
        /*@REMOVE@*/

        // take care of doctrine cache settings. make sure we void
        // caching if apc, memcache or memcached are selected,
        // but extensions are not loaded
        if (isset($initialConfig['application']) &&
            isset($initialConfig['application']['doctrine.cache.enabled']) &&
            $initialConfig['application']['doctrine.cache.enabled']) {
            $doctrineApp = &$initialConfig['application'];

            $doctrineTypes = array(
                'metadata_cache',
                'query_cache'
            );

            $disableDoctrineCache = true;

            foreach ($doctrineTypes as $doctrineCacheType) {
                $doctrineEnabledKey   = 'doctrine.cache.'.$doctrineCacheType.'.enabled';
                $doctrineCacheTypeKey = 'doctrine.cache.'.$doctrineCacheType.'.type';
                if ($doctrineApp[$doctrineEnabledKey] &&
                    $doctrineApp[$doctrineCacheTypeKey] != 'file') {
                    if (!extension_loaded($doctrineApp[$doctrineCacheTypeKey])) {
                        $doctrineApp[$doctrineEnabledKey] = false;

                        if ($initialConfig['log'] && $initialConfig['log']['enabled']) {
                            Conjoon_Log::log(
                                "\"" . $doctrineApp[$doctrineCacheTypeKey] . "\" for " .
                                "Doctrine Cache $doctrineCacheType selected, but " .
                                "extension is not available", Zend_Log::WARN
                            );
                        }
                    } else {
                        // dont disable cache is extension was loaded
                        $disableDoctrineCache = false;
                    }
                } else {
                    // dont disable cache if cache type is file
                    $disableDoctrineCache = false;
                }
            }

            // disable doctrine cache if loading extensions for each setting failed
            $initialConfig['application']['doctrine.cache.enabled'] =
                $disableDoctrineCache ? 0 : 1;
        }


        // take care of default cache
        if (isset($initialConfig['cache'])) {
            if (!$initialConfig['cache']['default.caching']) {
                unset($initialConfig['cache']);
            } else {
                $defaults = array();

                // extract defaults
                foreach ($initialConfig['cache'] as $key => $value) {
                    if (strpos($key, 'default.') === 0) {
                        $defaults[substr($key, 8)] = $initialConfig['cache'][$key];
                        unset($initialConfig['cache'][$key]);
                    }
                }

                // get the cache namespaces
                $cacheBlocks =& $initialConfig['cache'];
                $namespaces  = array();
                $unsets      = array();
                foreach ($cacheBlocks as $key => $value) {
                    $ns = explode(".", $key, 3);
                    if (array_key_exists($ns[0].'.'.$ns[1], $unsets)) {
                        continue;
                    }
                    if ($ns[2] == 'caching' && !$value) {
                        $unsets[$ns[0].'.'.$ns[1]] = true;
                    } else {
                        $namespaces[$ns[0].'.'.$ns[1]] = true;
                    }
                }

                // first off, unset all cache blocks that have caching set to 0
                foreach ($unsets as $key => $value) {
                    foreach ($cacheBlocks as $ckey => $cvalue) {
                         if (strpos($ckey, $key) === 0) {
                            unset($cacheBlocks[$ckey]);
                         }
                    }
                }

                foreach ($namespaces as $key => $value) {
                    foreach ($defaults as $defaultKey => $defaultValue) {
                        $m = $key . '.' . $defaultKey;
                        if (!array_key_exists($m, $cacheBlocks)) {
                            $cacheBlocks[$m] = $defaultValue;
                        }
                    }

                    // compute cache_dir backend HERE!
                    // check whether the cache-dir for the backend options is relative or
                    // absolute. This is a very simple check and may be error-prone,
                    // but its okay for now
                    $ck = $key.'.backend.cache_dir';
                    if (array_key_exists($ck, $cacheBlocks)) {
                        $cacheDir = $cacheBlocks[$ck];
                        if (strpos($cacheDir, '/') !== 0 &&
                            strpos($cacheDir, ':') !== 1) {
                            $cacheBlocks[$ck] = $initialConfig['environment']['application_path']
                                                . DIRECTORY_SEPARATOR
                                                . $cacheDir;
                        }
                    }
                }
            }
        }

        // take care of files
        if (isset($initialConfig['files'])) {
            // get the cache namespaces
            $cacheBlocks =& $initialConfig['files'];
            $namespaces  = array();

            foreach ($cacheBlocks as $key => $value) {
                $ns = explode(".", $key, 4);
                if (!isset($ns[2])) {
                    continue;
                }
                $namespaces[$ns[0].'.'.$ns[1].'.'.$ns[2]] = true;
            }

            foreach ($namespaces as $key => $value) {
                $ck = $key.'.dir';
                if (array_key_exists($ck, $cacheBlocks)) {
                    $cacheDir = $cacheBlocks[$ck];
                    if ($cacheDir && strpos($cacheDir, '/') !== 0 &&
                        strpos($cacheDir, ':') !== 1) {
                        $cacheBlocks[$ck] = $initialConfig['environment']['application_path']
                                            . DIRECTORY_SEPARATOR
                                            . $cacheDir;
                    }
                }
            }

        }

        return $initialConfig;
    }
