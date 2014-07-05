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

 $Author$
 $Id$
 $Date$
 $Revision$
 $LastChangedDate$
 $LastChangedBy$
 $URL$
-->

<h3>Installing the application folder</h3>
<p>
 conjoon's templates, controllers and cached files can be found within the ./application folder.
 This folder needs to be moved to a place where read and write access is possible. It is recommended
 that this folder is not within the document root.<br />
 Please provide the folder where the application folder should be moved to. The installation script
 will first check if this folder is readable and writable.<br />
 <br />
 <b>NOTE:</b><br />
 If you leave the field empty, the application folder will be moved to the folder where the index.php of the
 conjoon installation can be found (i.e. the root folder of the conjoon application). Be warned, as this is
 <b>NOT</b> recommended, and should only be used for demonstration purposes.
 <br />
 <br />
 <br />
 <?php if ($APPPATH['not_allowed']) { ?>
<div class="error_box">
<b>ERROR</b><br />
The path must not reside within the folder where the installation files reside.
</div>
<?php } ?>
<?php if ($APPPATH['not_existing']) { ?>
<div class="error_box">
<b>ERROR</b><br />
The specified path does not seem to exist.
</div>
<?php } ?>
<?php if (!$APPPATH['is_readable']) { ?>
<div class="error_box">
<b>ERROR</b><br />
The specified folder is not readable. Please check the file permissions or chose another path.
</div>
<?php } ?>
<?php if (!$APPPATH['is_writable']) { ?>
<div class="error_box">
<b>ERROR</b><br />
The specified folder is not writable. Please check the file permissions or chose another path.
</div>
<?php } ?>
 <input style="width:700px" type="text" name="app_path" value="<?php echo $_SESSION['app_path']; ?>" />
 <input type="hidden" name="app_path_post" value="1" />
</p>

<?php echo conjoon_configInfoSnippet('Application path', 'environment.application_path'); ?>