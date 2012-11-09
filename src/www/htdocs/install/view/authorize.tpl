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

<p>
Please authenticate yourself before using the conjoon Setup Assistant.
</p>

<?php if ($AUTH_ERRORS['key_match']) { ?>
    <div class="error_box">
        The provided key does not match the authorization key.
    </div>
<?php } else if ($AUTH_ERRORS['key_empty']) { ?>
    <div class="error_box">
        You have submitted a key, but the authorization key from the file
        must not be empty.
    </div>
<?php } else if ($AUTH_ERRORS['key_not_found']) { ?>
    <div class="error_box">
        Could not find the authorization key in the file.
    </div>
<?php } else if ($AUTH_ERRORS['file_issue']) { ?>
    <div class="error_box">
        Could not access the file.
    </div>
<?php } else if ($AUTH_ERRORS['no_submit']) { ?>
    <div class="error_box">
        Please enter a valid key.
    </div>
<?php } ?>


<form action="./index.php?action=authorize" method="POST">
Key : <input type="text" name="key" value="" />

    <input type="submit" />
</form>
