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
<input type="hidden" name="install_post" value="1" />