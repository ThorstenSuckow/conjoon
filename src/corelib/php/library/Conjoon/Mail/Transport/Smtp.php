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

    /**
     * @type boolean
     */
    protected $isUsed = false;

    /**
     * While set to true, this transport won't send anything.
     * @type boolean
     */
    protected $isMailTextAssemble = false;

    /**
     * Returns true if a call to send() was made, i.e. the transport tried
     * to send this message.
     *
     * @return boolean
     */
    public function wasSent() {

        return $this->isUsed;

    }

    /**
     * @inheritdoc
     *
     * @throws \Conjoon_Mail_Exception if this transport was already used
     */
    public function send(\Zend_Mail $mail) {

        if ($this->isUsed) {
            /**
             * @see \Conjoon_Mail_Exception
             */
            require_once 'Conjoon/Mail/Exception.php';

            throw new \Conjoon_Mail_Exception(
                "Transport was already used"
            );
        }

        $this->isUsed = true;

        return parent::send($mail);
    }


    /**
     * It is not guaranteed that the prepared header is identical to the header
     * which gets send later on.
     *
     * @param \Conjoon_Mail $mail
     * @return string
     *
     * @throws Zend_Mail_Exception
     */
    public function getPreparedHeader(\Conjoon_Mail $mail)
    {
        $this->isMailTextAssemble = true;
        $this->send($mail);
        $this->isMailTextAssemble = false;
        return $this->header;
    }

    /**
     * Willonly issue sending the mail if #$isMailTextAssemble is set to false
     *
     * @inheritdoc
     */
    public function _sendMail() {
        if (!$this->isMailTextAssemble) {
            parent::_sendMail();
        }
    }


}
