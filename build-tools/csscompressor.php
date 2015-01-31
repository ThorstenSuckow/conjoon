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
 * A simple script that will use the yuicompressor to "compress" all css files related
 * directly to the conjoon project, except for css files loaded from vendor libraries,
 * such as Ext JS or Ext user extensions.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 * @version 0.2
 */
$cssPath      = "../src/corelib/js/resources/css";
$yuiPath      = isset($argv[1]) ? $argv[1] : null;
$dropFileName = 'conjoon-all.css';
$dropFilePath = "../src/corelib/js/resources/css/";

$pathinfo = pathinfo(__FILE__);
$cwd      = $pathinfo['dirname'];

fwrite(STDOUT, "\n"
               ."+--------------------------------------------+\n"
               ."|            csscompressor V0.2              |\n"
               ."|   CSS Compressor for the conjoon project.  |\n"
               ."+--------------------------------------------+\n\n"
               ."Usage: csscompressor.php [path_to_yuicompressor]\n\n");

if (!$yuiPath) {
    fwrite(STDERR, "You must specify the path to yuicompressor. Exiting...\n");
    exit(-1);
}

$cssPath      = str_replace("\\", "/", realpath($cwd."/".$cssPath)).'/';
$dropFilePath = str_replace("\\", "/", realpath($cwd."/".$dropFilePath)) . '/' .$dropFileName;

// check if path to css dir exists
$chk1 = "Checking if directory \"$cssPath\" exists...\n";
fwrite(STDOUT, $chk1);
if (!file_exists($cssPath)) {
    fwrite(STDERR, "$cssPath does not seem to exist. Exiting...\n");
    exit(-1);
} else {
    fwrite(STDOUT, "Done.\n");
}

// check if yuicompressor exists
$chk2 = "Checking if file \"$yuiPath\" exists...\n";
fwrite(STDOUT, $chk2);
if (!file_exists($yuiPath)) {
    fwrite(STDERR, "$yuiPath does not seem to exist. Exiting...\n");
    exit(-1);
} else {
    fwrite(STDOUT, "Done.\n");
}

fwrite(STDOUT, "Looking for *.css-files in \"".$cssPath."\"...\n");

$files   = array();
$prepend = array();

if ($handle = opendir($cssPath)) {
    while (false !== ($file = readdir($handle))) {
        if (($file != $dropFileName && $file != '.' && $file != '..' && !is_dir($file))
           && (strpos($file, '.css',1) || strpos($file, '.css',1))) {

            if ($file === "com-conjoon-groupware.css") {
                fwrite(STDOUT, "Found \"".$file."\", I'm prepending this file later on.\n");
                $prepend[] = $file;
                continue;
            }
            fwrite(STDOUT, "Found \"".$file."\"\n");
            $files[] = $file;
        }
    }
    closedir($handle);
} else {
    fwrite(STDERR, "Could not open \"".$cssPath."\" for reading. Exiting...\n");
    exit(-1);
}

for ($i = 0, $len = count($prepend); $i < $len; $i++) {
    array_unshift($files, $prepend[$i]);
}

fwrite(STDOUT, "Found ".count($files)." files. Merging now...\n");

if (count($files) == 0) {
    fwrite(STDERR, "No files found in\"".$cssPath."\". Exiting...\n");
    exit(-1);
}

$content = "";
for ($i = 0, $len = count($files); $i < $len; $i++) {
    $content .= file_get_contents($cssPath . $files[$i]);
}

$temp = tempnam("/tmp", "csscompr");

fwrite(STDOUT, "Caching in temporary file $temp...\n");

file_put_contents($temp, $content);

if (file_exists($dropFilePath)) {
    fwrite(STDOUT, "Removing previous $dropFilePath...\n");
    unlink($dropFilePath);
}

$java = "java.exe";
if (strtolower(PHP_OS) === "linux") {
    fwrite(STDOUT, "I assume I'm currently running in a *nix environment...\n");
    $java = "java";
}

fwrite(STDOUT, "Calling yuicompressor...\n");
$cmd = "$java -jar \"".$yuiPath."\" --type css \"".$temp."\" -o \"".$dropFilePath."\"";

shell_exec($cmd);
fwrite(STDOUT, "...yuicompressor... finished!\n");
fwrite(STDOUT, "Cleaning up...\n");
fwrite(STDOUT, "Removing $temp...\n");
unlink($temp);
fwrite(STDOUT, "Finished!\n");
exit(0);
?>