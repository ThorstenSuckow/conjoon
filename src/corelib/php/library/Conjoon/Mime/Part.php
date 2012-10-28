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
 * Allows for passing content which is already encoded. Usually, the getContent
 * method from Zend_Mime_Party would encode the content since $this->encoding
 * denotes the encoding that should happen, not the encoding the content is
 * already available.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Mime_Part extends Zend_Mime_Part {

    protected $_alreadyEncoded = false;

    /**
     * create a new Mime Part.
     * The (unencoded) content of the Part as passed
     * as a string or stream
     *
     * @param mixed $content  String or Stream containing the content
     */
    public function __construct($content, $alreadyEncoded = false)
    {
        parent::__construct($content);

        $this->_alreadyEncoded = $alreadyEncoded;
    }

    /**
     * if this was created with a stream, return a filtered stream for
     * reading the content. very useful for large file attachments.
     * if $this->_alreadyEncoded is set to true, teh stream will be returned
     * without further filtering.
     *
     * @return stream
     * @throws Zend_Mime_Exception if not a stream or unable to append filter
     */
    public function getEncodedStream()
    {
        if (!$this->_isStream) {
            require_once 'Zend/Mime/Exception.php';
            throw new Zend_Mime_Exception('Attempt to get a stream from a string part');
        }

        if ($this->_alreadyEncoded) {
            return $this->_content;
        }

        return parent::getEncodedStream();
    }

    /**
     * Get the Content of the current Mime Part. This class assumes the
     * content is already encoded when $this->alreadyEncoded is set to true.
     *
     * @return String
     */
    public function getContent($EOL = Zend_Mime::LINEEND)
    {
        if ($this->_isStream) {
            return parent::getContent($EOL);
        } else {
            if ($this->_alreadyEncoded) {
                return $this->_content;
            } else {
                return parent::getContent($EOL);
            }
        }
    }


}