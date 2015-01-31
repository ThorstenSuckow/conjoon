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


namespace Conjoon\Mail\Client\Message\Flag;

/**
 * @see Conjoon_Mail_Client_Message_Flag_ClientMessageFlagCollection
 */
require_once 'Conjoon/Mail/Client/Message/Flag/FlagCollection.php';

/**
 * @see Conjoon_Mail_Client_Message_Flag_FlagException
 */
require_once 'Conjoon/Mail/Client/Message/Flag/FlagException.php';

/**
 * @see Conjoon\Mail\Client\Message\Flag\SeenFlag
 */
require_once 'Conjoon/Mail/Client/Message/Flag/SeenFlag.php';

/**
 * @see Conjoon\Mail\Client\Message\Flag\JunkFlag
 */
require_once 'Conjoon/Mail/Client/Message/Flag/JunkFlag.php';

/**
 * @see Conjoon\Mail\Client\Message\Flag\NotJunkFlag
 */
require_once 'Conjoon/Mail/Client/Message/Flag/NotJunkFlag.php';

/**
 * A default implementation for ClientMessageFlagCollection
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultFlagCollection implements FlagCollection {


    /**
     * @var array
     */
    protected $_flags = array();

    /**
     * @inheritdoc
     */
    public function __construct($options)
    {
        /**
         * @see Conjoon_Argument_Check
         */
        require_once 'Conjoon/Argument/Check.php';

        $data = array('messageFlagText' => $options);

        \Conjoon_Argument_Check::check(array(
            'messageFlagText' => array(
                'type'       => 'string',
                'allowEmpty' => false
            )
        ), $data);

        $options = $data['messageFlagText'];

        /**
         * @see Conjoon_Text_Parser_Mail_ClientMessageFlagListParser
         */
        require_once 'Conjoon/Text/Parser/Mail/ClientMessageFlagListParser.php';

        $parser = new \Conjoon_Text_Parser_Mail_ClientMessageFlagListParser();

        try {
            $flags = $parser->parse($options);
        } catch (\Conjoon_Text_Parser_Exception $e) {
            /**
             * @see Conjoon_Mail_Client_Message_Flag_ClientMessageFlagException
             */
            require_once 'Conjoon/Mail/Client/Message/Flag/FlagException.php';

            throw new FlagException(
                "flag-string for setting message flags seems to be invalid."
                . "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            );
        }

        $this->_flags = $this->_createCollection($flags);
    }

    /**
     * @inheritdoc
     */
    public function getFlags()
    {
        return $this->_flags;
    }


    /**
     * Helper function for creating message flags.
     *
     * @param array $flags The client generated string parsed into an array
     *
     * @return void
     */
    public function _createCollection(Array $clientFlags)
    {
        $flags = array();

        for ($i = 0, $len = count($clientFlags); $i < $len; $i++) {
            $clientFlag =& $clientFlags[$i];

            $id = (string)$clientFlag['id'];

            switch (true) {

                case (array_key_exists('isRead', $clientFlag)):
                    $clear = ! (bool) $clientFlag['isRead'];

                    try {
                        $flags[] = new SeenFlag(
                            $id, $clear
                        );
                    } catch (\Conjoon_Argument_Exception $e) {

                        throw new FlagException(
                            "Could not create client flag. Exception thrown by "
                            . "previous exception: ". $e->getMessage(), 0, $e
                        );
                    }

                    break;

                case (array_key_exists('isSpam', $clientFlag)):
                    $clear = ! ((bool) $clientFlag['isSpam']);

                    try {

                        if ($clear) {
                            $flags[] = new NotJunkFlag($id, false);
                        } else {
                            $flags[] = new JunkFlag($id, false);
                        }

                    } catch (\Conjoon_Argument_Exception $e) {

                        throw new FlagException(
                            "Could not create client flag. Exception thrown by "
                                . "previous exception: ". $e->getMessage(), 0, $e
                        );
                    }

                    break;

                default:

                    throw new FlagException(
                        "Unknown flag in client flag: \""
                        . implode(', ', array_keys($clientFlag))
                        . "\""
                    );
                    break;
            }
        }

        return $flags;
    }

}

