<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: SortDirection.php 2 2008-06-21 10:38:49Z T. Suckow $
 * $Date: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $Revision: 2 $
 * $LastChangedDate: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/php/library/Intrabuild/Filter/SortDirection.php $
 */

/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';


/**
 * @category   Filter
 * @package    Intrabuild_Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Filter_EmoticonReplacement implements Zend_Filter_Interface {

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