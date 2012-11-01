@echo off
echo -------------------------------------------------------------------
echo                  Ext.ux.GridViewMenuPlugin Build Tool
echo  (c) 2012 Thorsten Suckow-Homberg thorsten@suckow-homberg.de
echo -------------------------------------------------------------------
echo  Using yuicompressor:
echo  http://developer.yahoo.com/yui/compressor/
echo -------------------------------------------------------------------
echo.

if "%1"=="" goto help

set yuicompressor_path=%1

if not exist %yuicompressor_path% goto error_message

:process
set tp=..\..\
set gridviewmenuplugin_file_list_component=%tp%src\GridViewMenuPlugin.js
set gridviewmenuplugin_file_list_all=%gridviewmenuplugin_file_list_component%

set gridviewmenuplugin_cssfile_list_core=%tp%src\resources\css\ext-ux-grid-gridviewmenuplugin.css
set gridviewmenuplugin_cssfile_list_all=%youtubeplayer_cssfile_list_core%


echo ...building CSS files...

echo ...merging files for ext-ux-grid-gridviewmenuplugin.css...
copy /B /Y %gridviewmenuplugin_cssfile_list_core% %tp%build\_tmp.css
echo ...building ext-ux-grid-gridviewmenuplugin.css file...
java -jar %yuicompressor_path% -o %tp%build\resources\css\ext-ux-grid-gridviewmenuplugin.css --charset UTF-8 %tp%build\_tmp.css
echo Done!

echo ...building JS files...

echo ...merging files for ext-ux-grid-gridviewmenuplugin.js...
copy /B /Y %gridviewmenuplugin_file_list_all% %tp%build\_tmp.js
echo ...building ext-ux-grid-gridviewmenuplugin-all.js file...
java -jar %yuicompressor_path% -o %tp%build\ext-ux-grid-gridviewmenuplugin.js --charset UTF-8 %tp%build\_tmp.js
echo Done!

echo ...merging files for ext-ux-grid-gridviewmenuplugin-debug.js...
copy /B /Y %gridviewmenuplugin_file_list_all% %tp%build\ext-ux-grid-gridviewmenuplugin-debug.js
echo Done

echo ...removing temp files...
del %tp%build\_tmp.js
del %tp%build\_tmp.css

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