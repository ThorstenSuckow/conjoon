<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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

/**
 * @see Conjoon_Text_Parser
 */
require_once 'Conjoon/Text/Parser.php';

/**
 * Transforms a text by looking up tokens which look like path parts and
 * returns an array with path informations
 *
 * Example:
 *
 * Input:
 * ======
 * /root/79/INBOXtttt/rfwe2/New folder (7)
 *
 * Output:
 * =======
 * array(
 *    'path'    => '/INBOXtttt/rfwe2/New folder (7)',
 *    'nodeId'  => 'New folder (7)',
 *    'rootId'  => 79
 * )
 *
 * @uses Conjoon_Text_Parser
 * @category   Text
 * @package    Conjoon_Text
 *
 * @deprecated use Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Parser_Mail_MailboxFolderPathParser extends Conjoon_Text_Parser {

    /**
     * Function extracts information from a given path.
     *
     * If existing, the following information
     * will be returned:
     *    'nodeId' => The id of the node, i.e. the last id found in the path
     *    'rootId' => The id of the root node of the path
     *    'path'   => The sanitized path, without "/root" and the numeric id
     *                of the root folder     *
     *
     * @param string $input
     *
     * @return array An array with the following key value pairs:
     *    'nodeId' => The id of the node, i.e. the last id found in the path
     *    'rootId' => The id of the root node of the path
     *    'path'   => The sanitized path, without "/root" and the numeric id
     *                of the root folder     *
     *  If path was empty, an empty array will be returned.
     *
     */
    public function parse($input)
    {
        $path = $input;

        if ($path == "") {
            return array();
        }

        // strip /root part to be sure
        $path = '/' . ltrim(ltrim($path, '/root/'), '/');

        $result = array();

        $parts = explode('/', $path);

        $result['nodeId'] = $parts[count($parts)-1];
        array_shift($parts);
        $result['rootId'] = array_shift($parts);
        $result['path']   = '/' . implode($parts, '/');

        return $result;
    }

}