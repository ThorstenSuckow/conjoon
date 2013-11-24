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

namespace Conjoon\Mail\Client\Message\Strategy;


/**
 * A bas class for ReadableStrategy results.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ReadableStrategyResult {

    /**
     * @type string
     */
    protected $body;

    /**
     * @type string
     */
    protected $hasExternalResources = false;

    /**
     * @type string
     */
    protected $areExternalResourcesLoaded = false;


    /**
     * Creates a new instance of this class.
     *
     * @param string $body The readable message body.
     * @param boolean $hasExternalResources whether the body provides embedded external resources
     * @param boolean $areExternalResourcesLoaded whether the body provides embedded external
     * resources and these resources are loaded
     */
    public function __construct(
        $body, $hasExternalResources = false, $areExternalResourcesLoaded = false) {

        $this->body = $body;
        $this->hasExternalResources = $hasExternalResources;
        $this->areExternalResourcesLoaded = $areExternalResourcesLoaded;

    }

    /**
     * Returns the message text as transformed by the strategy which returns
     * an instance of this class.
     *
     * @return string
     */
    public function getBody() {
        return $this->body;
    }
    /**
     * Returns true if the message returned by getBody() contains external resources,
     * otherwise false.
     *
     * @return boolean
     */
    public function hasExternalResources() {
        return $this->hasExternalResources;
    }

    /**
     * Returns true if the message returned by getBody() contains external resources,
     * which are _not_ currently blocked, otherwise false.
     *
     * @return boolean
     *
     * @throws \Conjoon\Mail\Client\Message\NoExternalResourcesAvailableException
     * if this method is called, although the strategy could not find any
     * external resources, i.e. hasExternalResources returns false
     */
    public function areExternalResourcesLoaded() {

        if ($this->hasExternalResources()) {
            return $this->areExternalResourcesLoaded;
        }

        /**
         * @see \Conjoon\Mail\Client\Message\NoExternalResourcesAvailableException
         */
        require_once 'Conjoon/Mail/Client/Message/NoExternalResourcesAvailableException.php';

        throw new \Conjoon\Mail\Client\Message\NoExternalResourcesAvailableException();
    }



}
