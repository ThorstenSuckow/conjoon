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
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Email_ImapHelper {

    /**
     * @var array $_delimiterCache
     */
    private static $_delimiterCache = array();

    /**
     * @var array $_imapProtocolCache
     */
    private static $_imapProtocolCache = array();

    /**
     * Enforce static behavior.
     */
    private function __construct()
    {
    }

    /**
     * Checks whether there is already an opened connection for this
     * account and returns it, or establishes a new connection if not
     * found.
     *
     * @param Conjoon_Modules_Groupware_Email_Account_Dto $account
     *
     * @return Zend_Mail_Protocol_Imap
     *
     * @throws Conjoon_Modules_Groupware_Email_Exception if no connection
     * could be established for the specified account, or if the specified
     * account does not use the IMAP protocol
     */
    public static function reuseImapProtocolForAccount(
        Conjoon_Modules_Groupware_Email_Account_Dto $account)
    {
        if ($account->protocol !== 'IMAP') {

            /**
             * @see Conjoon_Modules_Groupware_Email_Exception
             */
            require_once 'Conjoon/Modules/Groupware/Email/Exception.php';

            throw new Conjoon_Modules_Groupware_Email_Exception(
                "The given account with the name \"".$account->name."\" "
                . "does not use the IMAP protocol"
            );
        }

        if (isset(self::$_imapProtocolCache[$account->id])) {
            try {
                self::$_imapProtocolCache[$account->id]->noop();
                return self::$_imapProtocolCache[$account->id];
            } catch (Zend_Mail_Protocol_Exception $e) {
                // let the protocol be rebuilt
            }
        }

        // Zend Framework does not support this out of teh box.. d'oh

       $config = array(
            'user'     => $account->usernameInbox,
            'host'     => $account->serverInbox,
            'password' => $account->passwordInbox,
            'port'     => $account->portInbox,
            'ssl'      => false
        );

        $cType = $account->inboxConnectionType;

        if ($cType == 'SSL' || $cType == 'TLS') {
            $config['ssl'] = $cType;
        }

        /**
         * @see Conjoon_Mail_Protocol_Imap
         */
        require_once 'Conjoon/Mail/Protocol/Imap.php';

        $protocol = new Conjoon_Mail_Protocol_Imap();
        $protocol->connect($config['host'], $config['port'], $config['ssl']);
        if (!$protocol->login($config['user'], $config['password'])) {

            /**
             * @see Conjoon_Modules_Groupware_Email_Exception
             */
            require_once 'Conjoon/Modules/Groupware/Email/Exception.php';

            throw new Conjoon_Modules_Groupware_Email_Exception('cannot login, user or password wrong');
        }


        self::$_imapProtocolCache[$account->id] = $protocol;

        return $protocol;
    }

    /**
     * Splits the passed argument into it's parts and returns it as an array.
     * The splitter is the hierarchy delimiter of the passed imap account.
     *
     * @param Conjoon_Modules_Groupware_Email_Account_Dto $account
     * @param string $path
     *
     * @throws Conjoon_Modules_Groupware_Email_Exception if the account is not
     * an IMAP account, or if a connection to the host specified in the account
     * could not be established or if no delim could be found
     */
    public static function splitFolderForImapAccount(
        $path, Conjoon_Modules_Groupware_Email_Account_Dto $account)
    {
        $delim = self::getFolderDelimiterForImapAccount($account);

        return explode($delim, $path);
    }

    /**
     * Returns the delimiter for the specified account. If the account is
     * not using the IMAP protocol, an exception will be thrown.
     *
     * @param Conjoon_Modules_Groupware_Email_Account_Dto $account
     *
     * @return String
     *
     * @throws Conjoon_Modules_Groupware_Email_Exception if the account is not
     * an IMAP account, or if a connection to the host specified in the account
     * could not be established or if no delim could be found
     */
    public static function getFolderDelimiterForImapAccount(
        Conjoon_Modules_Groupware_Email_Account_Dto $account)
    {
        if ($account->protocol !== 'IMAP') {

            /**
             * @see Conjoon_Modules_Groupware_Email_Exception
             */
            require_once 'Conjoon/Modules/Groupware/Email/Exception.php';

            throw new Conjoon_Modules_Groupware_Email_Exception(
                "The given account with the name \"".$account->name."\" "
                . "does not use the IMAP protocol"
            );
        }

        $protocol = self::reuseImapProtocolForAccount($account);

        if (isset(self::$_delimiterCache[$account->id])) {
            return self::$_delimiterCache[$account->id];
        }

        $mailboxes = $protocol->listMailbox();

        $delim = "";

        foreach ($mailboxes as $globalName => $data) {
            if (isset($data['delim'])) {
                $delim = $data['delim'];
                break;
            }
        }

        if ($delim == "") {
            /**
             * @see Conjoon_Modules_Groupware_Email_Exception
             */
            require_once 'Conjoon/Modules/Groupware/Email/Exception.php';

            throw new Conjoon_Modules_Groupware_Email_Exception(
                "No delimiter for account \"".$account->name."\" found."
            );
        }

        self::$_delimiterCache[$account->id] = $delim;

        return $delim;
    }

    /**
     * Transforms an imap Zend_Mail_Storage_Folder to an instance
     * of Conjoon_Modules_Groupware_Email_Folder_Dto.
     *
     * @param Zend_Mail_Storage_Folder $folder
     * @param boolean $lookupStandardNames Whether standard names like "INBOX"
     * and trash should be looked up and addiotnal properties adjusted for them.
     * @param array $config A preset set of config properties which are used
     * for the properties of the created FolderDto. By now, only the id, the
     * idForPath and the pendingCount property  are considered.
     *
     * @return Conjoon_Modules_Groupware_Email_Folder_Dto
     */
    public static function transformToFolderDto(
        Zend_Mail_Storage_Folder $folder, $lookupStandardNames = false,
        Array $config = array())
    {
        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Dto
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Dto.php';

        $tmpFolder = new Conjoon_Modules_Groupware_Email_Folder_Dto();

        // go through predefined variables
        if (isset($config['id'])) {
            $tmpFolder->id = $config['id'];
        } else {
            $tmpFolder->id = $folder->getGlobalName();
        }

        if (isset($config['idForPath'])) {
            $tmpFolder->idForPath = $config['idForPath'];
        } else {
            $tmpFolder->idForPath = $folder->getGlobalName();
        }

        if (isset($config['pendingCount'])) {
            $tmpFolder->pendingCount = $config['pendingCount'];
        }

        $tmpFolder->type           = 'folder';
        $tmpFolder->name           = $folder->getLocalName();
        $tmpFolder->isChildAllowed = 1;
        $tmpFolder->isLocked       = 0;
        //hasChildren doesnt seem to work
        $tmpFolder->childCount     = $folder->isLeaf() ? 0 : 1;
        $tmpFolder->isSelectable   = $folder->isSelectable() ? 1 : 0;

        // check whether we adjust properties based on standard names
        if ($lookupStandardNames) {
            if (strtolower($tmpFolder->name) === "inbox") {
                //$tmpFolder->type     = 'inbox';
                //$tmpFolder->isLocked = 1;
            } else if (strtolower($tmpFolder->name) === "trash") {
                //$tmpFolder->type     = 'trash';
               // $tmpFolder->isLocked = 1;
            }

        }

        return $tmpFolder;
    }

}