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

namespace Conjoon\Mail\Client\Folder\Strategy;

/**
 * @see Conjoon\Mail\Client\Folder\Strategy\FolderNamingForMovingStrategy
 */
require_once 'Conjoon/Mail/Client/Folder/Strategy/FolderNamingForMovingStrategy.php';

/**
 * @see Conjoon\Mail\Client\Folder\Strategy\FolderNamingForMovingStrategyResult
 */
require_once 'Conjoon/Mail/Client/Folder/Strategy/FolderNamingForMovingStrategyResult.php';

/**
 * @see Conjoon\Argument\ArgumentCheck;
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * @see Conjoon\Mail\Client\Folder\Strategy\StrategyException;
 */
require_once 'Conjoon/Mail/Client/Folder/Strategy/StrategyException.php';


use Conjoon\Argument\ArgumentCheck;

/**
 * Computes the name for a folder and compares against a list of folder names
 * to make sure the name is not already existent. If it is, the name is
 * re-computed.
 *
 * Example:
 *
 * Input:
 * ======
 * name: "My Folder"
 * list: ["Test", "Test2", "MyFolder"]
 * template: "{0} {1}"
 *
 *
 * Output:
 * =======
 * "My Folder (1)"
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultFolderNamingForMovingStrategy
    implements FolderNamingForMovingStrategy {

    /**
     * @type string
     */
    protected $template;

    /**
     * Constructs a new instance of this class.
     *
     * @param array $data The configuration for this class. Expected at least
     * the key value pair
     * - template string The template which is used for computing the folder
     * name. The following placeholders are considered:
     *  - {0} gets replaced with the name of the folder that should be used
     *    for naming
     *  - {1} gets replaced with a bracketed number >= 1, indicating the overall
     *    number of folders with the same name, -1
     *
     * @throws Conjoon\Mail\Client\Folder\Strategy\StrategyException
     */
    public function __construct(array $data) {

        try {
            ArgumentCheck::check(array(
                'template' => array(
                    'type'       => 'string',
                    'allowEmpty' => false,
                    'strict'     => true
                )
            ), $data);
        } catch (\Conjoon\Argument\InvalidArgumentException $e) {
            throw new StrategyException($e);
        }

        $this->template = $data['template'];
    }

    /**
     * Expects the following key/value-pairs in $data:
     * - name string The name to test against
     * - list array The list of values to test name against
     *
     * @inheritdoc
     */
    public function execute(array $data) {

        try {
            ArgumentCheck::check(array(
                'name'     => array(
                    'type'       => 'string',
                    'allowBlank' => false,
                    'strict'     => true
                ),
                'list'     => array(
                    'type'       => 'array',
                    'allowBlank' => false
                )
            ), $data);

        } catch (\Conjoon\Argument\InvalidArgumentException $e) {
            throw new StrategyException($e);
        }

        $name     = $data['name'];
        $cmpName  = trim(strtolower($name));
        $list     = $data['list'];
        $template = $this->template;

        $count     = 0;
        $foundInts = 0;
        foreach($list as $possibleMatch) {
            $target = trim(strtolower($possibleMatch));
            if ($target === $cmpName) {
                $count++;
            }

            // check now if there are already counted matches, such as "Folder (2)"
            // and consider them.
            if (stripos($target, $cmpName) === 0) {
                $possibleCounter = (substr($target, strlen($cmpName)));
                $possibleCounter = trim($possibleCounter);
                if (substr($possibleCounter, 0, 1) === '(' &&
                    substr($possibleCounter, strlen($possibleCounter) - 1, 1) === ')') {
                    $intVal = substr($possibleCounter, 1, strlen($possibleCounter) - 2);
                    if (is_numeric($intVal)) {
                        $intVal    = (int)$intVal;
                        $foundInts = max($foundInts, $intVal);
                    }
                }
            }
        }

        $count = max($count, $foundInts + 1);

        $name = str_replace("{0}", $name, $template);

        if ($count > 0) {
            $name = str_replace("{1}", "($count)", $name);
        } else {
            $name = str_replace("{1}", "", $name);
        }

        return new FolderNamingForMovingStrategyResult(trim($name));
    }

    /**
     * Returns the template value for an instance of this class.
     *
     * @return string
     */
    public function getTemplate() {
        return $this->template;
    }

}
