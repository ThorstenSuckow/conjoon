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


namespace Conjoon\Mail\Client\Service;

/**
 * Encapsulates the result of a service request Implementing classes must be
 * able to provide a string-, an array- and a json representation of the result.
 * ServiceResults are meant to provide a communication interface between a
 * controller action and a service facade's method.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface ServiceResult {

    /**
     * Returns true if the result indicates a successfull operation, otherwise
     * false.
     *
     * @return boolean
     */
    public function isSuccess();

    /**
     * Returns the data associated with the result.
     * This should be the data as returned by the request made by the service
     * and generalized to fit into an array structure.
     *
     * @return array
     */
    public function getData();

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