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

<h3>Install conjoon</h3>
<p>
 The wizard has now collected all data necessary to install conjoon.
 <br />
 Please check the data again and navigate back to the various cards if you need to change
 some of the information you have supplied.
 <br />
 If you click the "Next" button, conjoon will be installed using the following information:
 <br />
 <br />
 <br />

<h4>Installation info</h4>
<p>
<?php if (isset($_SESSION['installation_info']['previous_version'])) { ?>
<div class="success_box">
  Updating conjoon V<?php echo $_SESSION['installation_info']['previous_version']; ?>
  with V<?php echo $_SESSION['current_version']; ?>.
</div>
<?php } else { ?>
<div class="warning_box">
  Installing conjoon V<?php echo $_SESSION['current_version']; ?> from scratch.
</div>
<?php } ?>
</p>

<h4>Edition</h4>
<table>
    <tbody>
<tr>
    <td><i>Edition name</i>:</td>
    <td><?php echo $_SESSION['edition']; ?></td>
</tr>
</tbody>
</table>

<h4>Support key</h4>
<table>
    <tbody>
<tr>
    <td><i>Key</i>:</td>
    <td><?php echo $_SESSION['support_key'] != "" ? $_SESSION['support_key'] : "(None specified)"; ?></td>
</tr>
</tbody>
</table>


<?php if (isset($_SESSION['patches']) && !empty($_SESSION['patches'])) {?>
 <h4>Patches</h4>

<?php
     $allApply = true;
     foreach ($_SESSION['patches'] as $patchKey => $apply) {
        if (!$apply) {
            $allApply = false;
            break;
        }
     }
if (!$allApply) { ?>
    <div class="warning_box">
     One or more patches are available, but you did not choose all of them to be applied to this
     installation.
    </div>
<?php } ?>

  <table>
   <tbody>
    <?php
     $allApply = true;
     foreach ($_SESSION['patches'] as $patchKey => $apply) {
        if (!$apply) {$allApply = false;}
    ?>
    <tr>
     <td><i>Patch <?php echo $patchKey; ?></i>:</td>
     <td><?php echo $apply ? "ready to patch" : "ignored"; ?></td>
    </tr>
    <?php } ?>
</tbody>
</table>
<?php } ?>

<h4>Localization</h4>
<table>
    <tbody>
<tr>
    <td><i>Application's timezone</i>:</td>
    <td><?php echo $_SESSION['locale_timezone_default']; ?></td>
</tr>
<tr>
    <td><i>Application's fallback timezone</i>:</td>
    <td><?php echo $_SESSION['locale_timezone_fallback']; ?></td>
</tr>
</tbody>
</table>



<h4>Database information</h4>
<table>
    <tbody>
<tr>
    <td><i>Database type</i>:</td>
    <td><?php echo $_SESSION['db_adapter']; ?></td>
</tr>
<tr>
    <td><i>Database host</i>:</td>
    <td><?php echo $_SESSION['db_host']; ?></td>
</tr>
<tr>
    <td><i>Database port</i>:</td>
    <td><?php echo $_SESSION['db_port']; ?></td>
</tr>
<tr>
    <td><i>Database name</i>:</td>
    <td><?php echo $_SESSION['db']; ?></td>
</tr>
<tr>
    <td><i>Table prefix</i>:</td>
    <td><?php echo $_SESSION['db_table_prefix'] ? $_SESSION['db_table_prefix'] : '<i>- none specified -</i>'; ?></td>
</tr>
<tr>
    <td><i>Database user</i>:</td>
    <td><?php echo $_SESSION['db_user']; ?></td>
</tr>
<tr>
    <td><i>Database password</i>:</td>
    <td><?php echo ($_SESSION['db_password'] == "" ? "(None specified)" : $_SESSION['db_password']); ?></td>
</tr>
<tr>
    <td><i>Max allowed packet</i>:</td>
    <td><?php echo ($_SESSION['max_allowed_packet'] == "" ? "(None specified)" : $_SESSION['max_allowed_packet']); ?></td>
