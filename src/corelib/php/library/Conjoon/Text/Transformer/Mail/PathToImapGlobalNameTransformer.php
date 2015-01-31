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

/**
 * @see Conjoon_Text_Transformer
 */
require_once 'Conjoon/Text/Transformer.php';

/**
 * Transforms a text by looking up tokens which look like path components
 *(using / as path separator) and return them as a Global Name of an Imap folder.
 *
 * Example:
 *
 * Input:
 * ======
 * 1)
 * delimiter: .
 * popTail : false
 * /INBOX/[Merge] Test/Messages
 *
 * 2)
 * delimiter: .
 * popTail : true
 * /INBOX/[Merge] Test/Messages
 *
 * Output:
 * =======
 *  1) INBOX.[Merge] Test.Messages
 *  2) INBOX.[Merge] Test
 *
 * @uses Conjoon_Text_Transformer
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Transformer_Mail_PathToImapGlobalNameTransformer
    extends Conjoon_Text_Transformer {

    /**
     * Allows for specifying the options
     *  - delimiter: string, the delimiter for the Imap global name if path consists
     *               of more than one parts
     *  - popTail: bool, whether to return the global name without the last part,
     *             if any. Defaults to false
     *
     * @param array $options
     *
     * @throws Conjoon_Argument_Exception if delimiter was not specified
     */
    public function __construct(Array $options = array())
    {
        /**
         * @see Conjoon_Argument_Check
         */
        require_once 'Conjoon/Argument/Check.php';

        Conjoon_Argument_Check::check(array(
            'delimiter' => array(
                'type'       => 'string',
                'allowEmpty' => false
            )
        ), $options);

        $this->_options = $options;

        $this->_options['popTail'] = isset($this->_options['popTail'])
                                     ? (bool)$this->_options['popTail']
                                     : false;
    }

    /**
     * @inherit Conjoon_Text_Transformer::transform
     */
    public function transform($input)
    {
        $path = $input;

        $delim   = $this->_options['delimiter'];
        $popTail = $this->_options['popTail'];

        if ($path == "/" || $path == $delim) {
            /**
             * @see Conjoon_Text_Transformer_Exception
             */
            require_once 'Conjoon/Text/Transformer/Exception.php';

            throw new Conjoon_Text_Transformer_Exception(
                "\"path\" was invalid: $path"
            );
        } else {
            $path = rtrim(ltrim(str_replace('/', $delim, $path), $delim), $delim);
        }

        $parts = explode($delim, $path);

        if ($popTail === true) {
            array_pop($parts);
        }

        return implode($delim, $parts);
    }

}