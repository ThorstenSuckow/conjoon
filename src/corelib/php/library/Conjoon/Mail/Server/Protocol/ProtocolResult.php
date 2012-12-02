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


namespace Conjoon\Mail\Server\Protocol;

/**
 * Encapsulates teh result of a Protocol operation. Implementing classes must be
 * able to provide a string-, an array- and a json representation of the result.
 * Implementing classes should tag their model with either ErrorResult or
 * SuccessResult to distinguish between a proper executed protocol command and
 * an erroneous result.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface ProtocolResult {


    /**
     * Returns an array representation of the result.
     *
     * @return array
     */
    public function toArray();

    /**
     * Returns a json representation of the result.
     *
     * @return string
     */
    public function toJson();

    /**
     * Returns a string representation of the result.
     *
     * @return string
     */
    public function __toString();

}