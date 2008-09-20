<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: Message.php 73 2008-08-21 22:15:14Z T. Suckow $
 * $Date: 2008-08-22 00:15:14 +0200 (Fr, 22 Aug 2008) $
 * $Revision: 73 $
 * $LastChangedDate: 2008-08-22 00:15:14 +0200 (Fr, 22 Aug 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/php/library/Intrabuild/Mail/Message.php $
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
