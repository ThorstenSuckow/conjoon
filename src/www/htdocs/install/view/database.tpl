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

<h3>Setting up the database</h3>
<p>
conjoon relies on a database backend for storing application data. Please provide your database setup in the following step.
</p>
<div class="info_box">
 <strong>Note:</strong> If you specify a table prefix other than the one you might have specified
 in a previous installation, all tables will be installed again and no data will be migrated to
 the upgraded version. Do not change an already existing table prefix if you want to continue to work with
 your old data in a new version of conjoon.
</div>
<br />
<table>
<tbody>

<?php if (isset($DATABASE['connection_error'])) {?>
<tr>
<td colspan="2"><div class="error_box">
<b>ERROR</b><br />
Error while trying to connect to the database: <br />
<?php echo $DATABASE['connection_error']; ?>
<br />
Please check the user name, password and connection information, and make sure the database "<?php echo $_SESSION['db'] ?>"
does exist.
</div></td>
</tr>
<tr>
<td colspan="2">&nbsp;</td>
</tr>
<?php } ?>

<tr>
<td colspan="2">Please chose the type of database that is used</td>
</tr>
<?php if (in_array('db_adapter', $DATABASE['missing'])) {?>
<tr>
<td colspan="2"><div class="error_box">
<b>ERROR</b><br />
Please chose the type of database.
</div></td>
</tr>
<?php } ?>
<?php if (!$DATABASE['pdo_extension_loaded']) {?>
<tr>
<td colspan="2"><div class="error_box">
<b>ERROR</b><br />
PDO extension not available. Can't proceed. Please install the PDO extension for PHP first.
</div></td>
</tr>
<?php } ?>
<?php if ($DATABASE['pdo_extension_loaded'] && !$DATABASE['pdo_mysql_available']) {?>
<tr>
<td colspan="2"><div class="error_box">
<b>ERROR</b><br />
mysql-adapter for PDO not available. Can't proceed. Please install the mysql-adapter for the PDO extension first.
</div></td>
</tr>
<?php } ?>
<tr>
<td>Database type:</td>
<td><select name="db_adapter">
    <?php for ($i = 0, $len = count($DATABASE['adapters']); $i < $len; $i++) { ?>
    <option <?php echo $_SESSION['db_adapter'] == $DATABASE['adapters'][$i]['value'] ? "selected=\"selected\"" : "" ?> value="<?php echo $DATABASE['adapters'][$i]['value']; ?>"><?php echo $DATABASE['adapters'][$i]['option']; ?></option>
    <?php } ?>
 </select></td>
</tr>

<tr>
<td colspan="2">&nbsp;</td>
</tr>

<tr>
<td colspan="2">Please enter the address of the host the database is running on. On most setups, this will
default to <i>localhost</i> (<i>127.0.0.1</i>)</td>
</tr>
<?php if (in_array('db_host', $DATABASE['missing'])) {?>
<tr>
<td colspan="2"><div class="error_box">
<b>ERROR</b><br />
Please provide the host where the database runs on.
</div></td>
</tr>
<?php } ?>
<tr>
<td>Database host:</td>
<td><input type="text" name="db_host" value="<?php echo (($_SESSION['db_host'] !== null) ? $_SESSION['db_host'] : '127.0.0.1') ?>" /></td>
</tr>


<tr>
<td colspan="2">&nbsp;</td>
</tr>

<tr>
<td colspan="2">Please enter the port your database listens to. On most setups, this will
default to port <i>3306</i> for MySQL-databases</td>
</tr>
<?php if (in_array('db_port', $DATABASE['missing'])) {?>
<tr>
<td colspan="2"><div class="error_box">
<b>ERROR</b><br />
Please provide the port where your database service listens to.
</div></td>
</tr>
<?php } ?>
<td>Database port:</td>
<td><input type="text" name="db_port" value="<?php echo (($_SESSION['db_port'] !== null) ? $_SESSION['db_port'] : '3306') ?>" /></td>
</tr>