</tr>
</tbody>
</table>

<h4>Application folder</h4>
<table>
    <tbody>
<tr>
    <td><i>Path to application folder</i>:</td>
    <td><?php echo $_SESSION['app_path']; ?></td>
</tr>
<?php if ($INSTALL['app_path']['delete_warning']) { ?>
<tr>
    <td colspan="2">
     <div class="warning_box">
       The directory <i><?php echo $INSTALL['app_path']['full']; ?></i>
       already exists. The following directories will be deleted:
       <ul>
       <?php for ($i = 0, $len = count($INSTALL['app_path']['delete']); $i < $len; $i++) { ?>
           <li><?php echo $INSTALL['app_path']['delete'][$i] ?></li>
       <?php } ?>
    </ul>
     </div>
    </td>
</tr>
<?php } ?>
</tbody>
</table>


<h4>Caching options</h4>

<?php if ($INSTALL['CACHE_REMOVE']['WARNING']) { ?>
 <div class="warning_box">
   conjoon has detected that caching was enabled in the previous installation.
   The install wizard will remove the entire existing cache.

   <?php if (count($INSTALL['CACHE_REMOVE']['FILES']) > 0) { ?>

   <br />
   The following cache directories have been detected and will be removed:
   <ul>

    <?php for ($i = 0, $len = count($INSTALL['CACHE_REMOVE']['FILES']); $i < $len; $i++) { ?>

     <li><?php echo $INSTALL['CACHE_REMOVE']['FILES'][$i];?></li>

    <?php } ?>

   </ul>

    <strong>Note:</strong> Deleting deeply nested folders usually takes some time. Make sure
    your php.ini settings regarding script execution timeout are set to a high enough value, or
    delete the specified folders by hand before you proceed.

   <?php } ?>

 </div>
<?php } ?>

<table>
    <tbody>
<tr>
    <td><i>Caching enabled</i>:</td>
    <td><?php echo $_SESSION['cache']['default.caching'] ? "Yes" : "No"; ?></td>
