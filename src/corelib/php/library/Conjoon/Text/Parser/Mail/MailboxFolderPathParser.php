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