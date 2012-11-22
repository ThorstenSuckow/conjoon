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
 * @see Conjoon_Mail_Client_Folder_ClientMailboxFolderPath
 */
require_once 'Conjoon/Mail/Client/Folder/ClientMailboxFolderPath.php';

/**
 * Provides a default implementation of
 * Conjoon_Mail_Client_Folder_ClientMailboxFolderPath.
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Mail_Client_Folder_DefaultClientMailboxFolderPath
    implements Conjoon_Mail_Client_Folder_ClientMailboxFolderPath{

    protected $_path = array();

    protected $_nodeId = null;

    protected $_rootId = null;

    /**
     * @inheritdoc
     */
    public function __construct($options)
    {
        /**
         * @see Conjoon_Argument_Check
         */
        require_once 'Conjoon/Argument/Check.php';

        $data = array('path' => $options);

        Conjoon_Argument_Check::check(array(
            'path' => array(
                'type'       => 'string',
                'allowEmpty' => false
            )
        ), $data);

        $options = $data['path'];

        /**
         * @see Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser
         */
        require_once 'Conjoon/Text/Parser/Mail/MailboxFolderPathJsonParser.php';

        $parser = new Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser();

        try {
            $parts = $parser->parse($options);
        } catch (Conjoon_Text_Parserexception $e) {

            /**
             * @see Conjoon_Mail_Client_Folder_ClientMailboxFolderPathException
             */
            require_once 'Conjoon/Mail/Client/Folder/ClientMailboxFolderPathException.php';

            throw new Conjoon_Mail_Client_Folder_ClientMailboxFolderPathException(
                "Could not extract path info from \"$options\" - exception "
                . "triggered by previous exception", 0, $e
            );
        }

        $this->_path   = $parts['path'];
        $this->_nodeId = $parts['nodeId'];
        $this->_rootId = $parts['rootId'];
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return $this->_path;
    }


    /**
     * @inheritdoc
     */
    public function getNodeId()
    {
        return $this->_nodeId;
    }

    /**
     * @inheritdoc
     */
    public function getRootId()
    {
        return $this->_rootId;
    }


    /**
     * @inheritdoc
     */
    public function __toArray()
    {
        return array(
            'path'   => $this->getPath(),
            'rootId' => $this->getRootId(),
            'nodeId' => $this->getNodeId()
        );
    }

}

