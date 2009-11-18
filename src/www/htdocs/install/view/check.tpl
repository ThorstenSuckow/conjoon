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

<h3>Validating prerequisites for conjoon</h3>
<p>
This step will validate that your system meets the requirements needed for running conjoon. If anything
fails, i.e. if a <i>WARNING</i> or <i>ERROR</i> is generated, it is likely that your installation will
not work! The wizard will let you continue with trying to setup conjoon, though. Support from the conjoon
project in this cases is, however, not guaranteed.
</p>


<!-- CHECK PHP VERSION -->
<h3>1. Checking PHP Version</h3>
<p>
<div class="<?php echo ($CHECK['php_version_match'] === true) ? 'success_box' : 'warning_box'; ?>">
<?php if ($CHECK['php_version_match']) { ?>
 <b>SUCCESS</b>   <br />
 PHP <?php echo $CHECK['current_php_version']; ?> detected. conjoon needs at least PHP
 <?php echo $CHECK['php_version_required']; ?>.
<?php } else { ?>
 <b>WARNING</b> <br />
  You are running PHP <?php echo $CHECK['current_php_version']; ?>. conjoon needs at least PHP
 <?php echo $CHECK['php_version_required']; ?>. Running conjoon with this version might work, but
 it is not supported.
<?php } ?>
</div>
</p>

<!-- CHECK PARENT_DIR_WRITABLE -->
<h4>1.1 Checking if parent dir is writable</h4>
<p>
<div class="<?php echo ($CHECK['parent_dir_writable'] === true) ? 'success_box' : 'error_box'; ?>">
<?php if ($CHECK['parent_dir_writable']) { ?>
 <b>SUCCESS</b>   <br />
 Parent dir <i><?php echo $CHECK['parent_dir']; ?></i> is writable by the server.
<?php } else { ?>
 <b>ERROR</b> <br />
  The parent dir <i><?php echo $CHECK['parent_dir']; ?></i> is not writable by the webserver. Please change the permissions for this directory.
<?php } ?>
</div>
</p>

<!-- CHECK SAFE_MODE -->
<?php if($CHECK['safe_mode_enabled'] && $CHECK['safe_mode_failure']) { ?>
<h4>1.2 Checking safe_mode permissions</h4>
<p>
<div class="error_box">
 <b>ERROR</b> <br />
  safe_mode restriction in effect. conjoon might not be able to move the
  application and library folders to their desired location.
  <br />
  (Please remove the directory <i>../<?php echo $CHECK['safe_mode_tmp_dir'];?></i> by hand, which was
  created by this script for checking safe_mode restrictions.)
</div>
</p>
<?php } ?>

<!-- CHECK APACHE VERSION -->
<h3>2. Checking Apache Version</h3>
<?php if (!$CHECK['apache_available']) { ?>
 <p>
   <div class="error_box">
     <b>ERROR</b><br />
     Apache was not detected on your server. conjoon was developed to run under Apache <?php echo $CHECK['apache_version_required']; ?>.
     Running conjoon under any other webserver is experimental and not supported.
   </div>
 </p>
<?php } else { ?>
<p>
<div class="<?php echo ($CHECK['apache_version_match'] === true) ? 'success_box' : 'warning_box'; ?>">
<?php if ($CHECK['apache_version_match']) { ?>
 <b>SUCCESS</b>   <br />
 Apache <?php echo $CHECK['apache_version']; ?> detected. conjoon needs at least Apache
 <?php echo $CHECK['apache_version_required']; ?>.
<?php } else { ?>
 <b>WARNING</b> <br />
  You are running Apache <?php echo $CHECK['apache_version']; ?>. conjoon needs at least Apache
 <?php echo $CHECK['apache_version_required']; ?>. Running conjoon with this version might work, but
 it is not supported.
<?php } ?>
</div>
</p>

<!-- CHECK MOD_REWRITE -->
<h4>2.1 Checking mod_rewrite</h4>
<p>
  <?php if (!$CHECK['mod_rewrite_available']) { ?>
    <div class="warning_box">
     <b>WARNING</b><br />
     mod_rewrite was not detected. If you are running PHP as CGI, it's possible that it is
     already installed. Please check your Apache installation. mod_rewrite has to be installed
     in order to run conjoon.
   </div>
  <?php } else { ?>
    <div class="success_box">
     <b>SUCCESS</b><br />
     The mod_rewrite-module for Apache is installed.
   </div>
  <?php } ?>

</p>

<?php }  ?>


