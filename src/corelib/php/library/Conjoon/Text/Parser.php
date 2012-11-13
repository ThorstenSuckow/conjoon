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
 * A simple class providing the interface for classes that operate and parse
 * text strings.
 *
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class Conjoon_Text_Parser {

    /**
     * @type mixed
     */
    protected $_options;

    public function __construct($options = array())
    {
        $this->_options = $options;
    }

    /**
     * Takes an input argument and returns the parsed result.
     *
     * @param string $input
     *
     * @return mixed
     *
     * @throws Conjoon_Text_Parser_Exception, Conjoon_Argument_Exception
     */
    abstract public function parse($input);

}