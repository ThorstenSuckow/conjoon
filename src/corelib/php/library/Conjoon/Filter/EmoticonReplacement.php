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