<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: Letterman.php 137 2008-09-18 21:43:27Z T. Suckow $
 * $Date: 2008-09-18 23:43:27 +0200 (Do, 18 Sep 2008) $
 * $Revision: 137 $
 * $LastChangedDate: 2008-09-18 23:43:27 +0200 (Do, 18 Sep 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild_rep/trunk/src/corelib/php/library/Intrabuild/Modules/Groupware/Email/Letterman.php $
 */

/**
 * @see Intrabuild_Mail
 */
require_once 'Intrabuild/Mail.php';

/**
 * @see Zend_Mail_Transport_Smtp
 */
require_once 'Zend/Mail/Transport/Smtp.php';

/**
 * @see Intrabuild_Modules_Groupware_Email_Address
 */
require_once 'Intrabuild/Modules/Groupware/Email/Address.php';

/**
 * @see Intrabuild_Version
 */
require_once 'Intrabuild/Version.php';

/**
 * @see Intrabuild_Mail_Sent
 */
require_once 'Intrabuild/Mail/Sent.php';

/**
 * @see Intrabuild_Mail_Util
 */
require_once 'Intrabuild/Mail/Util.php';

/**
 * A utility class for sending emails.
 *
 * @category   Email
 * @package    Intrabuild_Modules_Groupware
 * @subpackage Intrabuild_Modules_Groupware_Email
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */

class Intrabuild_Modules_Groupware_Email_Sender {

    /**
     * Constructor.
     * Private to enforce static behavior
     */
    private function __construct()
    {
    }


    /**
     * Sends the draft for the specified account
     *
     * @param Intrabuild_Modules_Groupware_Email_Draft $draft The draft to send
     * @param Intrabuild_Modules_Groupware_Email_Account $account The account to use to send this email with
     *
     * @return Intrabuild_Mail_Sent
     *
     * @throws Zend_Mail_Exception
     */
    public static function send(Intrabuild_Modules_Groupware_Email_Draft $draft, Intrabuild_Modules_Groupware_Email_Account $account)
    {
        $mail = new Intrabuild_Mail('UTF-8');

        // let everyone know...
        $mail->addHeader('X-MailGenerator', 'Intrabuild ' . Intrabuild_Version::VERSION);

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

        $mail->setMessageId(
            Intrabuild_Mail_Util::generateMessageId(
                Intrabuild_Mail_Util::getHostFromAddress($account->getAddress())
            )
        );

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
            $mail->setBodyText($plain);
        }
        if ($html !== "") {
            $mail->setBodyHtml($html);
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
                'password' => $account->getPasswordOutbox()
            );
        }

        $transport = new Zend_Mail_Transport_Smtp($account->getServerOutbox(), $config);

        $mail->send($transport);

        return new Intrabuild_Mail_Sent($mail, $transport->header, $transport->body);
    }

}