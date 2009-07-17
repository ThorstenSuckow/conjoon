@echo off
echo -----------------------------------------------------
echo -----------------------------------------------------
echo            Ext.ux.FlashControl Build Tool
echo  (c) 2009 Thorsten Suckow-Homberg ts@siteartwork.de
echo -----------------------------------------------------
echo  Using yuicompressor:
echo  http://developer.yahoo.com/yui/compressor/
echo -----------------------------------------------------
echo.

if "%1"=="" goto help

set yuicompressor_path=%1

if not exist %yuicompressor_path% goto error_message

:process
set tp=..\..\
set flashcontrol_file_list_all=%tp%src\FlashControl.js

echo ...building CSS file...
java -jar %yuicompressor_path% -o %tp%build\resources\css\ext-ux-flashcontrol.css --charset UTF-8 %tp%src\resources\css\ext-ux-flashcontrol.css
echo Done

echo ...merging files for flashcontrol-all.js...
copy /B /Y %flashcontrol_file_list_all% %tp%build\_tmp.js
echo ...building flashcontrol-all.js file...
java -jar %yuicompressor_path% -o %tp%build\flashcontrol-all.js --charset UTF-8 %tp%build\_tmp.js
echo Done

echo ...merging files for flashcontrol-all-debug.js...
copy /B /Y %flashcontrol_file_list_all% %tp%build\flashcontrol-all-debug.js
rem echo ...building flashcontrol-all-debug.js file...
rem java -jar %yuicompressor_path% -o %tp%build\flashcontrol-all-debug.js --nomunge --disable-optimizations --charset UTF-8 %tp%build\_tmp.js
echo Done

echo ...removing temp file...
del %tp%build\_tmp.js

echo FINISHED!
goto end

:help
echo Usage: make.bat [path to yuicompressor.jar]
echo Example: make.bat C:/Tools/yuicompressor-2.4.jar
echo Download yuicompressor at http://developer.yahoo.com/yui/compressor/
echo.
goto end

:error_message
echo.
echo Error: %yuicompressor_path% does not seem to point to the yuicompressor jar
echo.

:end