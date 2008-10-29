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
class Intrabuild_Filter_QuoteToBlockquote implements Zend_Filter_Interface
{

    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns a ">" quoted replaced with blockquots, i.e.
     * <pre>
     *  > test
     * </pre>
     *
     * becomes
     * <pre>
     *  <blockquote>test</blockquote>
     * </pre>
     *
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        $lines = explode("\n", $value);
        $len = count($lines);

        if ($len == 0 || strpos($value, '&gt;') === false) {
            return $value;
        }

        $text = "";
        for ($i = 0; $i < $len; $i++) {

            $quotes = "";
            $a = $i;
            while($a < $len && (strpos($lines[$a], '&gt;') === 0)) {
                $quotes .= $lines[$a]."\n";
                $a++;
            }
            $text .= $this->_quote($quotes);
            $i = $a;


            $text .= ($a == $len ? "" : $lines[$i]."\n");
        }

        return $text;
    }

    private function _quote($value)
    {
        // the replacement text we will return to parse()
        $quoted = "";

        // normalize ">" - this will group all ">" and remove whitespaces in
        // between them
        $value = preg_replace(
            "/^((\Q&gt;\E)+)(( (\Q&gt;\E)*)*)/em",
            "'$1'.str_replace(' &gt;', '&gt;', '\\3').''",
            $value
        );

        $matches = array();

        preg_match_all('/^((\Q&gt;\E)+) *?(.*?$)/ms', $value, $matches, PREG_SET_ORDER);

        $currentIntend = 0;

        // loop through each list-item element.
        foreach ($matches as $key => $match) {

            $intendation = strlen($match[1])/4;

            while ($intendation > $currentIntend) {
                $currentIntend++;
                $quoted .= "<blockquote>";
            }

            while ($currentIntend > $intendation) {
                $currentIntend--;
                $quoted .= "</blockquote>";
            }

            $quoted .= $match[3]."\n";
        }

        //$quoted = trim($quoted);

        while ($currentIntend > 0) {
            $currentIntend--;
            $quoted .= "</blockquote>";
        }

        return $quoted;
    }

}