<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
require_once 'Intrabuild/Mail.php';


/**
 * An object storing information about an Intrabuild_Mail object that was sent.
 *
 */
class Intrabuild_Mail_Sent{

    private $_mailObject;
    private $_header;
    private $_body;


    public function __construct(Intrabuild_Mail $mailObject, $header, $body)
    {
        $this->_mailObject = $mailObject;
        $this->_header     = $header;
        $this->_body       = $body;

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
