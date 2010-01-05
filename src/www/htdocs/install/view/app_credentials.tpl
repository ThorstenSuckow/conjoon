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