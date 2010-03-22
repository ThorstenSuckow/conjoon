<!--
 conjoon
 (c) 2002-2010 siteartwork.de/conjoon.org
 licensing@conjoon.org

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
 <br />
 <br />
 <input id="aipcb" <?php echo ($_SESSION['add_include_path'] ? "checked=\"checked\"" :"" ); ?> type="checkbox" name="add_include_path" value="1" />  <label for="aipcb">Let conjoon add this path to php's include_path (uncheck this if you have access to the php.ini and can edit the include_path setting on your own)</label>
 <input type="hidden" name="lib_path_post" value="1" />
</p>