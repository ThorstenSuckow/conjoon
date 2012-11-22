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
 * @see Conjoon_Mail_Client_Message_Flag_ClientMessageFlagCollection
 */
require_once 'Conjoon/Mail/Client/Message/Flag/ClientMessageFlagCollection.php';


/**
 * A default implementation for ClientMessageFlagCollection
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Mail_Client_Message_Flag_DefaultClientMessageFlagCollection
    implements Conjoon_Mail_Client_Message_Flag_ClientMessageFlagCollection {


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

        Conjoon_Argument_Check::check(array(
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

        $parser = new Conjoon_Text_Parser_Mail_ClientMessageFlagListParser();

        try {
            $flags = $parser->parse($options);
        } catch (Conjoon_Text_Parser_Exception $e) {
            /**
             * @see Conjoon_Mail_Client_Message_Flag_ClientMessageFlagException
             */
            require_once 'Conjoon/Mail/Client/Message/Flag/ClientMessageFlagException.php';

            throw new Conjoon_Mail_Client_Message_Flag_ClientMessageFlagException(
                "flag-string for setting message flags seems to be invalid."
                . "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            );
        }

        $this->_createCollection($flags);
    }

    /**
     * @inheritdoc
     */
    public function getClientMessageFlags()
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

        /**
         * @see Conjoon_Mail_Client_Message_Flag_ClientSeenFlag
         */
        require_once 'Conjoon/Mail/Client/Message/Flag/ClientSeenFlag.php';

        for ($i = 0, $len = count($clientFlags); $i < $len; $i++) {
            $clientFlag =& $clientFlags[$i];

            $id = (string)$clientFlag['id'];

            switch (true) {
                case (array_key_exists('isRead', $clientFlag)):
                    $clear = (bool)$clientFlag['isRead'];

                    try {
                        $flags[] = new Conjoon_Mail_Client_Message_Flag_ClientSeenFlag(
                            $id, $clear
                        );
                    } catch (Conjoon_Argument_Exception $e) {
                        /**
                         * @see Conjoon_Mail_Client_Message_Flag_ClientMessageFlagException
                         */
                        require_once 'Conjoon/Mail/Client/Message/Flag/ClientMessageFlagException.php';

                        throw new Conjoon_Mail_Client_Message_Flag_ClientMessageFlagException(
                            "Could not create client flag. Exception thrown by "
                            . "previous exception: ". $e->getMessage(), 0, $e
                        );
                    }

                    break;

                default:
                    /**
                     * @see Conjoon_Mail_Client_Message_Flag_ClientMessageFlagException
                     */
                    require_once 'Conjoon/Mail/Client/Message/Flag/ClientMessageFlagException.php';

                    throw new Conjoon_Mail_Client_Message_Flag_ClientMessageFlagException(
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

