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

<h3>Specifying user credentials</h3>
<p>
 conjoon comes with a default user - you can sign up to the application right away using
 the following credentials:<br />
 User name: <span style="font-family:Courier New">admin</span><br />
 password: <span style="font-family:Courier New">password</span><br />
 <br />
 <b>It is strongly recommended that you change the login credentials NOW! Otherwise any other user who is aware
 of the default user name and password can gain access to your private data stored by conjoon!</b>
 <br />
 <br />
 <br />

<?php if ($APPCREDENTIALS['user_missing']) {?>
<div class="error_box">
<b>ERROR</b><br />
You need to specify a valid user name.
</div>
<?php } ?>
 User name: <input type="text" name="user" value="<?php echo $_SESSION['app_credentials']['user']; ?>" />
 <br />
 <br />
 <?php if ($APPCREDENTIALS['password_missing']) {?>
<div class="error_box">
<b>ERROR</b><br />
You need to specify a valid password.
</div>
<?php } ?>
 Password:&nbsp;  <input type="text" name="password" value="<?php echo $_SESSION['app_credentials']['password']; ?>" />
 <input type="hidden" name="app_credentials_post" value="1" />
</p>

<br />
<br />
<?php if ($APPCREDENTIALS['firstname_missing']) {?>
<div class="error_box">
<b>ERROR</b><br />
You need to specify a valid first name.
</div>
<?php } ?>
 First name:&nbsp;&nbsp;&nbsp;&nbsp; <input type="text" name="firstname" value="<?php echo $_SESSION['app_credentials']['firstname']; ?>" />

 <br />
 <br />
<?php if ($APPCREDENTIALS['lastname_missing']) {?>
<div class="error_box">
<b>ERROR</b><br />
You need to specify a valid last name.
</div>
<?php } ?>
 Last name:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="text" name="lastname" value="<?php echo $_SESSION['app_credentials']['lastname']; ?>" />
 <br />
 <br />
<?php if ($APPCREDENTIALS['emailaddress_missing']) {?>
<div class="error_box">
<b>ERROR</b><br />
You need to specify an email address.
</div>
<?php } ?>
 Email address: <input type="text" name="email_address" value="<?php echo $_SESSION['app_credentials']['email_address']; ?>" />