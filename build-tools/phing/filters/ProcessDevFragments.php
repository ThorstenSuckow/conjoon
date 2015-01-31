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
 * Removes the following strings:
 *
 * "<!--@BUILD_ACTIVE@" and "@BUILD_ACTIVE@-->"
 *  "\/*@BUILD_ACTIVE@*\/"
 * "<?php \/*@BUILD_ACTIVE@" and  "@BUILD_ACTIVE@*\/ ?>"
 *
 * Example:
 *
 * <pre>
 *   <!--@BUILD_ACTIVE@
 *    <div style="display:block">Now we are live</div>
 *   @BUILD_ACTIVE@-->
 * </pre>
 *
 * becomes
 *
 * <pre>
 *    <div style="display:block">Now we are live</div>
 * </pre>
 *
 * Removes everything that is in between
 *
 *  \/*@REMOVE@*\/ or ;@REMOVE@;
 *
 * Example:
 * <pre>
 *  <?php \/*@REMOVE@*\/ ?>
 *   <!-- base -->
 *  <script type="text/javascript" src="/js/extjs/adapter/ext/ext-base.js"></script>
 *  <script type="text/javascript" src="/js/extjs/ext-all-debug.js"></script>
 *  <!-- ^^ EO base -->
 *  <?php \/*@REMOVE@*\/ ?>
 * </pre>
 *
 * becomes
 *
 * <pre>
 *  <?php?>
 * </pre>
 *
 * (The remaining "<?php?>" will be cleaned up later, though)
 *
 *
 * @author    Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 * @see       FilterReader
 */
class ProcessDevFragments extends BaseFilterReader implements ChainableReader {

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
            "/\/\*@REMOVE@\*\/(.*?)\/\*@REMOVE@\*\//ims",
            "",
            $output
        );

        $output = preg_replace(
            "/;@REMOVE@;(.*?);@REMOVE@;/ims",
            "",
            $output
        );

        $output = trim(str_replace(
            array(
                '<!--@BUILD_ACTIVE@',
                '@BUILD_ACTIVE@-->',
                '/*@BUILD_ACTIVE@*/',
                '<?php /*@BUILD_ACTIVE@',
                '@BUILD_ACTIVE@*/ ?>',
                '/*@BUILD_ACTIVE@',
                '@BUILD_ACTIVE@*/'
            ),
            "",
            $output
        ));

        unlink($file->getAbsolutePath());

        $this->processed = true;

        return $output;
    }

    /**
     * Creates a new ProcessDevFragments using the passed in
     * Reader for instantiation.
     *
     * @param reader A Reader object providing the underlying stream.
     *               Must not be <code>null</code>.
     *
     * @return a new filter based on this configuration, but filtering
     *         the specified reader
     */
    public function chain(Reader $reader) {
        $newFilter = new ProcessDevFragments($reader);
        $newFilter->setProject($this->getProject());
        return $newFilter;
    }
}
