<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: de-intrabuild-groupware.css 2 2008-06-21 10:38:49Z T. Suckow $
 * $Date: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $Revision: 2 $
 * $LastChangedDate: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/js/resources/css/de-intrabuild-groupware.css $
 */

/**
 * A simple script that will use the yuicompressor found in the vendor directory
 * to "compress" all css files related directly to the intrabuild project, except
 * for css files loaded from vendor libraries, such as Ext JS or Ext user extensions.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 * @version 0.1.1
 */

$cssPath      = "../src/corelib/js/resources/css";
$yuiPath      = "../vendor/yuicompressor/build/yuicompressor-2.3.4.jar";
$dropFileName = 'intrabuild-all.css';
$dropFilePath = "../src/corelib/js/resources/css/".$dropFileName;

$pathinfo = pathinfo(__FILE__);
$cwd      = $pathinfo['dirname'];

fwrite(STDOUT, "\n"
               ."+--------------------------------------------+\n"
               ."|            csscompressor V0.1.1            |\n"
               ."| CSS Compressor for the intrabuild project. |\n"
               ."+--------------------------------------------+\n\n");

$cssPath      = str_replace("\\", "/", realpath($cwd."/".$cssPath)).'/';
$yuiPath      = str_replace("\\", "/", realpath($cwd."/".$yuiPath));
$dropFilePath = str_replace("\\", "/", realpath($cwd."/".$dropFilePath));

// check if path to css dir exists
$chk1 = "Checking if directory \"$cssPath\" exists...\n";
fwrite(STDOUT, $chk1);
if (!file_exists($cssPath)) {
    fwrite(STDERR, "$cssPath does not seem to exist. Exiting...\n");
    exit();
} else {
    fwrite(STDOUT, "Done.\n");
}

// check if yuicompressor exists
$chk2 = "Checking if file \"$yuiPath\" exists...\n";
fwrite(STDOUT, $chk2);
if (!file_exists($yuiPath)) {
    fwrite(STDERR, "$yuiPath does not seem to exist. Exiting...\n");
    exit();
} else {
    fwrite(STDOUT, "Done.\n");
}

fwrite(STDOUT, "Looking for *.css-files in \"".$cssPath."\"...\n");

$files = array();

if ($handle = opendir($cssPath)) {
    /* Das ist der korrekte Weg, ein Verzeichnis zu durchlaufen. */
    while (false !== ($file = readdir($handle))) {
        if (($file != $dropFileName && $file != '.' && $file != '..' && !is_dir($file))
           && (strpos($file, '.css',1) || strpos($file, '.css',1))) {
           fwrite(STDOUT, "Found \"".$file."\"\n");
           $files[] = $file;
        }
    }
    closedir($handle);
} else {
    fwrite(STDERR, "Could not open \"".$cssPath."\" for reading. Exiting...\n");
    exit();
}

fwrite(STDOUT, "Found ".count($files)." files. Merging now...\n");

if (count($files) == 0) {
    fwrite(STDERR, "No files found in\"".$cssPath."\". Exiting...\n");
    exit();
}

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

fwrite(STDOUT, "Calling yuicompressor...\n");
$cmd = "java.exe -jar \"".$yuiPath."\" --type css \"".$temp."\" -o \"".$dropFilePath."\"";
shell_exec($cmd);
fwrite(STDOUT, "yuicompressor... finished!\n");
fwrite(STDOUT, "Cleaning up...\n");
fwrite(STDOUT, "Removing $temp...\n");
unlink($temp);
fwrite(STDOUT, "Finished!\n");
?>