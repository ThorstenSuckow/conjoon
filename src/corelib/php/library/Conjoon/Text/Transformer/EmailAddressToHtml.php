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
 * Transforms a text by looking up tokens which look like email addresses and
 * replaces them with html code to make those addresses clickable in a document.
 *
 * Example:
 *
 * Input:
 * ======
 * This is a text. You can answer user@domain.tld if you like.
 *
 * Output:
 * =======
 * This is a text. You can answer
 * <a href="mailto:user@domain.tld">user@domain.tld</a> if you like.
 *
 * @uses Conjoon_Text_Transformer
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Transformer_EmailAddressToHtml extends Conjoon_Text_Transformer {

    /**
     * @inherit Conjoon_Text_Transformer::transform
     */
    public function transform($input)
    {
        $output = "";

        $pattern = "/([_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.([a-z]){2,6}))/is";

        $output = preg_replace(
            $pattern,
            "<a href=\"mailto:$1\">$1</a>",
            $input
        );

        return $output;
    }

}