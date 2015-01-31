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
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';


/**
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_EmoticonReplacement implements Zend_Filter_Interface {

    private $_emoticonMap = array(
        'O:-)'    => 'O:-)',
        ':-)'     => ':-)',
        ':)'      => ':)',
        ':-D'     => ':-D',
        ':D'      => ':D',
        ':-('     => ':-(',
        ':('      => ':(',
        ':-['     => ':-[',
        ';-)'     => ';-)',
        ';)'      => ';)',
        ':-\\'    => ':-\\',
        ':-P'     => ':-P',
        ';-P'     => ';-P',
        ':P'      => ':P',
        '=-O'     => '=-O',
        ':-*'     => ':-*',
        ':*'      => ':*',
        '&gt;:o'  => '&gt;:o',
        '&gt;:-o' => '&gt;:-o',
        '8-)'     => '8-)',
        ':-$'     => ':-$',
        ':-!'     => ':-!',
        ':\'('    => ':\'(',
        ':-X'     => ':-X'
    );

    public function __construct(Array $emoticonMap = array())
    {
        $this->_emoticonMap = array_merge($this->_emoticonMap, $emoticonMap);
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the text h all ascii-emoticons replaced as defined in $_emoticonMap
     * with their corresponding values.
     *
     * @param  mixed $value
     * @return string
     */
    public function filter($value)
    {
        foreach ($this->_emoticonMap as $emoticon => $replacement) {
            $emoticon = str_replace(
                array('/', '\\', '*', ')', '(', '[', ']', '|', '$'),
                array('\/', '\\\\', '\*', '\)', '\(', '\[', '\]', '\|', '\$'),
                $emoticon
            );
            $value = preg_replace( "/(^".$emoticon.")|( )(".$emoticon.")/im", "\\2".$replacement, $value);
        }

        return $value;
    }
}