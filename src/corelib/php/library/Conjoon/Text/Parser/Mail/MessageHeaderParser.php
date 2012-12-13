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
 * @see Conjoon_Text_Parser
 */
require_once 'Conjoon/Text/Parser.php';

/**
 * Parses a message header text and returns an array with the following
 * key/value pairs:
 * from
 * replyTo
 * to
 * subject
 * date
 * cc
 * references
 * inReplyTo
 *
 * Example:
 *
 * Input
 * =====
 * From: toaddress@domain.tld
 * Reply-To: replyname@domain.tld
 *.To: demo-registration@conjoon.org
 * Subject:  . . . reg for [someuser@domainname.tld]
 * Date: Mon, 19 Nov 2012 13:01:38 +0100
 * Content-Type: text/plain; charset=iso-8859-1
 * Content-Transfer-Encoding: quoted-printable
 * Content-Disposition: inline
 * MIME-Version: 1.0
 * Message-Id: <uniqueid@somegatewy.tld>
 *
 * Output
 * ======
 * array(
 *     'from'       => 'toaddress@domain.tld',
 *     'replyTo'    => 'replyname@domain.tld',
 *     'to'         => 'demo-registration@conjoon.org',
 *     'subject'    => '. . . reg for [someuser@domainname.tld]',
 *     'date'       => 'Mon, 19 Nov 2012 13:01:38 +0100',
 *     'cc'         => '',
 *     'references' => '',
 *     'inReplyTo'  => ''
 * )
 *
 *
 * @uses Conjoon_Text_Parser
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Parser_Mail_MessageHeaderParser extends Conjoon_Text_Parser {

    const ICONV_OLD   = 'old';
    const ICONV_UTF_8 = 'UTF-8';

    /**
     * @inherit Conjoon_Text_Parser::parse
     */
    public function parse($input)
    {
        $data = array('header' => $input);

        /**
         * @see Conjoon_Text_Parser_Exception
         */
        require_once 'Conjoon/Text/Parser/Exception.php';


        /**
         * @see Conjoon_Argument_Check
         */
        require_once 'Conjoon/Argument/Check.php';

        Conjoon_Argument_Check::check(array(
            'header' => array(
                'allowEmpty' => false,
                'type'       => 'string'
            )
        ), $data);

        $this->_setIconvEncoding(self::ICONV_UTF_8);

        $message = new Conjoon_Mail_Message(array(
            'headers'    => $data['header'],
            'noToplines' => true,
            'body'       => 'dummy'
        ));

        $emailItem = array();

        $messageId = "";

        try {
            $messageId = $message->messageId;
        } catch (Zend_Mail_Exception $e) {
            // ignore
        }

        $emailItem['messageId'] = $messageId;

        try {
            $emailItem['from'] = $message->from;
        } catch (Zend_Mail_Exception $e) {
            // may be changed to localized header values by anti vir programs
            try  {
                $emailItem['from'] = $message->von;
            } catch (Zend_Mail_Exception $e) {
                $emailItem['from'] = "-";
            }
        }

        if (!isset($emailItem['from'])) {

            $this->_setIconvEncoding(self::ICONV_OLD);

            throw new Conjoon_Text_Parser_Exception(
                "No header with the name \"from\" found."
            );
        }

        $emailItem['subject'] = "";

        // very few emails will come in without a subject.
        try {
            $emailItem['subject'] = $message->subject;
        } catch (Zend_Mail_Exception $e) {
            try {
                // may be changed to localized header values by anti vir programs
                $emailItem['subject'] = $message->betreff;
            } catch (Zend_Mail_exception $e) {
                // ignore
            }
        } catch (Zend_Mail_exception $e) {
            // ignore
        }

        $emailItem['date'] = "";

        // date field will be given presedence
        try {
            $emailItem['date'] = $message->date;
        } catch (Zend_Mail_Exception $e) {
            // ignore
        }

        // if date not found, look up deliveryDate
        if (!$emailItem['date']) {
            try {
                $emailItem['date'] = $message->deliveryDate;
            } catch (Zend_Mail_Exception $e) {
                // ignore
            }

            if (!$emailItem['date']) {
                try {
                    // may be changed to localized header values by anti vir programs
                    $emailItem['date'] = $message->datum;
                } catch (Zend_Mail_Exception $e) {
                    // ignore
                }

                // and one further down to fall back to actual
                // date if none was found
                if (!$emailItem['date']) {
                    /**
                     * @see Zend_Date
                     */
                    require_once 'Zend/Date.php';
                    $zd = new Zend_Date();
                    $emailItem['date'] = $zd->get(Zend_Date::RFC_2822);
                }
            }

        }

        try {
            $emailItem['to'] = $message->to;
        } catch (Zend_Mail_Exception $e) {
            // "to" might not be used, instead "cc" will be probably available
            // then
            $emailItem['to'] = "";
        }

        if (!$emailItem['to']) {
            try {
                // may be changed to localized header values by anti vir programs
                $emailItem['to'] = $message->an;
            } catch (Zend_Mail_Exception $e) {
                // ignore
            }
        }

        try {
            $emailItem['cc'] = $message->cc;
        } catch (Zend_Mail_Exception $e) {
            $emailItem['cc'] = '';
        }

        try {
            $emailItem['references'] = $message->references;
        } catch (Zend_Mail_Exception $e) {
            $emailItem['references'] = '';
        }

        try {
            $emailItem['replyTo'] = $message->replyTo;
        } catch (Zend_Mail_Exception $e) {
            $emailItem['replyTo'] = '';
        }

        try {
            $emailItem['inReplyTo'] = $message->inReplyTo;
        } catch (Zend_Mail_Exception $e) {
            $emailItem['inReplyTo'] = '';
        }

        $this->_setIconvEncoding(self::ICONV_OLD);

        return $emailItem;
    }
    /**
     * @var array
     */
    protected $_oldEncodings = array();

    /**
     * Sets the iconv-internal-encodings, since Zend_Mime does not allow
     * for passing an indivdual charset for decoding.
     * This is a simple helper which allows for either setting the encoding
     * to utf-8 or reset the endoding to the old value.
     *
     * @param string $type
     *
     */
    protected function _setIconvEncoding($type)
    {
        if ($type != self::ICONV_UTF_8) {
            if (!empty($this->_oldEncodings)) {
                iconv_set_encoding('input_encoding',    $this->_oldEncodings['input_encoding']);
                iconv_set_encoding('output_encoding',   $this->_oldEncodings['output_encoding']);
                iconv_set_encoding('internal_encoding', $this->_oldEncodings['internal_encoding']);
            }
        } else {
            if(empty($this->_oldEncodings)) {
                $this->_oldEncodings = array(
                    'input_encoding'    => iconv_get_encoding('input_encoding'),
                    'output_encoding'   => iconv_get_encoding('output_encoding'),
                    'internal_encoding' => iconv_get_encoding('internal_encoding')
                );
            }

            iconv_set_encoding('input_encoding',    'UTF-8');
            iconv_set_encoding('output_encoding',   'UTF-8');
            iconv_set_encoding('internal_encoding', 'UTF-8');
        }
    }

}