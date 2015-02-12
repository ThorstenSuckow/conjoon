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

namespace Conjoon\Mail\Client\Folder\Strategy;


/**
 * An interface for strategies that need to compute the name of a folder based
 * on a given template.
 * The general purpose is to find a name that is not in an available list of
 * names.
 *
 * Given the following input,
 *
 * Input:
 * ======
 * FolderName: "My Folder"
 * FolderList: ["Test", "Test2", "MyFolder"]
 *
 * Te computed name should be something like "My Folder 2" To make sure
 * "My Folder" is not two times in the list of folder names.
 *
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface FolderNamingForMovingStrategy {

    /**
     * Returns a text based on the specified data found in the
     * passed argument.
     *
     * @param array $data
     *
     * @return \Conjoon\Mail\Client\Folder\Strategy\FolderNamingForMovingStrategyResult
     *
     * @throws \Conjoon\Mail\Client\Message\Folder\StrategyException if executing
     *         the strategy fails
     */
    public function execute(array $data);

}
