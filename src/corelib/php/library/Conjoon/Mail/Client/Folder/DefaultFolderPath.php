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


namespace Conjoon\Mail\Client\Folder;

use \Conjoon\Argument\ArgumentCheck;

/**
 * @see \Conjoon\Mail\Client\Folder\FolderPath
 */
require_once 'Conjoon/Mail/Client/Folder/FolderPath.php';

/**
 * Provides a default implementation of
 * Conjoon_Mail_Client_Folder_ClientMailboxFolderPath.
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultFolderPath implements FolderPath {

    protected $_path = array();

    protected $_nodeId = null;

    protected $_rootId = null;

    /**
     * @inheritdoc
     */
    public function __construct($options)
    {
        /**
         * @see \Conjoon\Argument\ArgumentCheck
         */
        require_once 'Conjoon/Argument/ArgumentCheck.php';

        $data = array('path' => $options);

        ArgumentCheck::check(array(
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

        $parser = new \Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser();

        try {
            $parts = $parser->parse($options);
        } catch (Conjoon_Text_Parserexception $e) {

            /**
             * @see \Conjoon\Mail\Client\Folder\FolderPathException
             */
            require_once 'Conjoon/Mail/Client/Folder/FolderPathException.php';

            throw new FolderPathException(
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

    /**
     * @inheritdoc
     */
    public function __toString() {
        return json_encode(
            array(
                'rootId' => $this->getRootId(),
                'path' => $this->getPath(),
                'nodeId' => $this->getNodeId()
            )
        );
    }

}

