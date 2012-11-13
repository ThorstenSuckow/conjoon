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
 * Quotes a name if needed to be used as the name for an email address.
 *
 * @uses Conjoon_Text_Transformer
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Transformer_Mail_EmailAddressNameQuoteTransformer
    extends Conjoon_Text_Transformer {



    /**
     * @inherit Conjoon_Text_Transformer::transform
     */
    public function transform($input)
    {
        $pattern = '/[,@\[\];"]/';

        $hit = preg_match($pattern, $input);

        if ($hit != 0) {
            // quote only if the string is not already quoted
            if  (substr($input, 0, 1) != '"' || substr($input, -1) != '"') {
                // if the string needs quoting, check if the quotes within the string
                // are already escaped
                if (strpos(trim($input, '"'), '\"') === false
                    && strpos(trim($input, '"'), '"') !== false) {
                    $input = str_replace('"', '\"', $input);
                }
                $input = '"' . $input . '"';
            }
        }

        return $input;
    }

}