</tr>
<?php if ($_SESSION['cache']['default.caching']) { ?>

  <!-- DB CACHE -->
  <tr>
      <td colspan="2"><strong>Database cache options</strong></td>
  </tr>
  <tr>
      <td><i>Metadata caching enabled</i>:</td>
      <td><?php echo $_SESSION['cache']['db.metadata.caching'] ? "Yes" : "No" ; ?></td>
  </tr>
  <?php if ($_SESSION['cache']['db.metadata.caching']) { ?>
  <tr>
      <td><i>Metadata cache path</i>:</td>
      <td><?php echo $_SESSION['cache']['db.metadata.backend.cache_dir']; ?></td>
  </tr>
<?php } ?>
  <!-- ^^ EO DB CACHE -->

<tr><td colspan="2">&nbsp;</td></tr>

  <!-- EMAIL CACHE -->
  <tr>
      <td colspan="2"><strong>Email cache options</strong></td>
  </tr>

  <tr>
      <td><i>Email message caching enabled</i>:</td>
      <td><?php echo $_SESSION['cache']['email.message.caching'] ? "Yes" : "No" ; ?></td>
  </tr>
  <?php if ($_SESSION['cache']['email.message.caching']) { ?>
  <tr>
      <td><i>Email message cache path</i>:</td>
      <td><?php echo $_SESSION['cache']['email.message.backend.cache_dir']; ?></td>
  </tr>
  <?php } ?>
  <tr>
      <td><i>Email accounts caching enabled</i>:</td>
      <td><?php echo $_SESSION['cache']['email.accounts.caching'] ? "Yes" : "No" ; ?></td>
  </tr>
  <?php if ($_SESSION['cache']['email.accounts.caching']) { ?>
  <tr>
      <td><i>Email accounts cache path</i>:</td>
      <td><?php echo $_SESSION['cache']['email.accounts.backend.cache_dir']; ?></td>
  </tr>
  <?php } ?>

  <tr>
    <td><i>Email Folders' Root Type caching enabled</i>:</td>
    <td><?php echo $_SESSION['cache']['email.folders_root_type.caching'] ? "Yes" : "No" ; ?></td>
  </tr>
  <?php if ($_SESSION['cache']['email.folders_root_type.caching']) { ?>
    <tr>
        <td><i>Email Folders' Root Type accounts cache path</i>:</td>
        <td><?php echo $_SESSION['cache']['email.folders_root_type.backend.cache_dir']; ?></td>
    </tr>
   <?php } ?>
  <!-- ^^ EO EMAIL CACHE -->

<tr><td colspan="2">&nbsp;</td></tr>

  <!-- Feed CACHE -->
  <tr>
      <td colspan="2"><strong>Feed cache options</strong></td>
  </tr>

  <tr>
      <td><i>Feed entry caching enabled</i>:</td>
      <td><?php echo $_SESSION['cache']['feed.item.caching'] ? "Yes" : "No" ; ?></td>
  </tr>
  <?php if ($_SESSION['cache']['feed.item.caching']) { ?>
  <tr>
      <td><i>Feed entry cache path</i>:</td>
      <td><?php echo $_SESSION['cache']['feed.item.backend.cache_dir']; ?></td>
  </tr>
  <?php } ?>
  <tr>
      <td><i>Feed item list caching enabled</i>:</td>
      <td><?php echo $_SESSION['cache']['feed.item_list.caching'] ? "Yes" : "No" ; ?></td>
  </tr>
  <?php if ($_SESSION['cache']['feed.item_list.caching']) { ?>
  <tr>
      <td><i>Feed item list cache path</i>:</td>
      <td><?php echo $_SESSION['cache']['feed.item_list.backend.cache_dir']; ?></td>
  </tr>
  <?php } ?>

  <tr>
      <td><i>Feed account caching enabled</i>:</td>
      <td><?php echo $_SESSION['cache']['feed.account.caching'] ? "Yes" : "No" ; ?></td>
  </tr>
  <?php if ($_SESSION['cache']['feed.account.caching']) { ?>
  <tr>
      <td><i>Feed account cache path</i>:</td>
      <td><?php echo $_SESSION['cache']['feed.account.backend.cache_dir']; ?></td>
  </tr>
  <?php } ?>

  <tr>
      <td><i>Feed account list caching enabled</i>:</td>
      <td><?php echo $_SESSION['cache']['feed.account_list.caching'] ? "Yes" : "No" ; ?></td>
  </tr>
  <?php if ($_SESSION['cache']['feed.account_list.caching']) { ?>
  <tr>
      <td><i>Feed account list cache path</i>:</td>
      <td><?php echo $_SESSION['cache']['feed.account_list.backend.cache_dir']; ?></td>
  </tr>
  <?php } ?>

  <tr>
      <td><i>Feed Reader caching enabled</i>:</td>
      <td><?php echo $_SESSION['cache']['feed.reader.caching'] ? "Yes" : "No" ; ?></td>
  </tr>
  <?php if ($_SESSION['cache']['feed.reader.caching']) { ?>
  <tr>
      <td><i>Feed Reader cache path</i>:</td>
      <td><?php echo $_SESSION['cache']['feed.reader.backend.cache_dir']; ?></td>
  </tr>
  <?php } ?>

  <!-- ^^ EO FEED CACHE -->

<tr><td colspan="2">&nbsp;</td></tr>

  <!-- TWITTER CACHE -->
  <tr>
      <td colspan="2"><strong>Twitter cache options</strong></td>
  </tr>

  <tr>
      <td><i>Twitter account caching enabled</i>:</td>
      <td><?php echo $_SESSION['cache']['twitter.accounts.caching'] ? "Yes" : "No" ; ?></td>
  </tr>
  <?php if ($_SESSION['cache']['twitter.accounts.caching']) { ?>
  <tr>
      <td><i>Twitter account cache path</i>:</td>
      <td><?php echo $_SESSION['cache']['twitter.accounts.backend.cache_dir']; ?></td>
  </tr>
  <?php } ?>
  <!-- ^^ EO TWITTER CACHE -->


<?php } ?>
</tbody>
</table>

