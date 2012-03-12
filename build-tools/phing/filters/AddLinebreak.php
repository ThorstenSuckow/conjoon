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
 * Adds an additional linebreak to the read in files.
 *
 * @author    Thorsten Suckow-Homberg <ts@siteartwork.de>
 * @see       FilterReader
 */
class AddLinebreak extends BaseFilterReader implements ChainableReader {

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
        $output = file_get_contents($file->getAbsolutePath()) . "\r\n";
        unlink($file->getAbsolutePath());

        $this->processed = true;

        return $output;
    }

    /**
     * Creates a new AddLinebreak using the passed in
     * Reader for instantiation.
     *
     * @param reader A Reader object providing the underlying stream.
     *               Must not be <code>null</code>.
     *
     * @return a new filter based on this configuration, but filtering
     *         the specified reader
     */
    public function chain(Reader $reader) {
        $newFilter = new AddLinebreak($reader);
        $newFilter->setProject($this->getProject());
        return $newFilter;
    }
}