<tr>
<td colspan="2">&nbsp;</td>
</tr>

<tr>
<td colspan="2">Please enter the name of the database where the conjoon tables should be installed to. <br />
<b>NOTE:</b> The database must already exist!</td>
</tr>
<?php if (in_array('db', $DATABASE['missing'])) {?>
<tr>
<td colspan="2"><div class="error_box">
<b>ERROR</b><br />
Please provide the name of the database.
</div></td>
</tr>
<?php } ?>
<td>Database:</td>
<td><input type="text" name="db" value="<?php echo (($_SESSION['db'] !== null) ? $_SESSION['db'] : '') ?>" /></td>
</tr>


<tr>
<td colspan="2">&nbsp;</td>
</tr>

<tr>
<td colspan="2">You can enter a prefix here that gets prepended to the tables created by conjoon.
    This is helpful in cases where there is only one database available that gets used by multiple
    applications, to prevent namespace clashes.
    <br />
    <div class="info_box">
        <strong>Note:</strong>
        The prefix entered here gets exactly prepended as specified. For example, specifying the prefix "cj_" will
        rename the table "users" to "cj_users". <br />Leave the field empty if you do not want to use
        a table prefix. This is only recommended for databases that were created explicitly for conjoon.
    </div>
</tr>
<?php if ($DATABASE['db_table_prefix_failed']) {?>
<tr>
<td colspan="2"><div class="error_box">
<b>ERROR</b><br />
A table prefix may only contain letters, numbers and underscores. For example, a valid prefix would be
"cj_".
</div></td>
</tr>
<?php } ?>
<td>Table prefix:</td>
<td><input type="text" name="db_table_prefix" value="<?php echo (($_SESSION['db_table_prefix'] !== null) ? $_SESSION['db_table_prefix'] : '') ?>" /></td>
</tr>


<tr>
<td colspan="2">&nbsp;</td>
</tr>

<tr>
<tr>
<td colspan="2">Please enter the user name that should be used for connecting to the database.</td>
</tr>
<?php if (in_array('db_user', $DATABASE['missing'])) {?>
<tr>
<td colspan="2"><div class="error_box">
<b>ERROR</b><br />
Please provide the user name.
</div></td>
</tr>
<?php } ?>
<tr>
<td>Database user:</td>
<td><input type="text" name="db_user" value="<?php echo (($_SESSION['db_user'] !== null) ? $_SESSION['db_user'] : '') ?>" /></td>
</tr>

<tr>
<td colspan="2">&nbsp;</td>
</tr>

<tr>
<td colspan="2">Please enter the password for the above user name for connecting to the database.</td>
</tr>
<tr>
<td>Database password:</td>
<td><input type="text" name="db_password" value="<?php echo (($_SESSION['db_password'] !== null) ? $_SESSION['db_password'] : '') ?>" /></td>
</tr>

<tr>
<td colspan="2">&nbsp;</td>
</tr>

<tr>
<td colspan="2">Please enter the maximum size of the packet that is allowed to be stored in the database, in bytes.
Any packet's size that is larger than the given value will not be stored in the database. If you do not know which value
to provide, leave this field empty, as conjoon will try to determine the maximum size during runtime.</td>
</tr>
<?php if ($DATABASE['max_allowed_packet_failed']) {?>
<tr>
<td colspan="2"><div class="error_box">
<b>ERROR</b><br />
The value you provided for the max_allowed_packet failed. The value must not be greater than
<?php echo $DATABASE['max_allowed_packet'];?>.
</div></td>
</tr>
<?php } ?>
<tr>
<td>Max allowed packet:</td>
<td><input type="text" name="max_allowed_packet" value="<?php echo (($_SESSION['max_allowed_packet'] !== null) ? $_SESSION['max_allowed_packet'] : '') ?>" /></td>
</tr>

</tbody>
</table>
<input type="hidden" name="database_check" value="1" />