<h4>Libraries</h4>
<table>
    <tbody>
<tr>
    <td><i>Path to libraries</i>:</td>
    <td><?php echo $_SESSION['lib_path']; ?></td>
</tr>
<?php if ($INSTALL['include_path']['delete_warning']) { ?>
<tr>
    <td colspan="2">
     <div class="warning_box">
       The directory <i><?php echo $INSTALL['include_path']['ini']; ?></i>
       already exists. The following directories will be deleted:
       <ul>
       <?php for ($i = 0, $len = count($INSTALL['include_path']['delete']); $i < $len; $i++) { ?>
           <li><?php echo $INSTALL['include_path']['delete'][$i] ?></li>
       <?php } ?>
    </ul>
    <strong>Note:</strong> Deleting deeply nested folders usually takes some time. Make sure
    your php.ini settings regarding script execution timeout are set to a high enough value, or
    delete the specified folders by hand before you proceed.
     </div>
    </td>
</tr>
<?php } ?>

<tr>
    <td><i>conjoon sets include_path</i>:</td>
    <td><?php echo ($_SESSION['add_include_path'] ? "Yes" : "No"); ?></td>
</tr>
<?php if (!$_SESSION['add_include_path']) { ?>
<tr>
    <td colspan="2">
     <div class="warning_box">
       Please add the following path to PHP's include_path: <br />
       <i><?php echo $INSTALL['include_path']['ini']; ?></i>
     </div>
    </td>
</tr>
<?php } ?>
</tbody>
</table>

<h4>Document path</h4>
<table>
    <tbody>
<tr>
    <td><i>Document path</i>:</td>
    <td><?php echo $_SESSION['doc_path']; ?></td>
</tr>
</tbody>
</table>

<?php if (!isset($_SESSION['installation_info']['app_credentials'])) { ?>
<h4>User credentials</h4>
<table>
    <tbody>
<tr>
    <td><i>User name</i>:</td>
    <td><?php echo $_SESSION['app_credentials']['user']; ?></td>
</tr>
<tr>
    <td><i>Password</i>:</td>
    <td><?php echo $_SESSION['app_credentials']['password']; ?></td>
</tr>
<tr>
    <td><i>First name</i>:</td>
    <td><?php echo $_SESSION['app_credentials']['firstname']; ?></td>
</tr>
<tr>
    <td><i>Last name</i>:</td>
    <td><?php echo $_SESSION['app_credentials']['lastname']; ?></td>
</tr>
<tr>
    <td><i>Email address</i>:</td>
    <td><?php echo $_SESSION['app_credentials']['email_address']; ?></td>
</tr>
</tbody>
</table>
<?php } ?>

<?php if ($INSTALL['IMREMOVING']['js'] || $INSTALL['IMREMOVING']['_configCache']) { ?>
<h4>Deleting old folders</h4>
<table>
 <tbody>
  <tr>
      <td colspan="2">
       <div class="warning_box">
         The following directories were most likely created during a previous installation of
         conjoon and will be removed:
         <ul>
           <?php if ($INSTALL['IMREMOVING']['js']) { ?>
             <li>../js</li>
           <?php } ?>
           <?php if ($INSTALL['IMREMOVING']['_configCache']) { ?>
             <li>../_configCache</li>
           <?php } ?>
         </ul>
        Please back up any data other than created by conjoon
         to prevent data loss.<br />
      <strong>Note:</strong> Deleting deeply nested folders usually takes some time. Make sure
      your php.ini settings regarding script execution timeout are set to a high enough value, or
      delete the specified folders by hand before you proceed.
       </div>
      </td>
  </tr>
  </tbody>
</table>
<?php } ?>

<h4></h4>

<input type="hidden" name="install_post" value="1" />