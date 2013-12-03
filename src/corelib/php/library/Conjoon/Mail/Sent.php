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

    /**
     * @type Conjoon_Mail
     */
    private $mailObject;

    /**
     * @type boolean
     */
    protected $isPrepared;

    /**
     * @type string
     */
    protected $expectedBodyText;

    /**
     * @type string
     */
    protected $expectedHeaderText;

    /**
     * @type \Conjoon\Mail\Transport\Smtp
     */
    private $transport;

    public function __construct(Conjoon_Mail $mailObject, \Conjoon\Mail\Transport\Smtp $transport)
    {
        $this->mailObject = $mailObject;

        $this->transport = $transport;
    }

    /**
     * @return Conjoon\Mail\Transport\Smtp
     */
    public function getTransport() {
        return $this->transport;
    }

    /**
     * @return Conjoon_Mail
     */
    public function getMailObject() {
        return $this->mailObject;
    }

    /**
     * @return string
     */
    public function getExpectedHeaderText() {

        $this->prepareMail();

        return $this->expectedHeaderText;
    }

    /**
     * @return string
     */
    public function getExpectedBodyText() {
        $this->prepareMail();

        return $this->expectedBodyText;
    }

    /**
     * @return void
     */
    protected function prepareMail() {

        if ($this->isPrepared) {
            return;
        }

        $this->expectedHeaderText = $this->transport->getPreparedHeader(
            $this->mailObject
        );
        $this->expectedBodyText = $this->transport->body;

        $this->isPrepared = true;

    }

    /**
     * @return string
     * @throws Conjoon_Mail_Exception
     */
    public function getSentHeaderText() {
        if (!$this->transport->wasSent()) {
            /**
             * @see Conjoon_Mail_Exception
             */
            require_once 'Conjoon/Mail/Exception.php';
            throw new Conjoon_Mail_Exception(
                "Cannot retrieve sent header text from unsent mail message"
            );
        }
        return $this->transport->header;
    }

    /**
     * @return string
     * @throws Conjoon_Mail_Exception
     */
    public function getSentBodyText() {
        if (!$this->transport->wasSent()) {
            /**
             * @see Conjoon_Mail_Exception
             */
            require_once 'Conjoon/Mail/Exception.php';
            throw new Conjoon_Mail_Exception(
                "Cannot retrieve sent body text from unsent mail message"
            );
        }

        return $this->transport->body;
    }



}
