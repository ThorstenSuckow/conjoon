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
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/**
 * Strips unneeded information from a node path as build and submitted
 * by the Ext framework. The remaining fragment should only contain
 * database-ids and/or global names defined by an IMAP server, separated
 * by a "slash".
 * Note:
 * This filter should not be used anymore. Instead, the client should submit
 * paths as json encoded arrays to the backend, so no path separator pollution
 * occurs.
 *
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @deprecated use Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser
 */
class Conjoon_Filter_SanitizeExtFolderPath implements Zend_Filter_Interface
{

    /**
     * Defined by Zend_Filter_Interface
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        $value = trim((string)$value);

        return rtrim(ltrim($value, '/root'), '/');
    }
}
