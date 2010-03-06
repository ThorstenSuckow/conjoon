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
 * A simple script that will query vendor code for resources with the following
 * file extensions:
 * mp3,swf,js,ico,gif,jpg,png,css, html
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 * @version 0.2
 */

// fetch the version here
require_once '../src/corelib/php/library/Conjoon/Version.php';
$version = Conjoon_Version::VERSION .' '.time();

$resourcePath = "../src/corelib/js/resources/images";
$dropFileName = 'resource-file-list.css';
$dropFilePath = "./";

$pathinfo = pathinfo(__FILE__);

$cwd          = $pathinfo['dirname'];
$resourcePath = str_replace("\\", "/", $resourcePath).'/';
$replace      = array("../src/corelib/js/", "./js/conjoon/");
$dropFilePath = str_replace("\\", "/", realpath($cwd."/".$dropFilePath)) . '/' .$dropFileName;

$resourceConfigs = array(
    'conjoon' => array(
        'config' => array(
            // first section - images only
            array(
                'name'        => 'conjoon - images',
                'dir'         => '../src/corelib/js/resources/images',
                'extensions'  => array('jpg', 'gif', 'jpeg', 'png'),
                'pathReplace' => array("../src/corelib/js/", "./js/conjoon/"),
                'sections'    => array('build', 'dev')
            ),
            // second section - css only
            array(
                'name'        => 'conjoon - css',
                'dir'         => '../src/corelib/js/resources/css',
                'include'     => array('conjoon-all.css'),
                'extensions'  => array('css'),
                'pathReplace' => array("../src/corelib/js/", "./js/conjoon/"),
                'sections'    => array('build', 'dev')
            ),
            // third section - mp3 only
            array(
                'name'        => 'conjoon - mp3',
                'dir'         => '../src/corelib/js/resources/sfx',
                'extensions'  => array('mp3'),
                'pathReplace' => array("../src/corelib/js/", "./js/conjoon/"),
                'sections'    => array('build', 'dev')
            ),
            // fourth section - js only
            array(
                'name'        => 'conjoon - js (dev)',
                'dir'         => '../src/corelib/js/source',
                'extensions'  => array('js'),
                'pathReplace' => array("../src/corelib/js/", "./js/conjoon"),
                'sections'    => array('dev')
            ),
            // fifth section - htdocs folder
            array(
                'name'        => 'conjoon - htdocs',
                'dir'         => '../src/www/htdocs',
                'subdirs'     => false,
                'extensions'  => array('js', 'png', 'html', 'gif'),
                'pathReplace' => array("../src/www/htdocs", "./"),
                'sections'    => array('dev', 'build')
            )
        )
    ),
    'extjs' => array(
        'config' => array(
            // first section - images only
            array(
                'name'        => 'extjs - images',
                'dir'         => '../vendor/extjs/resources/images',
                'extensions'  => array('jpg', 'gif', 'jpeg', 'png'),
                'pathReplace' => array("../vendor/extjs/", "./js/extjs/"),
                'sections'    => array('build', 'dev')
            ),
            // second section - css only
            array(
                'name'        => 'extjs - css',
                'dir'         => '../vendor/extjs/resources/css',
                'extensions'  => array('css'),
                'include'     => array('ext-all.css'),
                'pathReplace' => array("../vendor/extjs/", "./js/extjs/"),
                'sections'    => array('build', 'dev')
            ),
            // third section - swf only
            array(
                'name'        => 'extjs - swf',
                'dir'         => '../vendor/extjs/resources/css',
                'extensions'  => array('swf'),
                'pathReplace' => array("../vendor/extjs/", "./js/extjs/"),
                'sections'    => array('build', 'dev')
            )
        )
    )
);


$cwd      = str_replace("\\", '/', $pathinfo['dirname']);
$validExtensions = array('jpg', 'gif', 'jpeg', 'png', 'css', 'js');

fwrite(STDOUT, "\n"
               ."+----------------------------------------------+\n"
               ."|            resource lister V0.1              |\n"
               ."| File resource lister for the conjoon project |\n"
               ."| for Google Gears manifest files.             |\n"
               ."+----------------------------------------------+\n\n"
               ."Usage: resource-lister.php                      \n\n");

