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
class Intrabuild_Filter_BlockquoteToQuote implements Zend_Filter_Interface
{

    /**
     * Defined by Zend_Filter_Interface
     *
     *
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        // first off, add an extra whitespace char to every "&gt;" that appears
        // at the beginning of a line, so they are not accidently identified as
        // as quotes. We will also check if the &gt; appears after a closing tag, in this case we
        // will also add an extra whitespace.
        // This also applies to text that starts with a &gt;
        $index = strpos($value, "&gt;");

        if ($index === 0) {
            $value = " ". $value;
        }

       $value = str_replace(
            array("\n&gt;", ">&gt;"),
            array("\n &gt;", "> &gt;"),
            $value
        );

        // normalize blockquote
        $value = preg_replace("/(<\/?)(blockquote)[^>]*>/i",
             "$1blockquote>",
             $value
        );

        // some browsers (IE) add linefeeds in between tags... remove them
        $value = str_replace(
            array("<blockquote>\n<blockquote>", "</blockquote>\n</blockquote>"),
            array("<blockquote><blockquote>", "</blockquote></blockquote>"),
            $value
        );

        $lines = explode("\n", $value);

        $quotes = array();
        $final  = array();
        for ($nr = 0, $len = count($lines); $nr < $len; $nr++) {
            $line = $lines[$nr];

            if (trim($line) == "") {
                $final[] = implode("", $quotes) . $line;
                continue;
            } else if (trim($line) == "<blockquote>") {
                $quotes[] = '&gt;';
                $final[] = implode("", $quotes) . preg_replace("/(<\/?)(blockquote)[^>]*>/i", "", $line);
                continue;
            } else if (trim($line) == "</blockquote>") {
                $final[] = implode("", $quotes) . preg_replace("/(<\/?)(blockquote)[^>]*>/i", "", $line);
                array_pop($quotes);
                continue;
            }

            $vLine = str_replace(
                array('<blockquote>', '</blockquote>'),
                array("\n<blockquote>\n", "\n</blockquote>\n"),
                $line
            );

            $vLines = explode("\n", $vLine);

            for ($a = 0, $lena = count($vLines); $a < $lena; $a++) {
                $tline = $vLines[$a];

                if ($tline == '<blockquote>') {
                    $quotes[] = '&gt;';
                } else if ($tline == '</blockquote>') {
                    array_pop($quotes);
                } else if ($tline == "") {
                    continue;
                } else {
                    $final[] = implode("", $quotes) . $tline;
                }
            }
        }

        return implode("\n", $final);

    }

}