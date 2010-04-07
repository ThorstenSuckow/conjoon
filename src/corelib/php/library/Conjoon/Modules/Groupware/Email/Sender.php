<?php
/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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
 * @see Conjoon_Mail
 */
require_once 'Conjoon/Mail.php';

/**
 * @see Zend_Mail_Transport_Smtp
 */
require_once 'Zend/Mail/Transport/Smtp.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Address
 */
require_once 'Conjoon/Modules/Groupware/Email/Address.php';

/**
 * @see Conjoon_Version
 */
require_once 'Conjoon/Version.php';

/**
 * @see Conjoon_Mail_Sent
 */
require_once 'Conjoon/Mail/Sent.php';

/**
 * A utility class for sending emails.
 *
 * @category   Email
 * @package    Conjoon_Modules_Groupware
 * @subpackage Conjoon_Modules_Groupware_Email
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */

class Conjoon_Modules_Groupware_Email_Sender {

    /**
     * Constructor.
     * Private to enforce static behavior
     */
    private function __construct()
    {
    }

    /**
     * Added to the demo branch of conjoon - returns a signature with additional
     * information about the origin of the generated message.
     *
     * @return string
     */
    private static function getAdditionalSignature()
    {
        return "\n\n\n-- \n"
            ."This email was sent using the demo version of conjoon - "
            ."http://www.conjoon.org\n\n"
            ."Follow us on Twitter: http://twitter.com/conjoon"
            ."\n"
            ."Become a fan of conjoon: "
            ."http://www.facebook.com/pages/conjoon/10150110840430717"
            ."\n"
            ."Please report abuse to admin@conjoon.org";
    }

    /**
     * Sends the draft for the specified account
     *
     * @param Conjoon_Modules_Groupware_Email_Draft $draft The draft to send
     * @param Conjoon_Modules_Groupware_Email_Account $account The account to use to send this email with
     *
     * @return Conjoon_Mail_Sent
     *
     * @throws Zend_Mail_Exception
     */
    public static function send(Conjoon_Modules_Groupware_Email_Draft $draft, Conjoon_Modules_Groupware_Email_Account $account)
    {
        $mail = new Conjoon_Mail('UTF-8');

        // let everyone know...
        $mail->addHeader('X-MailGenerator', 'conjoon ' . Conjoon_Version::VERSION);

        /**
         * Some clients need the MIME-Version header field. For example,
         * Outlook might have problems with decoding a message if no mime-version
         * is specified.
         */
        $mail->addHeader('MIME-Version', '1.0');


        // add recipients
        $to  = $draft->getTo();
        $cc  = $draft->getCc();
        $bcc = $draft->getBcc();
        foreach ($cc as $address) {
            $mail->addCc($address->getAddress(), $address->getName());
        }
        foreach ($to as $address) {
            $mail->addTo($address->getAddress(), $address->getName());
        }
        foreach ($bcc as $address) {
            $mail->addBcc($address->getAddress(), $address->getName());
        }

        $mail->setMessageId(true);

        // set sender
        $mail->setFrom($account->getAddress(), $account->getUserName());
        // set reply-to
        if ($account->getReplyAddress() != "") {
            $mail->setReplyTo($account->getReplyAddress());
        }

        // set in-reply-to
        if ($draft->getInReplyTo() != "") {
            $mail->setInReplyTo($draft->getInReplyTo());
        }

        // set references
        if ($draft->getReferences() != "") {
            $mail->setReferences($draft->getReferences());
        }

        // set date
        $mail->setDate($draft->getDate());

        // and the content
        $mail->setSubject($draft->getSubject());

        $plain = $draft->getContentTextPlain();
        $html  = $draft->getContentTextHtml();

        if ($plain === "" && $html === "") {
            $plain = " ";
        }

        if ($plain !== "") {
            // add the signature which is only visible to recipients
            $mail->setBodyText($plain . self::getAdditionalSignature());
        }
        if ($html !== "") {
            // add the signature which is only visible to recipients
            $mail->setBodyHtml($html . self::getAdditionalSignature());
        }


        // send!
        $config = array();
        if ($account->isOutboxAuth()) {
            $config = array(
                /**
                 * @todo allow for other auth methods as provided by ZF
                 */
                'auth'     => 'login',
                'username' => $account->getUsernameOutbox(),
                'password' => $account->getPasswordOutbox(),
                'port'     => $account->getPortOutbox()
            );
        }

        $transport = new Zend_Mail_Transport_Smtp($account->getServerOutbox(), $config);

        // Zend_Mail_Protocol_Abstract would not supress errors thrown by the native
        // stream_socket_client function, thus - depending on the setting of error_reporting -
        // a warning will bubble up if no internet conn is available while sending emails.
        // supress this error here.
        // An excpetion will be thrown right at this point if the message could not
        // be sent
        @$mail->send($transport);


        return new Conjoon_Mail_Sent($mail, $transport->header, $transport->body);
    }

}