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
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';


/**
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @deprecated use Conjoon_Text_Parser_Mail_EmailAddressIdentityParser
 */
class Conjoon_Filter_EmailRecipients implements Zend_Filter_Interface
{
    private $_addslashes;
    private $_useQuoting;

    /**
     * Constructor.
     *
     */
    public function __construct($addSlashes = true, $useQuoting = true)
    {
        $this->_addSlashes = $addSlashes;
        $this->_useQuoting = $useQuoting;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Expects an array with recipients for an email address. Returns an array
     * with address/name pairs.
     *
     * Input:
     * [
     *  "\"Thorsten Suckow-Homberg\" <tsuckow@conjoon.org>, yo@mtv.com",
     *  "\"Pit Bull\" <pit@doggydog.com>",
     * ]
     *
     * Returns:
     * [
     *  ["tsuckow@conjoon.org", "Thorsten Suckow-Homberg"],
     *  ["yo@mtv.com"],
     *  ["pit@doggydog.com", "Pit Bull"],
     * ]
     *
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        /**
         * @see Conjoon_Text_Parser_Mail_EmailAddressIdentityParser
         */
        require_once 'Conjoon/Text/Parser/Mail/EmailAddressIdentityParser.php';

        $parser = new Conjoon_Text_Parser_Mail_EmailAddressIdentityParser();

        $value = (array)$value;

        $data = array();
        for ($i = 0, $len = count($value); $i < $len; $i++) {
            $data[] = $parser->parse($value[$i]);
        }

        return $data;
    }


}
