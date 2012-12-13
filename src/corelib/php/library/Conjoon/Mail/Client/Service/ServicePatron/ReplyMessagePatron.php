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
     * Creates a new instance of this class.
     *
     * @param \Conjoon\Mail\Client\Account\AccountService $accountService
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

            $d['date'] = \Conjoon_Date_Format::utcToLocal($this->v('date', $d));

            $d['to']  = $this->getToAddress($this->v('from', $d));
            $d['bcc'] = array();
            $d['cc']  = array();

            $d['attachments'] = array();

            $messageId = $this->v('messageId', $d);

            $d['inReplyTo']  = $messageId;
            $d['references'] = $this->v('references', $d) != ''
                               ? $d['references'] . ' ' . $messageId
                               : $messageId;

            $standardAccount = $this->accountService->getStandardMailAccount();

            $d['groupwareEmailAccountsId'] = null;

            if ($standardAccount) {
                $d['groupwareEmailAccountsId'] = $standardAccount->getId();
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


        $data['contentTextPlain'] = $signatureStripper->filter(
            $lineFeedFilter->filter(
                $text
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
    protected function getToAddress($text)
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