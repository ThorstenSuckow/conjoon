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

require_once 'phing/Task.php';

/**
 * A simple task that was almost copied 1:1 from the ExecTask.
 * It's purpose is to gather information about the head revision
 * number of the specified working copy the task is used in.
 *
 *
 * @author    Thorsten Suckow-Homberg <ts@siteartwork.de>
 * @see       Task
 */
class HeadRevisionTask extends Task {

    /**
     * Command to execute.
     * @var string
     */
    protected $command;

    /**
     * The name of the property to store the revision information
     * in.
     * @var string
     */
    protected $revisionProperty;

    /**
     * Working directory.
     * @var File
     */
    protected $dir;

    /**
     * Operating system.
     * @var string
     */
    protected $os;

    /**
     * Whether to escape shell command using escapeshellcmd().
     * @var boolean
     */
    protected $escape = false;

    /**
     * Where to direct output.
     * @var File
     */
    protected $output;

    /**
     * Whether to passthru the output
     * @var boolean
     */
    protected $passthru = false;

    /**
     * Where to direct error output.
     * @var File
     */
    protected $error;

    /**
     * If spawn is set then [unix] programs will redirect stdout and add '&'.
     * @var boolean
     */
    protected $spawn = false;

    /**
     * Property name to set with return value from exec call.
     *
     * @var string
     */
    protected $returnProperty;

    /**
     * Whether to check the return code.
     * @var boolean
     */
    protected $checkreturn = false;

    /**
     * Main method: wraps execute() command.
     * @return void
     */
    public function main() {
        $this->execute();
    }

    /**
     * Executes a program and returns the return code.
     * Output from command is logged at INFO level.
     * @return int Return code from execution.
     */
    public function execute() {

        // test if os match
        $myos = Phing::getProperty("os.name");
        $this->log("Myos = " . $myos, Project::MSG_VERBOSE);
        if (($this->os !== null) && (strpos($this->os, $myos) === false)) {
            // this command will be executed only on the specified OS
            $this->log("Not found in " . $this->os, Project::MSG_VERBOSE);
            return 0;
        }

        if ($this->dir !== null) {
            if ($this->dir->isDirectory()) {
                $currdir = getcwd();
                @chdir($this->dir->getPath());
            } else {
                throw new BuildException("Can't chdir to:" . $this->dir->__toString());
            }
        }


        $this->command = 'svn info -r HEAD ' . $this->dir;


        if ($this->error !== null) {
            $this->command .= ' 2> ' . $this->error->getPath();
            $this->log("Writing error output to: " . $this->error->getPath());
        }

        if ($this->output !== null) {
            $this->command .= ' 1> ' . $this->output->getPath();
            $this->log("Writing standard output to: " . $this->output->getPath());
        } elseif ($this->spawn) {
            $this->command .= ' 1>/dev/null';
            $this->log("Sending ouptut to /dev/null");
        }

        // If neither output nor error are being written to file
        // then we'll redirect error to stdout so that we can dump
        // it to screen below.

        if ($this->output === null && $this->error === null) {
            $this->command .= ' 2>&1';
        }

        // we ignore the spawn boolean for windows
        if ($this->spawn) {
            $this->command .= ' &';
        }

        $this->log("Getting \"svn info -r HEAD\" on " . $this->dir);

        $output = array();
        $return = null;

        exec($this->command, $output, $return);

        if ($this->dir !== null) {
            @chdir($currdir);
        }

        foreach($output as $line) {
            $this->log($line,  ($this->passthru ? Project::MSG_INFO : Project::MSG_VERBOSE));
        }

        $str = implode("\n", $output);
        $matches = array();
        if (preg_match('/Rev:[\s]+([\d]+)/', $str, $matches) && $this->revisionProperty) {
            $this->project->setProperty($this->revisionProperty, $matches[1]);

            $this->log("HEAD revision on " . $this->dir ." should be " . $matches[1]);
        }

        if ($this->returnProperty) {
            $this->project->setProperty($this->returnProperty, $return);
        }

        if($return != 0 && $this->checkreturn) {
            throw new BuildException("Task exited with code $return");
        }

        return $return;
    }

    /**
     * The command to use.
     * @param mixed $command String or string-compatible (e.g. w/ __toString()).
     */
    function setCommand($command) {
        $this->command = "" . $command;
    }

    /**
     * Whether to use escapeshellcmd() to escape command.
     * @param boolean $escape
     */
    function setEscape($escape) {
        $this->escape = (bool) $escape;
    }

    /**
     * Specify the working directory for executing this command.
     * @param PhingFile $dir
     */
    function setDir(PhingFile $dir) {
        $this->dir = $dir;
    }

    /**
     * Specify OS (or muliple OS) that must match in order to execute this command.
     * @param string $os
     */
    function setOs($os) {
        $this->os = (string) $os;
    }

    /**
     * File to which output should be written.
     * @param PhingFile $output
     */
    function setOutput(PhingFile $f) {
        $this->output = $f;
    }

    /**
     * File to which error output should be written.
     * @param PhingFile $output
     */
    function setError(PhingFile $f) {
        $this->error = $f;
    }

    /**
     * Whether to use passthru the output.
     * @param boolean $passthru
     */
    function setPassthru($passthru) {
        $this->passthru = (bool) $passthru;
    }

    /**
     * Whether to suppress all output and run in the background.
     * @param boolean $spawn
     */
    function setSpawn($spawn) {
        $this->spawn  = (bool) $spawn;
    }

    /**
     * Whether to check the return code.
     * @param boolean $checkreturn
     */
    function setCheckreturn($checkreturn) {
        $this->checkreturn = (bool) $checkreturn;
    }

    /**
     * The name of property to set to return value from exec() call.
     * @param string $prop
     */
    function setReturnProperty($prop) {
        $this->returnProperty = $prop;
    }

    /**
     * The name of property to set to revision value from the exec() call.
     * @param string $prop
     */
    function setRevisionProperty($prop) {
        $this->revisionProperty = $prop;
    }
}

