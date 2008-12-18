<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
 *    * (c) 2002-2009 siteartwork.de/conjoon.org
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
 *    * (c) 2002-2009 siteartwork.de/conjoon.org
 *    * licensing@conjoon.org
 *    *
 *    * $Id$
 *    *\/
 *  </pre>
 *
 * (all file headers found at <http://wiki.conjoon.de/wiki/Developers/Guide/Coding/Guidelines/Files/Header>
 * will be altered this way).
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
 *  \/*@REMOVE@*\/
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
 * @author    Thorsten Suckow-Homberg <ts@siteartwork.de>
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
        $output = preg_replace(
            "/\/\*@REMOVE@\*\/(.*?)\/\*@REMOVE@\*\//ims",
            "",
            file_get_contents($file->getAbsolutePath())
        );

        $output = preg_replace(
            array(
                "/^(( \*|--|;|; \*) .(Author|Date|Revision|LastChangedDate|LastChangedBy|URL).*)(\n|\r\n)/m",
                "/<\?php\s*\?>/ims",
            ),
            "",
            $output
        );

        $output = trim(str_replace(
            array(
                '<!--@BUILD_ACTIVE@',
                '@BUILD_ACTIVE@-->',
                '/*@BUILD_ACTIVE@*/',
                '<?php /*@BUILD_ACTIVE@',
                '@BUILD_ACTIVE@*/ ?>'
            ),
            "",
            $output
        ));

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
        $newFilter = new RemoveDevTags($reader);
        $newFilter->setProject($this->getProject());
        return $newFilter;
    }
}
