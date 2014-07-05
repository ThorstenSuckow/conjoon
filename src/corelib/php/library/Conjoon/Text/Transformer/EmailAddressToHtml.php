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
 *
 * @deprecated use Conjoon_Text_Transformer_Email_EmailAddressToHtmlTransformer
 */
class Conjoon_Text_Transformer_EmailAddressToHtml extends Conjoon_Text_Transformer {

    /**
     * @inherit Conjoon_Text_Transformer::transform
     */
    public function transform($input)
    {
        /**
         * @see Conjoon_Text_Transformer_Mail_EmailAddressToHtmlTransformer
         */
        require_once 'Conjoon/Text/Transformer/Mail/EmailAddressToHtmlTransformer.php';

        $transformer = new Conjoon_Text_Transformer_Mail_EmailAddressToHtmlTransformer();

        return $transformer->transform($input);
    }

}