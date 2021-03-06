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

<p>
Welcome to the conjoon setup process!
</p>
<p>
This wizard will help you with installing conjoon. It will check if the environment
matches the prerequisites for conjoon and guide you through the several set up steps.
</p>
<div class="info_box">
    Before you continue with the setup of conjoon, please make sure that you
    have read the <a href="http://conjoon.org/wiki/display/DOC/conjoon+Installation+and+Upgrade+Guide" target="_blank">conjoon Installation and Upgrade Guide</a>.
    <br />
    Once conjoon is up and running, refer to the <a href="http://conjoon.org/wiki/display/DOC/conjoon+User%27s+Guide" target="_blank">conjoon User's Guide</a>
    which provides an extended end-user documentation.
</div>

<?php if (is_array($_SESSION['config_info'])) { ?>
<br />
<div class="success_box">
    We have detected an existing configuration file for conjoon (located at <i>../config.ini.php</i>).
    <br />
    conjoon will display the data found in the configuration file throughout the
    setup process so you will be able to compare this configuration to the
    settings you have submitted during your last setup run.
</div>
<?php } ?>

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
<?php echo conjoon_configInfoSnippet('Edition', 'environment.edition'); ?>
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
<h3>Setup Schedule</h3>
<p>
<pre style="border:1px dotted #5b5b5b; background:#dfdfdf">

    Welcome (You are here!)
              |
              |
    Step 1: Checking prerequisites
              |
              |
    Step 2: Localization
              |
              |
    Step 3: Database setup
              |
              |
    Step 4: Installing application folder
              |
              |
    Step 5: Cache settings
              |
              |
    Step 6: Tool Configuration
              |
              |
    Step 7: conjoon Application Configuration Options
              |
              |
    Step 8: Installing libraries, managing include_path
              |
              |
    Step 9: Specifying document path
              |
              |
    Step 10: Specifying user credentials - this step is only available
    if there is NO root user already in the database
              |
              |
    Patching: This involves patching previous installed
    versions of conjoon. This step is only available if installation
    information is available (usually found in installation.info.php)
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
<?php } else { ?>
<div class="warning_box">
    Please agree to the license terms first before you install/update conjoon.
</div>
<?php } ?>
<p>
<input id="license_agree" type="checkbox" name="license_agree" value="1" /> <label for="license_agree">I have read, understood and agreed to the </label><a href="#" onclick="window.open('./license.php')">terms and conditions of the software license</a>.
<br />
<br />
<?php if ($WELCOME['backup_check_missing']) { ?>
<div class="error_box">
  Hey, just curious if you gracefully ignored the hint to backing up your data before proceeding?
</div>
<?php } ?>
<input id="backup_check" type="checkbox" name="backup_check" value="1" /> <label for="backup_check">
    I have made a backup of my data! Let's continue!
</label>
</p>
<input type="hidden" name="welcome_post" value="1" />