<!-- CHECK MAGIC_QUOTES_GPC -->
<h3>3. Checking magic_quotes_gpc</h3>
<p>
<div class="<?php echo ($CHECK['magic_quotes_gpc'] === true) ? 'warning_box' : 'success_box'; ?>">
<?php if (!$CHECK['magic_quotes_gpc']) { ?>
 <b>SUCCESS</b>   <br />
 magic_quotes_gpc is disabled.
<?php } else { ?>
 <b>WARNING</b> <br />
  magic_quotes_gpc is enabled. conjoon will not work properly while this setting is enabled.
  In order to run conjoon, please change your php.ini so that magic_quotes_gpc is disabled..
<?php } ?>
</div>
</p>

<!-- CHECK REGISTER_GLOBALS -->
<h3>4. Checking register_globals</h3>
<p>
<div class="<?php echo ($CHECK['register_globals'] === true) ? 'warning_box' : 'success_box'; ?>">
<?php if (!$CHECK['register_globals']) { ?>
 <b>SUCCESS</b>   <br />
 register_globals is disabled.
<?php } else { ?>
 <b>WARNING</b> <br />
  register_globals is enabled. For security reasons, please disable register_globals in your php.ini.
<?php } ?>
</div>
</p>

<!-- CHECK PDO -->
<h3>5. Checking PDO extension</h3>
<p>
<div class="<?php echo ($CHECK['pdo_extension_loaded'] === true) ? 'success_box' : 'error_box'; ?>">
<?php if ($CHECK['pdo_extension_loaded']) { ?>
 <b>SUCCESS</b>   <br />
 PDO extension available.
<?php } else { ?>
 <b>ERROR</b> <br />
  The PDO extension is not available. conjoon uses PDO for database access. Please install the PDO
  extension and run setup again..
<?php } ?>
</div>
</p>

<?php if ($CHECK['pdo_extension_loaded']) { ?>
<!-- CHECK PDO_MYSQL -->
<h4>5.1 Checking PDO drivers</h4>
<p>
<div class="<?php echo ($CHECK['pdo_mysql_available'] === true) ? 'success_box' : 'error_box'; ?>">
<?php if ($CHECK['pdo_mysql_available']) { ?>
 <b>SUCCESS</b>   <br />
 pdo_mysql driver available.
<?php } else { ?>
 <b>ERROR</b> <br />
  pdo_mysql-driver not available. conjoon uses this driver for accessing the mysql database.
<?php } ?>
</div>
</p>
<?php } ?>

<!-- CHECK fsockopen -->
<h3>6 Checking fsockopen</h3>
<p>
<div class="<?php echo ($CHECK['fsockopen_available'] === true) ? 'success_box' : 'warning_box'; ?>">
<?php if ($CHECK['fsockopen_available']) { ?>
 <b>SUCCESS</b>   <br />
 fsockopen working.
<?php } else { ?>
 <b>WARNING</b> <br />
  fsockopen seems not to work on your server. Your hosting provider might have disabled it due to security
  reasons. You can proceed with the installation, but conjoon might not be able to work properly when
  it comes to networking (for example: fetching/sending emails).
<?php } ?>
</div>
</p>


<!-- CHECK simplexml -->
<h3>7 Checking simplexml</h3>
<p>
<div class="<?php echo ($CHECK['simplexml'] === true) ? 'success_box' : 'warning_box'; ?>">
<?php if ($CHECK['simplexml']) { ?>
 <b>SUCCESS</b>   <br />
 simplexml found.
<?php } else { ?>
 <b>WARNING</b> <br />
  simplexml does not seem to be available on your server. simplexml is needed by parts
  of the Zend Framework. You can proceed with the installation, but conjoon might not
  work properly (for example: parsing emails).
<?php } ?>
</div>
</p>