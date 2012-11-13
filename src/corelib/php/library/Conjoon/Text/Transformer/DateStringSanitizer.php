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
 * Sanitizes a date string so Zend_Date is capable of parsing it.
 *
 * Example:
 *
 * Input:
 * ======
 * Wed, 6 May 2009 20:38:58 +0000 (GMT+00:00)
 *
 * Output:
 * =======
 * Wed, 6 May 2009 20:38:58 +0000
 *
 * @uses Conjoon_Text_Transformer
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Transformer_DateStringSanitizer
    extends Conjoon_Text_Transformer {

    /**
     * @inherit Conjoon_Text_Transformer::transform
     */
    public function transform($input)
    {
        /**
         * @see Conjoon_Argument_Check
         */
        require_once 'Conjoon/Argument/Check.php';

        $data = array('input' => $input);

        Conjoon_Argument_Check::check(array(
            'input' => array(
                'type'       => 'string',
                'allowEmpty' => false
             )
        ), $data);

        $input = $data['input'];

        $regex = '/(\(.*\))$/';
        $str = preg_replace($regex, "", $input);

        return trim($str);
    }

}