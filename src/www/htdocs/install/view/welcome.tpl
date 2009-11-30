<!--
 conjoon
 (c) 2002-2009 siteartwork.de/conjoon.org
 licensing@conjoon.org

 $Author$
 $Id$
 $Date$
 $Revision$
 $LastChangedDate$
 $LastChangedBy$
 $URL$
-->

<p>
Welcome to the conjoon setup process!
</p>
<p>
This wizard will help you with installing conjoon. It will check if the environment
matches the prerequisites for conjoon and guide you through the several set up steps.
</p>
<p>
If you have a support key, enter it now. If you do not have a support key, leave the field blank or
enter your email address. This will help with identifying your installation in case you have requested
help. No personal data will be send to the conjoon project during installation.
<br />
<br />
Support key: <input style="width:700px" type="text" name="support_key" value="<?php echo isset($_SESSION['support_key']) ? $_SESSION['support_key'] : '' ; ?>" />
</p>
<br />
<p>
You can provide a name for your installation. The name will appear here and there in conjoon. It's not important
for the functionality of conjoon, but make sure you chose something that makes sense.
<br />
<br />
Edition: <input type="text" name="edition" value="<?php echo isset($_SESSION['edition']) ? $_SESSION['edition'] : '' ; ?>" />
</p>
<br />
<p>
If this installation wizards detects a previous installation of conjoon, most of the information you have
to provide will be preset with the data from the currrent installation.
<br />
<br />
<u>Status:</u>
<?php if (isset($_SESSION['installation_info']['previous_version'])) { ?>
<div class="success_box">
  Previous installation detected. Updating conjoon V<?php echo $_SESSION['installation_info']['previous_version']; ?>
  with V<?php echo $_SESSION['current_version']; ?>.
</div>
<p>
<b>WARNING</b>:
<br />Make sure you do a full backup of your existing data (application files and database) before
you proceed!
</p>
<?php } else { ?>
<div class="warning_box">
  <i>installation.info.php</i> not available, installing conjoon V<?php echo $_SESSION['current_version']; ?>
  from scratch.
</div>
<?php } ?>
</p>
<br />
<p>
<b>Note</b>:
<br />The wizard will collect all necessary informations first, and installs conjoon once
you have reached and confirmed the last setup step.<br />
Once installation has finished successful, the file <i>installation.info.php</i> in the root directory
of the conjoon application will be updated with the data collected from this wizard. This will simplify
further updating of conjoon. Please do not remove this file.
</p>
<h3>Setup process</h3>
<p>
<pre style="border:1px dotted #5b5b5b; background:#dfdfdf">

    Welcome (You are here!)
              |
              |
    Step 1: Checking prerequisites
              |
              |
    Step 2: Database setup
              |
              |
    Step 3: Installing application folder
              |
              |
    Step 4: Cache settings
              |
              |
    Step 5: Installing libraries, managing include_path
              |
              |
    Step 6: Specifying document path
              |
              |
    Step 7: Specifying user credentials - this step is only available
    if there is NO root user already in the database
              |
              |
    Install!: Reviewing collected data and invoke installation
              |
              |
    Finish: Additional informations and suggestions regarding
    this installation and further updates

</pre>
</p>
<br />
<p>
<b>Note</b>:
<br />The install folder has to be put into the directory where conjoon's index.php resides.
If this is not the case, move the folder before you proceed!
</p>
<br />
<?php if ($WELCOME['license_agree_missing']) { ?>
<div class="error_box">
  Please agree to the license terms first before you install/update conjoon.
</div>
<?php } ?>
<p>
<input id="license_agree" type="checkbox" name="license_agree" value="1" /> <label for="license_agree">I have read and understood the </label><a href="#" onclick="window.open('./license.php')">license terms</a>
</p>
<input type="hidden" name="welcome_post" value="1" />