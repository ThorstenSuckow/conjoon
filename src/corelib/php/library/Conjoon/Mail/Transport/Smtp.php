<?php
/**
 *
 */

namespace Conjoon\Mail\Transport;

/**
 * @see \Zend_Mail_Transport_Smtp
 */
require_once 'Zend/Mail/Transport/Smtp.php';

class Smtp extends \Zend_Mail_Transport_Smtp {


    public function getPreparedHeader($headers)
    {
        $myHeader = '';

        foreach ($headers as $header => $content) {
            if (isset($content['append'])) {
                unset($content['append']);
                $value = implode(',' . $this->EOL . ' ', $content);
                $myHeader .= $header . ': ' . $value . $this->EOL;
            } else {
                array_walk($content, array(get_class($this), '_formatHeader'), $header);
                $myHeader .= implode($this->EOL, $content) . $this->EOL;
            }
        }

        // Sanity check on headers -- should not be > 998 characters
        $sane = true;
        foreach (explode($this->EOL, $myHeader) as $line) {
            if (strlen(trim($line)) > 998) {
                $sane = false;
                break;
            }
        }
        if (!$sane) {
            /**
             * @see Zend_Mail_Transport_Exception
             */
            require_once 'Zend/Mail/Transport/Exception.php';
            throw new Zend_Mail_Exception('At least one mail header line is too long');
        }

        return $myHeader;
    }


}