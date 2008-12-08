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
