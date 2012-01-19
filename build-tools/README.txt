This folder contains scripts for building deployable versions of the conjoon
project.

build.sh
========
This is a simple shell script that executes the buildfile build.xml with the
following targets, in the following order:
 o php_tests - runs the PHPUnit Tests
 o rebuild - completely rebuilds the project (/build/build, /build/complete)
   if, and only if the tests succeeded
When calling the buildfile directly, tests will NOT be invoked. This is due
to namespace clashes when both docBlox and PHPUnit are used in a phing
buildfile

csscompressor.php
=================
cli script for compressing all css files used by conjoon (except for vendor
css files) into one file named "conjoon-all.css". All paths used by the script
depend on the directory layout as defined by the repository. Make sure you run
this script out of its current folder.
Note:
csscompressor should not be used anymore since 0.1a2 in favor of using the build_css.xml
buildfile.