<!--
 conjoon
 (c) 2007-2015 conjoon.org
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

<h3>Installing the libraries</h3>
<p>
 conjoon depends on the Zend Framework and the conjoon library itself. Both libraries come with the
 installation pack of conjoon.<br />
 This step will let you provide the folder where the libs folder will be moved to. The folder needs to
 be readable by the webserver. Additionally, you can decide if you want to add the chosen path to the include_path
 setting of php. If you do so, the include_path will be initialized on each server request. You might want to
 add the path to the libs folder to your php.ini directly, as this will give you a small perfomance increase.
 <br />
 <br />
 <b>NOTE:</b><br />
 If you leave the field empty, the libs folder will be moved to the folder where the index.php of the
 conjoon installation can be found (i.e. the root folder of the conjoon application).
 <br />
 <br />
 <br />
  <?php if ($LIBPATH['not_allowed']) { ?>
<div class="error_box">
<b>ERROR</b><br />
The path must not reside within the folder where the installation files reside.
</div>
<?php } ?>
<?php if ($LIBPATH['not_existing']) { ?>
<div class="error_box">
<b>ERROR</b><br />
The specified path does not seem to exist.
</div>
<?php } ?>
<?php if (!$LIBPATH['is_readable']) { ?>
<div class="error_box">
<b>ERROR</b><br />
The specified folder is not readable. Please check the file permissions or chose another path.
</div>
<?php } ?>

 <input style="width:700px" type="text" name="lib_path" value="<?php echo $_SESSION['lib_path']; ?>" />

 <?php echo conjoon_configInfoSnippet('Library path', 'environment.include_path'); ?>


 <br />
 <br />
 <input id="aipcb" <?php echo ($_SESSION['add_include_path'] ? "checked=\"checked\"" :"" ); ?> type="checkbox" name="add_include_path" value="1" />  <label for="aipcb">Let conjoon add this path to php's include_path (uncheck this if you have access to the php.ini and can edit the include_path setting on your own)</label>
 <input type="hidden" name="lib_path_post" value="1" />
 <br />
 <div class="info_box">
    <b>Note:</b> If you uncheck the checkbox and edit the <i>include_path</i>-setting on your own,
     make sure to consult the documentation for any additional library paths you have to
     specify.
</div>
</p>
