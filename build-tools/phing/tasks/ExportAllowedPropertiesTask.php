<?php
/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
 * licensing@conjoon.org
 *
 * phing copyright disclaimer:
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://phing.info>.
 *
 *
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */

require_once "phing/Task.php";

/**
 * Saves currently defined properties into a specified file. It overrdes
 * it's parent implementation by giving precedence "allowed" property names,
 * and if the given proeprty name is not identified by this "white list",
 * it will not be saved.
 *
 * @author Andrei Serdeliuc
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 * @uses  ExportPropertiesTask
 */
class ExportAllowedPropertiesTask extends Task
{

    protected static $_time;

    /**
     * Array of project properties
     *
     * (default value: null)
     *
     * @var array
     * @access private
     */
    protected $_properties = null;

    /**
     * Target file for saved properties
     *
     * (default value: null)
     *
     * @var string
     * @access private
     */
    protected $_targetFile = null;

    /**
     * Exclude properties starting with these prefixes
     *
     * @var array
     * @access private
     */
    protected $_disallowedPropertyPrefixes = array(
        'host.',
        'phing.',
        'os.',
        'php.',
        'line.',
        'env.',
        'user.'
    );

    /**
     * Include properties starting with these prefixes
     *
     * @var array
     * @access private
     */
    protected $_allowedPropertyPrefixes = array(
        'global.'
    );

    /**
     * Alias for setAllowedPropertyPrefixes()
     *
     * @access public
     * @param string $file
     * @return bool
     */
    public function setAllowed($prefixes)
    {
        return $this->setAllowedPropertyPrefixes($prefixes);
    }

    /**
     * setter for _allowedPropertyPrefixes
     *
     * @access public
     * @param string $file
     * @return bool
     */
    public function setAllowedPropertyPrefixes($prefixes)
    {
        $this->_allowedPropertyPrefixes = explode(",", $prefixes);
        return true;
    }

    /**
     * setter for _disallowedPropertyPrefixes
     *
     * @access public
     * @param string $file
     * @return bool
     */
    public function setDisallowedPropertyPrefixes($prefixes)
    {
        $this->_disallowedPropertyPrefixes = explode(",", $prefixes);
        return true;
    }

    /**
     * Checks if a property name is disallowed
     *
     * @access protected
     * @param string $propertyName
     * @return bool
     */
    protected function isDisallowedPropery($propertyName)
    {
        foreach($this->_disallowedPropertyPrefixes as $property) {
            if(substr($propertyName, 0, strlen($property)) == $property) {
                return true;
            }
        }

        return false;
    }

    /**
     * setter for _targetFile
     *
     * @access public
     * @param string $file
     * @return bool
     */
    public function setTargetFile($file)
    {
        if(!is_dir(dirname($file))) {
            throw new BuildException("Parent directory of target file doesn't exist");
        }

        if(!is_writable(dirname($file)) && (file_exists($file) && !is_writable($file))) {
            throw new BuildException("Target file isn't writable");
        }

        $this->_targetFile = $file;
        return true;
    }


    public function main()
    {
        $this->_properties = $this->getProject()->getProperties();

        $addDisallow = array();

        if(is_array($this->_properties) && !empty($this->_properties) && null !== $this->_targetFile) {
            foreach($this->_properties as $propertyName => $propertyValue) {
                if(!$this->isAllowedPropery($propertyName)) {
                    $addDisallow[] = $propertyName;
                }
            }
            $addDisallow = array_unique($addDisallow);
            $this->_disallowedPropertyPrefixes = array_unique(
                array_merge($this->_disallowedPropertyPrefixes, $addDisallow)
            );

            $this->__oldMain();
        }


    }

    public function __oldMain()
    {
        if (!self::$_time) {
            self::$_time = microtime();
        }

        // Sets the currently declared properties
        $this->_properties = $this->getProject()->getProperties();

        if(is_array($this->_properties) && !empty($this->_properties) && null !== $this->_targetFile) {
            $propertiesString = '';

            $old = @parse_ini_file($this->_targetFile);

            if ($old === false) {
                throw new BuildException('Failed reading ' . $this->_targetFile);
            }

            if ((!empty($old) && !isset($old['__time__']))
                || (isset($old['__time__']) &&  $old['__time__'] != self::$_time)) {
                    throw new BuildException(
                        'make sure you are working with a fresh version of ' . $this->_targetFile);
            }

            if (empty($old)) {
                $old['__time__'] = self::$_time;
            }

            foreach($this->_properties as $propertyName => $propertyValue) {
                if(!$this->isDisallowedPropery($propertyName)) {
                    $old[$propertyName] = $propertyValue;
                }
            }

            if (!isset($old['__time__.firstAction'])) {
                 $old['__time__.firstAction'] = date("Y-m-d H:i:s", time());
            }

            $old['__time__.lastAction'] = date("Y-m-d H:i:s", time());

            foreach ($old as $key => $value) {
                $propertiesString .= $key ."=". $value . PHP_EOL;
            }

            if(!file_put_contents($this->_targetFile, $propertiesString)) {
                throw new BuildException('Failed writing to ' . $this->_targetFile);
            }


        }
    }

    /**
     * Checks if a property name is allowed
     *
     * @access protected
     * @param string $propertyName
     * @return bool
     */
    protected function isAllowedPropery($propertyName)
    {
        foreach($this->_allowedPropertyPrefixes as $property) {
            if(substr($propertyName, 0, strlen($property)) == $property) {
                return true;
            }
        }

        return false;
    }
}

?>