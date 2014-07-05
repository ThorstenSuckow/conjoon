<!--
 conjoon
 (c) 2007-2014 conjoon.org
 licensing@conjoon.org

 conjoon
 Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as
 published by the Free Software Foundation, either version 3 of the
 License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU Affero General Public License for more details.

 You should have received a copy of the GNU Affero General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.

 $Author: T. Suckow $
 $Id: cache.tpl 1540 2012-11-06 23:19:11Z T. Suckow $
 $Date: 2012-11-07 00:19:11 +0100 (Mi, 07 Nov 2012) $
 $Revision: 1540 $
 $LastChangedDate: 2012-11-07 00:19:11 +0100 (Mi, 07 Nov 2012) $
 $LastChangedBy: T. Suckow $
 $URL: http://svn.conjoon.org/trunk/src/www/htdocs/install/view/cache.tpl $
-->

<?php
    $FOLDER_CREATE_ERROR = "Sorry, I tried to create the specified folder but "
                           ."could not succeed. Please make sure your webserver and PHP have "
                           ."appropriate rights to create a folder at the specified location.";

    function conjoon_getthisinfobox() {

        return "<div class=\"info_box\">
                <strong>Note:</strong> If you do not specify absolute paths, the specified directory will
                be treated relative to the application path, which was chosen in Step 4, and points to
                <i><strong>" .
                rtrim($_SESSION['app_path']) . '/' . $_SESSION['setup_ini']['app_path']['folder'] .
                "</strong></i></div>";

    }

?>

<script type="text/javascript">

    function showCacheOptions(show, libName)
    {
        document.getElementById('cacheOptionsContainer_' + libName).style.display =
            show ? '' : 'none';
    }



</script>

<h3>conjoon Application Configuration Settings</h3>
<p>
This step allows for editing settings related to conjoon application behavior.
</p>

<h4>File Storage Settings</h4>

<!-- UPLOAD FILESIZE -->
<div class="settingsContainer type_1">
<h5>Max. Upload File Size (in bytes)</h5>
<p>
    Set this to the maximum allowed size of uploaded files, in bytes.
    This value should be the minimum value of max. allowed packet (as configured in Step 3) and
    your php's post_max_size/upload_max_filesize.
    <br />
    <div class="info_box">
    Current settings:
        <ul>
            <li>
                max allowed packet: <?php echo conjoon_megaByteToByte($_SESSION['max_allowed_packet']); ?> bytes
                <?php
                $bytes = conjoon_bytesToMegaByte(conjoon_megaByteToByte($_SESSION['max_allowed_packet']));
                if((int) conjoon_megaByteToByte($_SESSION['max_allowed_packet']) >= 1024 * 1024) { ?>
                   (<?php echo $bytes; ?>)
                <?php } ?>
            </li>
            <li>
                php.ini upload_max_filesize: <?php echo conjoon_megaByteToByte(ini_get('upload_max_filesize')); ?> bytes
                <?php
                $bytes = conjoon_bytesToMegaByte(conjoon_megaByteToByte(ini_get('upload_max_filesize')));
                if( (int) conjoon_megaByteToByte(ini_get('upload_max_filesize')) >= 1024 * 1024) { ?>
                (<?php echo $bytes; ?>)
                <?php } ?>
            </li>
            <li>
                php.ini post_max_size: <?php echo conjoon_megaByteToByte(ini_get('post_max_size')); ?> bytes
                <?php
                $bytes = conjoon_bytesToMegaByte(conjoon_megaByteToByte(ini_get('post_max_size')));
                if( (int) conjoon_megaByteToByte(ini_get('post_max_size')) >= 1024 * 1024) { ?>
                (<?php echo $bytes; ?>)
                <?php } ?>
            </li>
        </ul>
    </div>
    <br />
    Max Upload Filesize:
    <br />
    <input style="width:100%" type="text" name="upload.max_size" value="<?php echo $CN_SETTINGS['upload.max_size']; ?>" />
    <?php echo conjoon_cacheDirSnippet('Max. Upload File Size', 'files.upload.max_size'); ?>
</p>
</div>
<!-- ^^ EO UPLOAD FILESIZE -->


<!-- FILESYSTEM ENABLED -->
<div class="settingsContainer type_2">
<h5>Enable Filesystem</h5>
<p>
    Set this to "yes" to store files in the filesystem of your server instead of
    the database.
    <br />
    <br />
    Do you want to enable the filesystem for files managed by conjoon?
    <div style="margin:5px">
        <input onclick="showCacheOptions(false, 'filesystem');" id="use_filesystem_radio_no" <?php echo !$CN_SETTINGS['storage.filesystem.enabled'] ? "checked=\"checked\"" : ""; ?> type="radio" name="storage.filesystem.enabled" value="0" /><label for="use_filesystem_radio_no">No</label>
        <br />
        <input onclick="showCacheOptions(true, 'filesystem');" id="use_filesystem_radio_yes" <?php echo $CN_SETTINGS['storage.filesystem.enabled'] ? "checked=\"checked\"" : ""; ?> type="radio" name="storage.filesystem.enabled" value="1" /><label for="use_filesystem_radio_yes">Yes</label>
    </div>
    <?php echo conjoon_cacheEnabledSnippet('Filesystem enabled', 'files.storage.filesystem.enabled'); ?>

    </p>
</div>
<!-- ^^ EO FILESYSTEM ENABLED -->

<!-- FILESYSTEM DIR -->
<div class="settingsContainer type_1" id="cacheOptionsContainer_filesystem" style="<?php echo $CN_SETTINGS['storage.filesystem.enabled'] ? "" : "display:none;" ?>">
 <h5>Path to Filesystem Storage</h5>
 <p>
 Specify the path where conjoon stores files (e.g. file uploads).
<?php echo conjoon_getthisinfobox(); ?>
 <!-- ERRORS -->
 <?php if (isset($CN_SETTINGS['storage.filesystem.dir.install_failed'])) { ?>
     <?php if ($CN_SETTINGS['storage.filesystem.dir.install_failed'] === true) { ?>
         <div class="error_box">
         <b>ERROR</b><br />
         <?php echo $FOLDER_CREATE_ERROR; ?>
         </div>
     <?php } ?>
 <?php } ?>
 <!-- ^^ EO ERRORS -->
  Path:
  <br />
  <input style="width:100%" type="text" name="storage.filesystem.dir" value="<?php echo $CN_SETTINGS['storage.filesystem.dir']; ?>" />
  <?php echo conjoon_cacheDirSnippet('Filesystem storage directory', 'files.storage.filesystem.dir'); ?>
 </p>
</div>
<!-- ^^ EO FILESYSTEM DIR -->


<input type="hidden" name="cn_settings_post" value="1" />

