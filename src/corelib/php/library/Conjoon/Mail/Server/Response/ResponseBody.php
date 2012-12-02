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


namespace Conjoon\Mail\Server\Response;

/**
 * An interface for a response body which is a simple wrapper for a text.
 * See SuccessResponseBody and ErrorResponseBody for concrete implementations.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface ResponseBody {


    /**
     * Creates a new response body instance.
     *
     * @param string $text
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    public function __construct($text = "");

    /**
     * Returns the response body's text.
     *
     * @return string
     */
    public function getText();

}