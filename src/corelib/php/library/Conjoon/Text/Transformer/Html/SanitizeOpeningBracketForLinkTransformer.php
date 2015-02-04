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

namespace Conjoon\Text\Transformer\Html;

/**
 * @see Conjoon_Text_Transformer
 */
require_once 'Conjoon/Text/Transformer.php';

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

use \Conjoon\Argument\ArgumentCheck as ArgumentCheck;

/**
 * Makes sure that opening &lt; brackets which are belonging to a link tag
 * (i.e. &lt;<a href=...></a>&gt;) are rendered properly before the opening tag
 * if a linefeed breaks them apart.
 *
 *
 * Example:
 *
 * Input:
 * ======
 * " &lt;<br /> <a href=""></a>&gt;" (without opening/closing quotes)
 *
 * Output:
 * =======
 * " &lt;<a href=""></a>&gt;" (without opening/closing quotes)
 *
 * @uses Conjoon_Text_Transformer
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class SanitizeOpeningBracketForLinkTransformer extends \Conjoon_Text_Transformer {

    /**
     * @inherit Conjoon_Text_Transformer::transform
     */
    public function transform($input)
    {
        $data = array('input' => $input);

        ArgumentCheck::check(array(
            'input' => array(
                'type'       => 'string',
                'allowEmpty' => false,
                'strict'     => true
             )
        ), $data);

        $value = $data['input'];

        return preg_replace("/ &lt;<br \/>\s*<a/", ' &lt;<a', $value);
    }

}