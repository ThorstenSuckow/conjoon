<?php
/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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
 * @see Conjoon_Data_LobAccess
 */
require_once 'Conjoon/Data/LobAccess.php';

/**
 * @see Conjoon_Argument_Check
 */
require_once 'Conjoon/Argument/Check.php';

/**
 * @see Conjoon_Util_Array
 */
require_once 'Conjoon/Util/Array.php';

/**
 *
 *
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_File_LobAccess implements Conjoon_Data_LobAccess {

    const OP_MOVE = 1;
    const OP_COPY = 2;

    protected $_filePointers = array();

    protected function _sanitizePath($path)
    {
        return str_replace("\\", "/", $path);
    }


// -------- Conjoon_Data_LobAccess

    /**
     * Moves a Lob.
     * The following properties must be available in the $data array.
     * - from The complete path including the filename that should be
     * copied
     * - to The destination path
     * - name The destination file name
     *
     * @param array $data
     *
     * @return mixed a unique identifier for this moved lob, otherwise
     * null
     *
     * @throws Conjoon_Data_Exception
     */
    public function moveLob(Array $data)
    {
        return $this->_moveOrCopyLob($data, self::OP_MOVE);
    }

    /**
     * Copies a Lob.
     * The following properties must be available in the $data array.
     * - from The complete path including the filename that should be
     * copied
     * - to The destination path
     * - name The destination file name
     *
     * @param array $data
     *
     * @return mixed the filename including the path of the copied file,
     * otherwise null
     *
     * @throws Conjoon_Data_Exception
     */
    public function copyLob(Array $data)
    {
        return $this->_moveOrCopyLob($data, self::OP_COPY);
    }

    /**
     * Deletes a lob.
     * The following properties have to be available in $data:
     * - path The path to the LOB that should be removed
     *
     * @param array $data
     *
     * @return bool true on success, otherwise false
     *
     * @throws Conjoon_Data_Exception
     */
    public function deleteLob(Array $data)
    {
        Conjoon_Argument_Check::check(array(
            'path' => array('type' => 'string')
        ), $data);

        $path = $this->_sanitizePath($data['path']);

        if (@is_dir($path)) {
            throw new Conjoon_Data_Exception(
                "Cannot delete \"$path\" since it does not seem to be a file."
            );
        }

        if (!@file_exists($path)) {
            throw new Conjoon_Data_Exception(
                "File \"$path\" does not seem to exist or is not readable."
            );
        }

        return @unlink($path);
    }

    /**
     * Deletes a lob for an id.
     * The id would be the path of the file to delete.
     *
     * @param string $path
     *
     * @return bool true on success, otherwise false
     *
     * @throws Conjoon_Data_Exception
     */
    public function deleteLobForId($path)
    {
        return $this->deleteLob(array('path' => $path));
    }

    /**
     * Sets the name for a lob.
     * The following properties have to be available in $data
     * path - the path to the lob that has to be renamed
     * name - the new name for the lob, without the path
     *
     *
     * @param array $data
     *
     * @return bool true on success, otherwise false
     *
     * @throws Conjoon_Data_Exception
     */
    public function setLobName(Array $data)
    {
        Conjoon_Argument_Check::check(array(
            'path' => array('type' => 'string'),
            'name' => array('type' => 'string')
        ), $data);

        $path = $this->_sanitizePath($data['path']);
        $to   = $this->_sanitizePath(dirname($path));

        return ($this->_moveOrCopyLob(array(
            'from' => $path,
            'to'   => $to,
            'name' => $data['name']
        ))) !== null ? true : false;
    }

    /**
     * Returns the lob's content.
     * The following properties have to be available in $data:
     * - path the full path to the lob
     *
     * @param array $data
     *
     * @return string
     *
     * @throws Conjoon_Data_Exception
     */
    public function getLobContent(Array $data)
    {
        Conjoon_Argument_Check::check(array(
            'path' => array('type' => 'string')
        ), $data);

        $path = $this->_sanitizePath($data['path']);

        if (@file_exists($path) && @is_dir($path)) {
            throw new Conjoon_Data_Exception(
                "File \"$path\" is a directory."
            );
        }

        if (!@file_exists($path)) {
            throw new Conjoon_Data_Exception(
                "File \"$path\" does not seem to exist or is not readable."
            );
        }

        $c = file_get_contents($path);

        if ($c === false) {
            throw new Conjoon_Data_Exception(
                "Cannot read from \"$path\"."
            );
        }

        return $c;
    }

    /**
     * Returns the meta data for the lob.
     * The following properties must exist in $data:
     * - path The complete path to the lob
     * - includeResource optional, if the lob should be returned as a string,
     * too
     *
     * @param array $data
     *
     * @return array An array with the following properties:
     *  - dirname
     *  - basename
     *  - extension
     *  - filename
     *  - resource with the plain content of the lob, if
     * includeResource was set to true
     * or null if the path was not valid
     *
     * @throws Conjoon_Data_Exception
     */
    public function getLobData(Array $data)
    {
        Conjoon_Argument_Check::check(array(
            'path' => array('type' => 'string')
        ), $data);

        $path = $this->_sanitizePath($data['path']);

        if (!@file_exists($path)) {
            throw new Conjoon_Data_Exception(
                "File \"$path\" does not seem to exist."
            );
        }

        if (@is_dir($path)) {
            throw new Conjoon_Data_Exception(
                "Path \"$path\" seems to point to a directory."
            );
        }
        $lobData = array();

        Conjoon_Util_Array::apply($lobData, pathinfo($path));

        if (isset($data['includeResource']) && $data['includeResource']) {
            $fc = @file_get_contents($path);

            $lobData['resource'] = ($fc !== false ? $fc : "");
            $fc = null;
        }

        return $lobData;
    }

    /**
     * Returns a stream resource for a lob.
     * The following properties must exist in $data:
     * - path The complete path to the lob
     *
     * @param array $data
     *
     * @return resource|null
     *
     * @throws Conjoon_Data_Exception
     * @see isStreamAccessSupported
     */
    public function getLobAsStream(Array $data)
    {
        if (!$this->isStreamAccessSupported()) {
            throw new Conjoon_Data_Exception(
                "Stream access for ".get_class(self)." is not supported."
            );
        }

        Conjoon_Argument_Check::check(array(
            'path' => array('type' => 'string')
        ), $data);

        $path = $this->_sanitizePath($path);

        if (!@file_exists($path)) {
            throw new Conjoon_Data_Exception(
                "File \"$path\" does not seem to exist."
            );
        }

        if (@is_dir($path)) {
            throw new Conjoon_Data_Exception(
                "Path \"$path\" seems to point to a directory."
            );
        }

        $fp = @fopen($path);

        if ($fp === false) {
            throw new Conjoon_Data_Exception(
                "Cannot create resource for \"$path\"."
            );
        }

        $this->_filePointers[] = $fp;

        return $fp;
    }

    /**
     * Returns true if this class is capable of returning a lob as a
     * stream, otherwise false
     *
     * @return bool
     */
    public function isStreamAccessSupported()
    {
        return true;
    }

    /**
     * Returns true if this class is capable of writing a lob as a
     * stream, otherwise false
     *
     * @return bool
     */
    public function isStreamWritingSupported()
    {
        return true;
    }

    /**
     * Saves the lob specified in $data
     * The following properties must exist in $data
     * - path The complete path to the new lob that should get created
     * - resource The content for this lob. Can be a resource or a
     * string
     *
     *
     * @param array $data
     *
     * @return string The path to the newly created lob, or null if not
     * successfull
     *
     * @throws Conjoon_Data_Exception
     */
    public function addLob(Array $data)
    {
        Conjoon_Argument_Check::check(array(
            'path' => array('type' => 'string')
        ), $data);

        if (!isset($data['resource'])) {
            throw new Conjoon_Data_Exception(
                "Property \"resource\" not set."
            );
        }

        if (is_resource($data['resource'])) {
            return $this->_addLobFromStream($data);
        }

        $path = $this->_sanitizePath($data['path']);

        if (@file_exists($path)) {
            throw new Conjoon_Data_Exception(
                "File \"$path\" already exists."
            );
        }

        if (@is_dir($path)) {
            throw new Conjoon_Data_Exception(
                "Path \"$path\" seems to point to a directory."
            );
        }

        $succ = @file_put_contents($path, $content);

        if ($succ === false) {
            throw new Conjoon_Data_Exception(
                "Could not add contents to \"$path\"."
            );
        }

        return $path;
    }

    /**
     * Adds the lob from a stream.
     * - path the target path
     * - resource the source resource
     *
     *
     * @param array $data
     *
     * @return mixed a unique identifier for this lob, or null
     *
     * @throws Conjoon_Data_Exception
     * @see isStreamWritingSupported
     */
    public function addLobFromStream(Array $data)
    {
        if (!$this->isStreamWritingSupported()) {
            throw new Conjoon_Data_Exception(
                "Stream writing for ".get_class(self)." is not supported."
            );
        }

        Conjoon_Argument_Check::check(array(
            'path' => array('type' => 'string')
        ), $data);

        if (!isset($data['resource'])) {
            throw new Conjoon_Data_Exception(
                "Property \"resource\" not set."
            );
        }

        if (!is_resource($data['resource'])) {
            throw new Conjoon_Data_Exception(
                "Property \"resource\" is not a resource."
            );
        }

        $path = $this->_sanitizePath($data['path']);

        if (@file_exists($path)) {
            throw new Conjoon_Data_Exception(
                "File \"$path\" already exists."
            );
        }

        if (@is_dir($path)) {
            throw new Conjoon_Data_Exception(
                "Path \"$path\" seems to point to a directory."
            );
        }

        $fp = @fopen($path, 'wb');

        if (!$fp) {
            throw new Conjoon_Data_Exception(
                "Cannot read from \"$path\"."
            );
        }

        while (!@feof($data['resource'])) {
            @fwrite($fp, @fread($data['resource'], 1024));
        }
        @fclose($fp);

        return $path;
    }

