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
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';


/**
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_SortDirection implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns either ASC or DESC based on the passed parameter.
     *
     * @param  mixed $value
     * @return integer
     *
     * @deprecated use Conjoon_Text_Transformer_SortDirectionTransformer
     */
    public function filter($value)
    {
        /**
         * @see Conjoon_Text_Transformer_SortDirectionTransformer
         */
        require_once 'Conjoon/Text/Transformer/SortDirectionTransformer.php';

        $transformer = new Conjoon_Text_Transformer_SortDirectionTransformer();

        return $transformer->transform($value);
    }

}
