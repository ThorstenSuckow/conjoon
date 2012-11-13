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
 * A simple class providing the interface for classes that operate and tranform
 * text strings.
 *
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class Conjoon_Text_Transformer {

    /**
     * @type mixed
     */
    protected $_options;

    public function __construct(Array $options = array())
    {
        $this->_options = $options;
    }

    /**
     * Takes an input argument and transforms $input to the $output string.
     *
     * @param string $input
     *
     * @return string
     *
     * @throws Conjoon_Text_Transformer_Exception, Conjoon_Argument_Exception
     */
    abstract public function transform($input);

}