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

fwrite(STDOUT, "\n"
               ."+----------------------------------------------+\n"
               ."|            resource lister V0.1              |\n"
               ."| File resource lister for the conjoon project |\n"
               ."| for Application Cache manifest files.        |\n"
               ."+----------------------------------------------+\n\n"
               ."Usage: resource-lister.php [BUILD_TYPE]        \n"
               ."[BUILD_TYPE] may equal to either\"dev\" or \"build\"\n");

$buildType = isset($argv[1]) ? $argv[1] : null;

if ($buildType != "dev" && $buildType != "build") {
    fwrite(STDERR, "\n\n Error: Please specify the [BUILD_TYPE] parameter.");
    exit(-1);
}

// fetch the version here
require_once '../src/corelib/php/library/Conjoon/Version.php';
$version = Conjoon_Version::VERSION;

$dropFileConfig = array(

    'flash'       => array(
        'build' => '../build/htdocs/install/files/conjoon_application/manifest/flash.list',
        'dev'   => '../src/www/application/manifest/flash.list'
    ),
    'javascript'  => array(
        'build' => '../build/htdocs/install/files/conjoon_application/manifest/javascript.list',
        'dev'   => '../src/www/application/manifest/javascript.list'
    ),
    'sounds'      => array(
        'build' => '../build/htdocs/install/files/conjoon_application/manifest/sounds.list',
        'dev'   => '../src/www/application/manifest/sounds.list'
    ),
    'stylesheets' => array(
        'build' => '../build/htdocs/install/files/conjoon_application/manifest/stylesheets.list',
        'dev'   => '../src/www/application/manifest/stylesheets.list'
    ),
    'html'        => array(
        'build' => '../build/htdocs/install/files/conjoon_application/manifest/html.list',
        'dev'   => '../src/www/application/manifest/html.list'
    ),
    'images'      => array(
        'build' => '../build/htdocs/install/files/conjoon_application/manifest/images.list',
        'dev'   => '../src/www/application/manifest/images.list'
    )

);

