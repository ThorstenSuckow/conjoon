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
