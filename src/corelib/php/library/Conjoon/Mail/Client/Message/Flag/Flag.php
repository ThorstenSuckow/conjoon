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


namespace Conjoon\Mail\Client\Message\Flag;

/**
 * A client message flag is a oo representation of a message flag. A message
 * flag exists of an id for the message, and a boolean value clear which
 * tells whether the flag is about to be set or unset.
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class Flag implements \Conjoon\Mail\Message\Flag\MessageFlag{

    /**
     * @var string
     */
    protected $_uId;

    /**
     * @var bool
     */
    protected $_clear;

    /**
     * Constructs a new instance of this class.
     *
     * @param string $uId
     * @param bool $clear whether the flag represented by this class
     * should be removed, or not
     *
     * @throws Conjoon_Argument_Exception if either $meesageId or $clear
     * did not evaluate to the expected types.
     */
    public function __construct($uId, $clear = false)
    {
        $data = array('uId' => $uId, 'clear' => $clear);

        /**
         * @see Conjoon_Argument_Check
         */
        require_once 'Conjoon/Argument/Check.php';

        \Conjoon_Argument_Check::check(array(
            'uId' => array(
                'type'       => 'string',
                'allowEmpty' => false
            ),
            'clear' => array(
                'type'       => 'bool',
                'allowEmpty' => false
            ),
        ), $data);

        $this->_uId = $data['uId'];
        $this->_clear     = $data['clear'];
    }

    /**
     * @inheritdoc
     */
    public function getUId()
    {
        return $this->_uId;
    }

    /**
     * @inheritdoc
     */
    public function isClear()
    {
        return $this->_clear;
    }

}

