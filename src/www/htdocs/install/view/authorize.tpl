<!--
 conjoon
 (c) 2007-2014 conjoon.org
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
