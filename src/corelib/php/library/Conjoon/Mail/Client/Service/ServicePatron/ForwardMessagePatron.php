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
 * A service patron for forwarding an email message.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ForwardMessagePatron
    extends \Conjoon\Mail\Client\Service\ServicePatron\AbstractServicePatron {

    /**
     * @var \Conjoon\Mail\Client\Account\AccountService
     */
    protected $accountService;

    /**
     * @var Conjoon_Text_Parser_Mail_EmailAddressIdentityParser
     */
    protected $identityParser;

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

            $ccList = $this->buildAddresses($this->v('cc', $d));
            $toList = $this->buildAddresses($this->v('to', $d));

            $usedAccount = $this->guessAccountUsed(
                array_merge($toList, $ccList)
            );

            //getMessageTextHeader($subject, $date, array $from, array $replyTo, array $to, array $cc)

            /**
             * @see \Conjoon_Filter_StringPrependIf
             */
            require_once 'Conjoon/Filter/StringPrependIf.php';

            $prep = new \Conjoon_Filter_StringPrependIf(array(
                'Fwd: ', 'FWD:'
            ), 'Fwd: ');

            $d['subject'] = $prep->filter($this->v('subject', $d));

            $messageTextHead = $this->getMessageTextHeader(
                $d['subject'], $d['date'],
                $this->buildAddresses($this->v('from', $d)),
                $this->buildAddresses($this->v('replyTo', $d)),
                $toList, $ccList
            );

            $d['to']  = array();
            $d['cc']  = array();
            $d['bcc'] = array();

            $d['attachments'] = array();

            $d['inReplyTo']  = "";
            $d['references'] = "";

            $d['groupwareEmailAccountsId'] = null;

            if ($usedAccount) {
                $d['groupwareEmailAccountsId'] = $usedAccount->getId();
            }

            $d['contentTextPlain'] = $messageTextHead . $this->getContentTextPlain(
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
     * @return string
     */
    protected function getMessageTextHeader($subject, $date, array $from,
                                            array $replyTo, array $to, array $cc)
    {


        $str = "<br /><br />------ Original Message ------
            <table>
                <tbody>

                <tr>
                    <td style='text-align:right'>
                        <strong>Subject:</strong>
                    </td>
                    <td>$subject</td>
                </tr>
                <tr>
                    <td style='text-align:right'>
                        <strong>Date:</strong>
                    </td>
                    <td>$date</td>
                </tr>";

        if (isset($from) && isset($from[0])) {
                $str .= "<tr>
                    <td style='text-align:right'>
                        <strong>From:</strong>
                    </td>
                    <td>" .
                        (isset($from[0]['name'])
                            ? $from[0]['name'] .
                                ' &lt;' . $from[0]['address'] . '&gt;'
                            : $from[0]['address'])

                    ."</td>
                </tr>";
        }

        if (isset($replyTo) && count($replyTo)) {

            $str .= "<tr>
                <td style='text-align:right'>
                    <strong>Reply-To:</strong>
                </td>
                <td>";

            $arr = array();
            for ($i = 0, $len = count($replyTo); $i < $len; $i++) {
                $arr[] = isset($replyTo[$i]['name'])
                    ? $replyTo[$i]['name'] .
                        ' &lt;' . $replyTo[$i]['address'] . '&gt;'
                    : $replyTo[$i]['address'];
            }
            $str .= implode(', ', $arr);

            $str .= "</td></tr>";
        }

        if (isset($to) && count($to)) {
            $str .= "<tr>
                <td style='text-align:right'>
                    <strong>To:</strong>
                </td>
                <td>";

                    $arr = array();
                    for ($i = 0, $len = count($to); $i < $len; $i++) {
                        $arr[] = isset($to[$i]['name'])
                            ? $to[$i]['name'] .
                                ' &lt;' . $to[$i]['address'] . '&gt;'
                            : $to[$i]['address'];
                    }
                    $str .= implode(', ', $arr);

            $str .= "</td></tr>";
        }

        if (isset($cc) && count($cc)) {
            $str .= "<tr>
                <td style='text-align:right'>
                    <strong>Cc:</strong>
                </td>
                <td>";

            $arr = array();
            for ($i = 0, $len = count($cc); $i < $len; $i++) {
                $arr[] = isset($cc[$i]['name'])
                    ? $cc[$i]['name'] .
                        ' &lt;' . $cc[$i]['address'] . '&gt;'
                    : $cc[$i]['address'];
            }
            $str .= implode(', ', $arr);

            $str .= "</td></tr>";
        }

        $str .= "</tbody></table>";

        return $str;
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
