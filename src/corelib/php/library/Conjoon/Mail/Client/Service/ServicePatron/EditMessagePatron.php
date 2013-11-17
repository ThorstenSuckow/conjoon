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


namespace Conjoon\Mail\Client\Service\ServicePatron;

use Conjoon\Lang\MissingKeyException;

/**
 * @see \Conjoon\Lang\MissingKeyException
 */
require_once 'Conjoon/Lang/MissingKeyException.php';

/**
 * @see \Conjoon\Mail\Client\Service\ServicePatron\AbstractServicePatron
 */
require_once 'Conjoon/Mail/Client/Service/ServicePatron/AbstractServicePatron.php';

/**
 * @see \Conjoon\Mail\Client\Service\ServicePatron\ServicePatronException
 */
require_once 'Conjoon/Mail/Client/Service/ServicePatron/ServicePatronException.php';

/**
 * A service patron for editing an email message.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class EditMessagePatron
    extends \Conjoon\Mail\Client\Service\ServicePatron\AbstractServicePatron {

    /**
     * @var \Conjoon_Text_Parser_Mail_EmailAddressIdentityParser
     */
    protected $identityParser;

    /**
     * @var \Conjoon\Mail\Client\Account\AccountService
     */
    protected $accountService;

    /**
     * Creates a new instance of this class.
     *
     * @param \Conjoon\Mail\Client\Account\AccountService $accountService
    *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function __construct(
        \Conjoon\Mail\Client\Account\AccountService $accountService)
    {
        $this->accountService = $accountService;
    }


    /**
     * @inheritdoc
     */
    public function applyForData(array $data)
    {
        try {

            $this->v('message', $data);

            $d =& $data['message'];

            /**
             * @see \Conjoon_Date_Format
             */
            require_once 'Conjoon/Date/Format.php';

            $date = $this->v('date', $d);
            $date = $date ? $date->format('Y-m-d H:i:s') : null;

            $d['date'] = \Conjoon_Date_Format::utcToLocal($date);

            $d['cc']      = $this->buildAddresses($this->v('cc', $d));
            $d['to']      = $this->buildAddresses($this->v('to', $d));
            $d['bcc']     = $this->buildAddresses($this->v('bcc', $d));
            $d['from']    = $this->buildAddresses($this->v('from', $d));
            $d['replyTo'] = $this->buildAddresses($this->v('replyTo', $d));

            $usedAccount = $this->guessAccountUsed( $d['from']);

            $d['attachments'] = array();


            $d['groupwareEmailAccountsId'] = null;

            if ($usedAccount) {
                $d['groupwareEmailAccountsId'] = $usedAccount->getId();
            }

            $d['contentTextPlain'] = $this->getContentTextPlain(
                $this->v('contentTextPlain', $d)
            );

            $d['contentTextHtml']  = "";

            $data['draft'] = $data['message'];
            unset($data['message']);


        } catch (\Exception $e) {
            throw new ServicePatronException(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            );
        }

        return $data;
    }

// -------- helper

    /**
     * Creates the attachments.
     *
     * @param array
     * @return array
     *
     */
    protected function createAttachments(array $attachments)
    {
        $data = array();

        return $data;
    }

    /**
     *
     * @param $text
     *
     * @return $text
     */
    protected function getContentTextPlain($text)
    {
        /**
         * @see Conjoon_Filter_QuoteToBlockquote
         */
        require_once 'Conjoon/Filter/QuoteToBlockquote.php';

        /**
         * @see Conjoon_Filter_NormalizeLineFeeds
         */
        require_once 'Conjoon/Filter/NormalizeLineFeeds.php';

        /**
         * @see Conjoon_Filter_PlainToHtml
         */
        require_once 'Conjoon/Filter/PlainToHtml.php';

        /**
         * @see Conjoon_Filter_SignatureStrip
         */
        require_once 'Conjoon/Filter/SignatureStrip.php';

        $signatureStripper  = new \Conjoon_Filter_SignatureStrip();
        $quoteFilter        = new \Conjoon_Filter_QuoteToBlockquote();
        $lineFeedFilter     = new \Conjoon_Filter_NormalizeLineFeeds();
        $plainToHtmlFilter  = new \Conjoon_Filter_PlainToHtml();


        /**
         * @see \Zend_Filter_HtmlEntities
         */
        require_once 'Zend/Filter/HtmlEntities.php';

        $entitiesFilter = new \Zend_Filter_HtmlEntities(
            array(
                'quotestyle' => ENT_COMPAT,
                'charset'    => 'UTF-8'
            )
        );

        $text = $signatureStripper->filter(
            $lineFeedFilter->filter(
                $entitiesFilter->filter($text)
            )
        );

        // if signature stripper returned empty messagem, return the data
        if (trim($text) == "") {
            $text = trim($text);
            return $text;
        }

        return $plainToHtmlFilter->filter(
                $quoteFilter->filter(
                    $text
                )
            );
    }

    /**
     *
     * @param string $text
     *
     * @return array
     */
    protected function buildAddresses($text)
    {
        if (!$this->identityParser) {
            /**
             * @see Conjoon_Text_Parser_Mail_EmailAddressIdentityParser
             */
            require_once 'Conjoon/Text/Parser/Mail/EmailAddressIdentityParser.php';

            $this->identityParser = new \Conjoon_Text_Parser_Mail_EmailAddressIdentityParser();
        }

        $res = $this->identityParser->parse($text);

        $addresses = array();

        foreach ($res as $values) {
            $addresses[] = array(
                'address' => isset($values[0]) ? $values[0] : '',
                'name'    => isset($values[1]) ? $values[1] : '',

            );
        }

        return $addresses;
    }

    /**
     * Tries to guess the account being used by comparing recipients
     * adresses with adresses found in the mail accounts configured for the
     * user. If the account could not be guessed, standard mail account for
     * the user will be returned. If this was not successfull, null is returned.
     *
     * @param array $addressList
     *
     * @return null|\Conjoon\Data\Entity\Mail\MailAccountEntity
     */
    protected function guessAccountUsed(array $addressList)
    {
        $accounts = $this->accountService->getMailAccounts();

        $addresses = array();
        $matching = array();
        for ($i = 0, $len = count($accounts); $i < $len; $i++) {
            $add = $accounts[$i]->getAddress();
            $addresses[] = $add;
            $matching[$add] = $accounts[$i];
        }

        for ($i = 0, $len = count($addressList); $i < $len; $i++) {
            if (in_array($addressList[$i]['address'], $addresses)) {
                return $matching[$addressList[$i]['address']];
            }
        }

        return $this->accountService->getStandardMailAccount();

    }

}
