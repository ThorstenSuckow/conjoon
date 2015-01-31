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

/**
 * @see BaseFilterReader
 */
require_once 'phing/filters/BaseFilterReader.php';

/**
 * @see ChainableReader
 */
require_once 'phing/filters/ChainableReader.php';

/**
 * Processes development files the following way:
 *
 * Processes development files and removes all SVN keywords - except for
 * the keyword "Id" - from the file header:
 *  <pre>
 *  \/**
 *    * conjoon
 *    * (c) 2002-2012 siteartwork.de/conjoon.org
 *    * licensing@conjoon.org
 *    *
 *    * $Author$
 *    * $Id$
 *    * $Date$
 *    * $Revision$
 *    * $LastChangedDate$
 *    * $LastChangedBy$
 *    * $URL$
 *    *\/
 *  </pre>
 *
 * becomes
 *
 *  \/**
 *    * conjoon
 *    * (c) 2002-2012 siteartwork.de/conjoon.org
 *    * licensing@conjoon.org
 *    *
 *    * $Id$
 *    *\/
 *  </pre>
 *
 * (all file headers found at <http://wiki.conjoon.de/wiki/Developers/Guide/Coding/Guidelines/Files/Header>
 * will be altered this way).
 *
 * @author    Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 * @see       FilterReader
 */
class PruneFileHeader extends BaseFilterReader implements ChainableReader {

    private $processed = false;

    /**
     * Returns the stream with an additional linebreak.
     *
     * @return the resulting stream, or -1
     *         if the end of the resulting stream has been reached
     *
     * @throws IOException if the underlying stream throws an IOException
     *                        during reading
     */
    function read($len = null) {

        if ($this->processed === true) {
            return -1; // EOF
        }

        // Read
        $php = null;
        while ( ($buffer = $this->in->read($len)) !== -1 ) {
            $php .= $buffer;
        }

        if ($php === null ) { // EOF?
            return -1;
        }

        if(empty($php)) {
            $this->log("File is empty!", Project::MSG_WARN);
            return ''; // return empty string
        }

        // write buffer to a temporary file, since php_strip_whitespace() needs a filename
        $file = new PhingFile(tempnam(PhingFile::getTempDir(), 'stripwhitespace'));
        file_put_contents($file->getAbsolutePath(), $php);

        $output = $php;

        $output = preg_replace(
            array(
                "/^(( | \* |-- |; |; \* ).(Author|Date|Revision|LastChangedDate|LastChangedBy|URL).*)(\n|\r\n)/m",
                "/<\?php\s*\?>/ims",
            ),
            "",
            $output
        );

        unlink($file->getAbsolutePath());

        $this->processed = true;

        return $output;
    }

    /**
     * Creates a new PruneFileHeader using the passed in
     * Reader for instantiation.
     *
     * @param reader A Reader object providing the underlying stream.
     *               Must not be <code>null</code>.
     *
     * @return a new filter based on this configuration, but filtering
     *         the specified reader
     */
    public function chain(Reader $reader) {
        $newFilter = new PruneFileHeader($reader);
        $newFilter->setProject($this->getProject());
        return $newFilter;
    }
}
