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