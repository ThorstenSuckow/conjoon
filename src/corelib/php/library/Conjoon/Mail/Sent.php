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
 * @see Zend_Mail
 */
require_once 'Conjoon/Mail.php';


/**
 * An object storing information about an Conjoon_Mail object that was sent.
 *
 */
class Conjoon_Mail_Sent{

    private $_mailObject;
    private $_header;
    private $_body;

    private $transport;

    public function __construct(Conjoon_Mail $mailObject, $header, $body = null)
    {
        $this->_mailObject = $mailObject;

        if ($body === null && ($header instanceof \Conjoon\Mail\Transport\Smtp)) {

            $this->transport = $header;
            $this->_header   = $header->header
                               ? $header->header
                               : $header->getPreparedHeader(
                                      $mailObject->getHeaders()
                               );

            $this->_body = $header->body
                           ? $header->body
                           : $mailObject->getBodyText(true);

            return;
        }

        $this->_header     = $header;
        $this->_body       = $body;

    }

    public function getTransport()
    {
        return $this->transport;
    }

    public function getMailObject()
    {
        return $this->_mailObject;
    }

    public function getHeader()
    {
        return $this->_header;
    }

    public function getBody()
    {
        return $this->_body;
    }



}
