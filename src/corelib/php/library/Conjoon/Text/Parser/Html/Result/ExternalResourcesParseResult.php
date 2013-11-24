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
