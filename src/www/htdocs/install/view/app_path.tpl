<!--
 conjoon
 (c) 2002-2012 siteartwork.de/conjoon.org
 licensing@conjoon.org

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