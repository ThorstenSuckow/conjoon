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

namespace Conjoon\Text\Parser;

/**
 * A simple class providing the interface for classes that operate and parse
 * text strings.
 *
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class ParseResult {

    /**
     * Returns an array with the detailed parse result data.
     *
     * @return array
     */
    abstract public function getData();

}