fwrite(STDOUT, "\nProcessing for conjoon $version\n\n");

$foundFiles = array();
$fileCount  = 0;

foreach ($resourceConfigs as $resourceName => $config) {
    fwrite(STDOUT, "Processing \"$resourceName\"...\n");
    $i = 1;
    foreach ($config as $optionLoop) {
        foreach ($optionLoop as $options) {
            $name        = $options['name'];
            $dir         = $options['dir'];
            $extensions  = $options['extensions'];
            $pathReplace = $options['pathReplace'];
            $sections    = (array)$options['sections'];
            $includes    = (isset($options['include']) ? (array)$options['include'] : array());
            $subdirs     = isset($options['subdirs']) ? $options['subdirs'] : true;

            fwrite(STDOUT, "    Processing section \"$name\" ($i) of \"$resourceName\"...\n");
            fwrite(STDOUT, "    Checking if directory \"$dir\" exists...\n");
            if (!file_exists($dir)) {
                fwrite(STDERR, "    $dir does not seem to exist. Exiting...\n");
                exit(-1);
            } else {
                fwrite(STDOUT, "    Done.\n");
            }

            fwrite(STDOUT, "    Looking for \"".implode(',', $extensions)."\"-files in \"".$dir."\" and sub-directories...\n");

            $files = array();
            $dirHandle = opendir($dir);
            lookUpFiles($dirHandle, $dir, $extensions, $pathReplace, $files, $includes, $subdirs);
            fwrite(STDOUT, "    Found ".count($files)." files for section \"$name\" ($i) in $resourceName.\n");
            fwrite(STDOUT, "    Saving results in section(s) ".implode(',', $sections)."...\n");

            $fileCount += count($files);

            for ($i = 0, $len = count($sections); $i < $len; $i++) {

                if ($sections[$i] === 'test') {
                    var_dump($files);
                    die();
                }

                if (!isset($foundFiles[$sections[$i]])) {
                    $foundFiles[$sections[$i]] = array();
                }
                $foundFiles[$sections[$i]] = array_merge($foundFiles[$sections[$i]], $files);
            }

            fwrite(STDOUT, "\n");
            $i++;
        }
    }
}

fwrite(STDOUT, "\n");
fwrite(STDOUT, "Found $fileCount files.\n");


$files = array();

function lookUpFiles($dir_handle, $path, $validExtensions, $replace, &$files, $includes, $subdirs)
{
    while (false !== ($file = readdir($dir_handle))) {
        $dir = $path.'/'.$file;
        if(is_dir($dir) && $file != '.' && $file !='..' ) {

            if (!$subdirs) {
                continue;
            }

            $handle = opendir($dir);
            lookUpFiles($handle, $dir, $validExtensions, $replace, $files, $includes, $subdirs);
        } else if($file != '.' && $file !='..') {

            if (!empty($includes)) {
                if (!in_array($file, $includes)) {
                    continue;
                }
            }

            $pathInfo = pathinfo($file);
            if (isset($pathInfo['extension'])) {
                $extension = strtolower($pathInfo['extension']);
                if (in_array($extension, $validExtensions)) {
                    $files[] = str_replace(
                        array($replace[0], '//'),
                        array($replace[1], '/')
                        , $path . '/' . $file
                    );
                }
            }
        }
    }

    closedir($dir_handle);
}


//    fwrite(STDERR, "Could not open \"".$resourcePath."\" for reading. Exiting...\n");
//    exit(-1);

exit(-1);

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

fwrite(STDOUT, "Calling yuicompressor...\n");
$cmd = "java.exe -jar \"".$yuiPath."\" --type css \"".$temp."\" -o \"".$dropFilePath."\"";
shell_exec($cmd);
fwrite(STDOUT, "yuicompressor... finished!\n");
fwrite(STDOUT, "Cleaning up...\n");
fwrite(STDOUT, "Removing $temp...\n");
unlink($temp);
fwrite(STDOUT, "Finished!\n");
exit(0);
?>