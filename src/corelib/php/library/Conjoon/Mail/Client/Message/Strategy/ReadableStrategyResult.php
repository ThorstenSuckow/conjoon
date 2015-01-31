<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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