$resourceConfigs = array(
    'conjoon' => array(
        'config' => array(
            // first section - images only
            array(
                'name'        => 'conjoon - images',
                'dir'         => '../src/corelib/js/resources/images',
                'extensions'  => array('jpg', 'gif', 'jpeg', 'png'),
                'pathReplace' => array("../src/corelib/js/", "../../js/conjoon/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['images'][$buildType]
            ),
            // second section - css only
            array(
                'name'        => 'conjoon - css',
                'dir'         => '../src/corelib/js/resources/css',
                'include'     => array('conjoon-all.css'),
                'extensions'  => array('css'),
                'pathReplace' => array("../src/corelib/js/", "../../js/conjoon/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['stylesheets'][$buildType]
            ),
            // third section - sounds only
            array(
                'name'        => 'conjoon - mp3',
                'dir'         => '../src/corelib/js/resources/sfx',
                'extensions'  => array('mp3'),
                'pathReplace' => array("../src/corelib/js/", "../../js/conjoon/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['sounds'][$buildType]
            ),
            // fourth section - js only
            array(
                'name'        => 'conjoon - js (dev)',
                'dir'         => '../src/corelib/js/source',
                'extensions'  => array('js'),
                'pathReplace' => array("../src/corelib/js/", "../../js/conjoon/"),
                'sections'    => array('dev'),
                'dropFile'    => $dropFileConfig['javascript'][$buildType]
            ),
            // fifth section - htdocs folder JAVASCRIPT
            array(
                'name'        => 'conjoon - htdocs',
                'dir'         => '../src/www/htdocs',
                'subdirs'     => false,
                'extensions'  => array('js'),
                'pathReplace' => array("../src/www/htdocs", "../../"),
                'sections'    => array('dev', 'build'),
                'dropFile'    => $dropFileConfig['javascript'][$buildType]
            ),
            // fifth section - htdocs folder IMAGES
            array(
                'name'        => 'conjoon - htdocs',
                'dir'         => '../src/www/htdocs',
                'subdirs'     => false,
                'extensions'  => array('jpg', 'gif', 'jpeg', 'png'),
                'pathReplace' => array("../src/www/htdocs", "../../"),
                'sections'    => array('dev', 'build'),
                'dropFile'    => $dropFileConfig['images'][$buildType]
            ),
            // fifth section - htdocs folder HTML
            array(
                'name'        => 'conjoon - htdocs',
                'dir'         => '../src/www/htdocs',
                'subdirs'     => false,
                'extensions'  => array('html'),
                'pathReplace' => array("../src/www/htdocs", "../../"),
                'sections'    => array('dev', 'build'),
                'dropFile'    => $dropFileConfig['html'][$buildType]
            )
        )
    ),
    'vendor' => array(
        'config' => array(

            // soundmanager - javascript only
            array(
                'name'        => 'soundmanager - js',
                'dir'         => '../vendor/soundmanager/script',
                'extensions'  => array('js'),
                'include'     => array('soundmanager2.js'),
                'pathReplace' => array('../vendor/soundmanager', "../../js/soundmanager/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['javascript'][$buildType]
            ),

            // soundmanager - flash only
            array(
                'name'        => 'soundmanager - flash',
                'dir'         => '../vendor/soundmanager/swf',
                'extensions'  => array('swf'),
                'pathReplace' => array('../vendor/soundmanager', "../../js/soundmanager/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['flash'][$buildType]
            ),

            // util-messagebus - javascript only
            array(
                'name'        => 'util-messagebus - js',
                'dir'         => '../vendor/ext-ux-util-messagebus/src',
                'extensions'  => array('js'),
                'pathReplace' => array('../vendor/ext-ux-util-messagebus/src', "../../js/ext-ux-util-messagebus/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['javascript'][$buildType]
            ),


            // layout-slidelayout - javascript only
            array(
                'name'        => 'layout-slidelayout - js',
                'dir'         => '../vendor/ext-ux-layout-slidelayout/src',
                'extensions'  => array('js'),
                'pathReplace' => array('../vendor/ext-ux-layout-slidelayout/src', "../../js/ext-ux-layout-slidelayout/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['javascript'][$buildType]
            ),

            // layout-cardlayout - javascript only
            array(
                'name'        => 'layout-cardlayout - js',
                'dir'         => '../vendor/ext-ux-layout-cardlayout/src',
                'extensions'  => array('js'),
                'pathReplace' => array('../vendor/ext-ux-layout-cardlayout/src', "../../js/ext-ux-layout-cardlayout/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['javascript'][$buildType]
            ),

            // flashcontrol - images only
            array(
                'name'        => 'flashcontrol - images',
                'dir'         => '../vendor/ext-ux-flashcontrol/build',
                'extensions'  => array('jpg', 'gif', 'jpeg', 'png'),
                'pathReplace' => array('../vendor/ext-ux-flashcontrol/build', "../../js/ext-ux-flashcontrol/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['images'][$buildType]
            ),
            // flashcontrol - css only
            array(
                'name'        => 'flashcontrol - css',
                'dir'         => '../vendor/ext-ux-flashcontrol/build',
                'extensions'  => array('css'),
                'pathReplace' => array('../vendor/ext-ux-flashcontrol/build', "../../js/ext-ux-flashcontrol/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['stylesheets'][$buildType]
            ),
            // flashcontrol - javascript only
            array(
                'name'        => 'flashcontrol - js',
                'dir'         => '../vendor/ext-ux-flashcontrol/build',
                'extensions'  => array('js'),
                'include'     => array('flashcontrol-all-debug.js'),
                'pathReplace' => array('../vendor/ext-ux-flashcontrol/build', "../../js/ext-ux-flashcontrol/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['javascript'][$buildType]
            ),

            // flexaccord - images only
            array(
                'name'        => 'flexaccord - images',
                'dir'         => '../vendor/ext-ux-flexaccord/build',
                'extensions'  => array('jpg', 'gif', 'jpeg', 'png'),
                'pathReplace' => array('../vendor/ext-ux-flexaccord/build', "../../js/ext-ux-flexaccord/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['images'][$buildType]
            ),
            // flexaccord - css only
            array(
                'name'        => 'flexaccord - css',
                'dir'         => '../vendor/ext-ux-flexaccord/build',
                'extensions'  => array('css'),
                'pathReplace' => array('../vendor/ext-ux-flexaccord/build', "../../js/ext-ux-flexaccord/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['stylesheets'][$buildType]
            ),
            // flexaccord - javascript only
            array(
                'name'        => 'flexaccord - js',
                'dir'         => '../vendor/ext-ux-flexaccord/build',
                'extensions'  => array('js'),
                'include'     => array('flexaccord-all-debug.js'),
                'pathReplace' => array('../vendor/ext-ux-flexaccord/build', "../../js/ext-ux-flexaccord/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['javascript'][$buildType]
            ),

            // wiz - images only
            array(
                'name'        => 'wiz - images',
                'dir'         => '../vendor/ext-ux-wiz/src',
                'extensions'  => array('jpg', 'gif', 'jpeg', 'png'),
                'pathReplace' => array('../vendor/ext-ux-wiz/src', "../../js/ext-ux-wiz/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['images'][$buildType]
            ),
            // wiz - css only
            array(
                'name'        => 'wiz - css',
                'dir'         => '../vendor/ext-ux-wiz/src',
                'extensions'  => array('css'),
                'pathReplace' => array('../vendor/ext-ux-wiz/src', "../../js/ext-ux-wiz/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['stylesheets'][$buildType]
            ),
            // wiz - javascript only
            array(
                'name'        => 'livegrid - js',
                'dir'         => '../vendor/ext-ux-wiz/src',
                'extensions'  => array('js'),
                'pathReplace' => array('../vendor/ext-ux-wiz/src', "../../js/ext-ux-wiz/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['javascript'][$buildType]
            ),

            // livegrid - images only
            array(
                'name'        => 'livegrid - images',
                'dir'         => '../vendor/ext-ux-livegrid/build',
                'extensions'  => array('jpg', 'gif', 'jpeg', 'png'),
                'pathReplace' => array('../vendor/ext-ux-livegrid/build', "../../js/ext-ux-livegrid/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['images'][$buildType]
            ),
            // livegrid - css only
            array(
                'name'        => 'livegrid - css',
                'dir'         => '../vendor/ext-ux-livegrid/build',
                'extensions'  => array('css'),
                'pathReplace' => array('../vendor/ext-ux-livegrid/build', "../../js/ext-ux-livegrid/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['stylesheets'][$buildType]
            ),
            // livegrid - javascript only
            array(
                'name'        => 'livegrid - js',
                'dir'         => '../vendor/ext-ux-livegrid/build',
                'extensions'  => array('js'),
                'include'     => array('livegrid-all-debug.js'),
                'pathReplace' => array('../vendor/ext-ux-livegrid/build', "../../js/ext-ux-livegrid/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['javascript'][$buildType]
            ),

            // toastwindow - images only
            array(
                'name'        => 'toastwindow - images',
                'dir'         => '../vendor/ext-ux-toastwindow',
                'extensions'  => array('jpg', 'gif', 'jpeg', 'png'),
                'pathReplace' => array('../vendor/ext-ux-toastwindow', "../../js/ext-ux-toastwindow/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['images'][$buildType]
            ),
            // toastwindow - css only
            array(
                'name'        => 'toastwindow - css',
                'dir'         => '../vendor/ext-ux-toastwindow',
                'extensions'  => array('css'),
                'pathReplace' => array('../vendor/ext-ux-toastwindow', "../../js/ext-ux-toastwindow/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['stylesheets'][$buildType]
            ),
            // toastwindow - javascript only
            array(
                'name'        => 'toastwindow - js',
                'dir'         => '../vendor/ext-ux-toastwindow',
                'extensions'  => array('js'),
                'pathReplace' => array('../vendor/ext-ux-toastwindow', "../../js/ext-ux-toastwindow/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['javascript'][$buildType]
            ),

            // youtubeplayer - images only
            array(
                'name'        => 'youtubeplayer - images',
                'dir'         => '../vendor/ext-ux-youtubeplayer/src',
                'extensions'  => array('jpg', 'gif', 'jpeg', 'png'),
                'pathReplace' => array('../vendor/ext-ux-youtubeplayer/src', "../../js/ext-ux-youtubeplayer/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['images'][$buildType]
            ),
            // youtubeplayer - css only
            array(
                'name'        => 'youtubeplayer - css',
                'dir'         => '../vendor/ext-ux-youtubeplayer/src',
                'extensions'  => array('css'),
                'pathReplace' => array('../vendor/ext-ux-youtubeplayer/src', "../../js/ext-ux-youtubeplayer/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['stylesheets'][$buildType]
            ),
            // youtubeplayer - javascript only
            array(
                'name'        => 'youtubeplayer - js',
                'dir'         => '../vendor/ext-ux-youtubeplayer/src',
                'extensions'  => array('js'),
                'pathReplace' => array('../vendor/ext-ux-youtubeplayer/src', "../../js/ext-ux-youtubeplayer/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['javascript'][$buildType]
            ),

            // gridviewmenuplugin - images only
            array(
                'name'        => 'gridviewmenuplugin - images',
                'dir'         => '../vendor/ext-ux-grid-gridviewmenuplugin/src',
                'extensions'  => array('jpg', 'gif', 'jpeg', 'png'),
                'pathReplace' => array('../vendor/ext-ux-grid-gridviewmenuplugin/src', "../../js/ext-ux-grid-gridviewmenuplugin/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['images'][$buildType]
            ),
            // gridviewmenuplugin - css only
            array(
                'name'        => 'gridviewmenuplugin - css',
                'dir'         => '../vendor/ext-ux-grid-gridviewmenuplugin/src',
                'extensions'  => array('css'),
                'pathReplace' => array('../vendor/ext-ux-grid-gridviewmenuplugin/src', "../../js/ext-ux-grid-gridviewmenuplugin/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['stylesheets'][$buildType]
            ),
            // gridviewmenuplugin - javascript only
            array(
                'name'        => 'gridviewmenuplugin - js',
                'dir'         => '../vendor/ext-ux-grid-gridviewmenuplugin/src',
                'extensions'  => array('js'),
                'pathReplace' => array('../vendor/ext-ux-grid-gridviewmenuplugin/src', "../../js/ext-ux-grid-gridviewmenuplugin/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['javascript'][$buildType]
            )

        ),


    ),
    'extjs' => array(
        'config' => array(
            // first section - images only
            array(
                'name'        => 'extjs - images',
                'dir'         => '../vendor/extjs/resources/images',
                'extensions'  => array('jpg', 'gif', 'jpeg', 'png'),
                'pathReplace' => array("../vendor/extjs/", "../../js/extjs/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['images'][$buildType]
            ),
            // second section - css only
            array(
                'name'        => 'extjs - css',
                'dir'         => '../vendor/extjs/resources/css',
                'extensions'  => array('css'),
                'include'     => array('ext-all.css'),
                'pathReplace' => array("../vendor/extjs/", "../../js/extjs/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['stylesheets'][$buildType]
            ),
            // third section - swf only
            array(
                'name'        => 'extjs - swf',
                'dir'         => '../vendor/extjs/resources',
                'extensions'  => array('swf'),
                'pathReplace' => array("../vendor/extjs/", "../../js/extjs/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['flash'][$buildType]
            ),
            // fourth section - javascript only
            array(
                'name'        => 'extjs - js',
                'dir'         => '../vendor/extjs',
                'include'     => array('ext-all-debug.js'),
                'extensions'  => array('js'),
                'pathReplace' => array("../vendor/extjs/", "../../js/extjs/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['javascript'][$buildType]
            ),
            // fifth section - javascript adapter only
            array(
                'name'        => 'extjs - js (adapter)',
                'dir'         => '../vendor/extjs/adapter/ext',
                'include'     => array('ext-base.js'),
                'extensions'  => array('js'),
                'pathReplace' => array("../vendor/extjs", "../../js/extjs/"),
                'sections'    => array('build', 'dev'),
                'dropFile'    => $dropFileConfig['javascript'][$buildType]
            )
        )
    )
);

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
            $dropFile    = $options['dropFile'];

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
            fwrite(STDOUT, "    Adding results to be saved in $dropFile for section(s) ".implode(',', $sections)."...\n");

            $fileCount += count($files);

            for ($i = 0, $len = count($sections); $i < $len; $i++) {

                if ($sections[$i] === $buildType) {
                    if (!isset($foundFiles[$dropFile][$sections[$i]])) {
                        $foundFiles[$dropFile][$sections[$i]] = array();
                    }
                    $foundFiles[$dropFile][$sections[$i]] = array_merge(
                        $foundFiles[$dropFile][$sections[$i]], $files
                    );
                }
            }

            fwrite(STDOUT, "\n");
            $i++;
        }
    }
}

fwrite(STDOUT, "\n\n");

fwrite(STDOUT, "Writing files...\n");

$final = array();
foreach ($foundFiles as $dropFile => $sections) {

    foreach ($sections as $section => $fileEntries) {
        file_put_contents($dropFile , implode("\n", $fileEntries));
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

exit(0);
?>