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

use Conjoon\Lang\MissingKeyException;

/**
 * A service patron for reading an email message.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ReadMessagePatron
    extends \Conjoon\Mail\Client\Service\ServicePatron\AbstractServicePatron {

    /**
     * @var \Conjoon_Text_Parser_Mail_EmailAddressIdentityParser
     */
    protected $identityParser;

    /**
     * @var \Conjoon\Mail\Client\Message\Strategy\ReadableStrategy
     */
    protected $readableStrategy;

    /**
     * Creates a new instance of ReadMessagePatron
     *
     * @param \Conjoon\Mail\Client\Service\ServicePatron\Strategy\ReadStrategy $readStrategy
     *        The read strategy for this message patron
     */
    public function __construct(
        \Conjoon\Mail\Client\Message\Strategy\ReadableStrategy $readableStrategy) {
        $this->readableStrategy = $readableStrategy;
    }

    /**
     * Returns the readStrategy used for this message patron.
     *
     * @return \Conjoon\Mail\Client\Message\Strategy\ReadableStrategy
     */
    public function getReadableStrategy() {
        return $this->readableStrategy;
    }

    /**
     * @inheritdoc
     */
    public function applyForData(array $data)
    {
        try {

            $this->v('message', $data);

            $d =& $data['message'];

            $d['isPlainText'] = 1;
            $strategyResult = $this->getReadableStrategy()->execute($data);
            $d['body'] = $strategyResult->getBody();

            $d['hasResources'] = false;
            $d['resourcesLoaded'] = false;

            if ($strategyResult->hasExternalResources()) {
                $d['hasResources'] = true;
                $d['resourcesLoaded'] = $strategyResult->areExternalResourcesLoaded();
            }

            $d['attachments'] = $this->createAttachments($this->v('attachments', $d));

            /**
             * @see \Conjoon_Date_Format
             */
            require_once 'Conjoon/Date/Format.php';

            $date = $this->v('date', $d);
            $date = $date ? $date->format('Y-m-d H:i:s') : null;

            $d['date'] = \Conjoon_Date_Format::utcToLocal($date);

            $d['to']      = $this->createAddressList($this->v('to', $d));
            $d['cc']      = $this->createAddressList($this->v('cc', $d));
            $d['from']    = $this->createAddressList($this->v('from', $d));
            $d['bcc']     = $this->createAddressList($this->v('bcc', $d));
            $d['replyTo'] = $this->createAddressList($this->v('replyTo', $d));

            /**
             * @see \Zend_Filter_HtmlEntities
             */
            require_once 'Zend/Filter/HtmlEntities.php';

            $htmlEntitiesFilter = new \Zend_Filter_HtmlEntities(array(
                'quotestyle' => ENT_COMPAT
            ));

            $d['subject'] = $htmlEntitiesFilter->filter($this->v('subject', $d));

            unset($d['contentTextPlain']);
            unset($d['contentTextHtml']);

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

        for ($i = 0, $len = count($attachments); $i < $len; $i++) {
            $att =& $attachments[$i];

            $data[] = array(
                'fileName' => $att['fileName'],
                'mimeType' => $att['mimeType'],
                'key'      => $att['key']
            );
        }

        return $data;
    }

    /**
     *
     * @param string $text
     *
     * @return array
     */
    protected function createAddressList($text)
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

        return array(
            'addresses' => $addresses
        );

    }
}
