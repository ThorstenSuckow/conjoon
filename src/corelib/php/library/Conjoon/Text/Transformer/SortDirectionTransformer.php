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
 * Transforms a text by simply returning ASC or DESC based om the input.
 * Simply uppercases the asc or desc string. If the string does not equal to asc
 * or desc, ASC is returned by default.
 *
 * Example:
 *
 * Input:
 * ======
 * asc
 * test
 * DESC
 * desc
 *
 * Output:
 * =======
 * ASC
 * ASC
 * DESC
 * DESC
 *
 * @uses Conjoon_Text_Transformer
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Transformer_SortDirectionTransformer extends Conjoon_Text_Transformer {

    /**
     * @inherit Conjoon_Text_Transformer::transform
     */
    public function transform($input)
    {
        $str = trim(strtolower((string)$input));

        switch ($str) {
            case 'asc':
                return 'ASC';
            case 'desc':
                return 'DESC';
            default:
                return 'ASC';
        }


    }

}