// -------- api

    protected function _moveOrCopyLob(Array $data, $type)
    {
        Conjoon_Argument_Check::check(array(
            'from' => array('type' => 'string'),
            'to'   => array('type' => 'string'),
            'name' => array('type' => 'string')
        ), $data);

        $from = $this->_sanitizePath($data['from']);
        $to   = $this->_sanitizePath($data['to']);
        $name = $data['name'];

        if (!@file_exists($from)) {
            throw new Conjoon_Data_Exception(
                "File \"$from\" does not seem to exist or is not readable."
            );
        }

        if (!@is_dir($to)) {
            throw new Conjoon_Data_Exception(
                "Path \"$to\" does not seem to exist or is not a directory."
            );
        }

        $finalPath = $to . '/' . $name;

        if (@file_exists($finalPath)) {
            throw new Conjoon_Data_Exception(
                "File \"$finalPath\" already exists."
            );
        }

        if ($type === self::OP_COPY) {
            $succ = @copy($from, $finalPath);
        } else if ($type === self::OP_MOVE) {
            $succ = @rename($from, $finalPath);
        } else {
            throw new Conjoon_Data_Exception(
                "Unknown operation type - \"$type\"."
            );
        }

        if ($succ === false) {
            return null;
        }

        return $finalPath;
    }

    public function __destruct()
    {
        for ($i = 0, $len = count($this->_filePointers); $i < $len; $i++) {
            @fclose($this->_filePointers[$i]);
        }
    }

}