<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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

namespace Conjoon\Text\Parser\Html\Result;

/**
 * @see \Conjoon\Text\Parser\ParseResult
 */
require_once 'Conjoon/Text/Parser/ParseResult.php';

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';


use \Conjoon\Argument\ArgumentCheck;

/**
 * Default ParseResult for ExternalResourcesParser
 *
 * @uses \Conjoon\Text\Parser\ParseResult
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ExternalResourcesParseResult extends \Conjoon\Text\Parser\ParseResult {

    /**
     * @type bool
     */
    protected $externalResourcesAvailable = false;

    /**
     * Creates a new instance of this result object.
     *
     * @param bool $externalResourcesAvailable true to indicate that the parser
     * found external resources, otherwise false.
     *
     * @throws \Conjoon\Argument\InvalidArgumentException if passed argument is not of
     * type bool
     */
    public function __construct($externalResourcesAvailable) {

        $data = array(
            'externalResourcesAvailable' => $externalResourcesAvailable
        );

        ArgumentCheck::check(array(
            'externalResourcesAvailable' => array(
                'type' => 'bool',
                'allowEmpty' => false
            )
        ), $data);

        $this->externalResourcesAvailable = $data['externalResourcesAvailable'];

    }

    /**
     *
     * @return array returns an array with the following key-value pairs:
     *  - externalResources: boolean, true if external resources where found,
     *otherwise false
     *
     * @inheritdoc
     */
    public function getData() {

        return array(
            'externalResources' => $this->externalResourcesAvailable
        );

    }


}
