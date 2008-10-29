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
 * Utility methods for Intrabuild_Mail-package.
 *
 * @package Intrabuild_Mail
 */
class Intrabuild_Mail_Util{

    /**
     * Enforce static behavior.
     */
    private function __construct()
    {
    }

    /**
     * Generate a unique message id.
     *
     * @param string $domain The domain for which the unique message id should be
     * generated
     *
     * @return string
     */
    public static function generateMessageId($host)
    {
        return '<' . md5(uniqid(rand(), true)) . '@' . $host . '>';
    }

    /**
     * Extracts the host part of an email address
     *
     * @param string $emailAddress The email address to get the host part for
     *
     * @return string
     */
    public static function getHostFromAddress($emailAddress)
    {
        $parts = explode('@', (string)$emailAddress);

        return array_pop($parts);
    }

}
