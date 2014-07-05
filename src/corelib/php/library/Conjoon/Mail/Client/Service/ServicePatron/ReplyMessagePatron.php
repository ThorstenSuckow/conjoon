<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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

use Conjoon\Lang\MissingKeyException,
    Conjoon\Argument\ArgumentCheck;

/**
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

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
 * A service patron for reading an email message.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ReplyMessagePatron
    extends \Conjoon\Mail\Client\Service\ServicePatron\AbstractServicePatron {

    /**
     * @var \Conjoon\Mail\Client\Account\AccountService
     */
    protected $accountService;

    /**
     * @var \Conjoon_Text_Parser_Mail_EmailAddressIdentityParser
     */
    protected $identityParser;

    /**
     * @var boolean
     */
    protected $replyAll;

    /**
     * Creates a new instance of this class.
     *
     * @param \Conjoon\Mail\Client\Account\AccountService $accountService
     * @param boolean $replyAll whether the patrong shpuld prepare the message
     *                to reply to all recipients
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function __construct(
        \Conjoon\Mail\Client\Account\AccountService $accountService,
        $replyAll = false
        )
    {
        $data = array('replyAll' => $replyAll);

        ArgumentCheck::check(array(
            'replyAll' => array(
                'type'      => 'boolean',
                'allowEmpty' => false
            )), $data);

        $this->replyAll = $data['replyAll'];

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

            $ccList = $this->buildAddresses($this->v('cc', $d));
            $toList = $this->buildAddresses($this->v('to', $d));

            $usedAccount = $this->guessAccountUsed(
                array_merge($toList, $ccList)
            );

            // build "cc" before "to" since "to" gets overwritten later on
            $d['cc'] = $this->buildCcAddressList(
                $ccList, $toList, $usedAccount
            );

            $d['to'] = $this->getToAddress(
                $this->v('from', $d), $this->v('replyTo', $d)
            );

            $d['bcc'] = array();




            $d['attachments'] = array();

            $messageId = $this->v('messageId', $d);

            $d['inReplyTo']  = $messageId;
            $d['references'] = $this->v('references', $d) != ''
                               ? $d['references'] . ' ' . $messageId
                               : $messageId;


            $d['groupwareEmailAccountsId'] = null;

            if ($usedAccount) {
                $d['groupwareEmailAccountsId'] = $usedAccount->getId();
            }

            /**
             * @see \Conjoon_Filter_StringPrependIf
             */
            require_once 'Conjoon/Filter/StringPrependIf.php';

            $prep = new \Conjoon_Filter_StringPrependIf(array(
                'Re: ', 'RE: ', 'Aw: ', 'AW: '
            ), 'Re: ');

            $d['subject'] = $prep->filter($this->v('subject', $d));

            $d['contentTextPlain'] = $this->getContentTextPlain(
                $this->v('contentTextPlain', $d)
            );
            $d['contentTextHtml']  = "";

            unset($d['messageId']);
            unset($d['replyTo']);
            unset($d['from']);

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
     * @param string $cc
     * @param string $to
     * @param \Conjoon\Data\Entity\Mail\MailAccountEntity $usedAccount The mail account
     *        which is most likely used to answer to this message
     *
     * @return array
     */
    protected function buildCcAddressList(array $cc, array $to,
        \Conjoon\Data\Entity\Mail\MailAccountEntity $usedAccount = null)
    {
        if (!$this->replyAll) {
            return array();
        }

        $accounts = $this->accountService->getMailAccounts();

        $filterAddressList = array();

        for ($i = 0, $len = count($accounts); $i < $len; $i++) {
            if ($usedAccount && $usedAccount->getId() == $accounts[$i]->getId()) {
                $filterAddressList[] = $accounts[$i]->getAddress();
                break;
            }
        }

        $merge = array();
        for ($i = 0, $len = max(count($cc), count($to)); $i < $len; $i++) {
            if (isset($cc[$i]) && !in_array($cc[$i]['address'], $filterAddressList)) {
                $merge[] = $cc[$i];
            }
            if (isset($to[$i]) && !in_array($to[$i]['address'], $filterAddressList)) {
                $merge[] = $to[$i];
            }
        }


        return $merge;
    }

    /**
     * Tries to guess the account being used by comparing recipients
     * adresses with adresses found in the mail accounts configured for the
     * user. If the account could not be guessed, tdard mail account for
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


    /**
     * @param string $text
     *
     * @return string
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

        $startTag = "<blockquote>";
        $endTag   = "</blockquote>";

        return $startTag.
            $plainToHtmlFilter->filter(
                $quoteFilter->filter(
                    $text
                )
            )
            . $endTag;
    }


    /**
     *
     * @param string $text
     *
     * @return array
     */
    protected function getToAddress($text, $replyToText)
    {
        if ($replyToText != "") {
            $text = $replyToText;
        }

        return $this->buildAddresses($text);
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

}
