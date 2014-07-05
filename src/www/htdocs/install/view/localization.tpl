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

<h3>Localization</h3>
<p>
For proper calculating times and dates, conjoon needs to know the timezone in which the application is running.
</p>
<div class="info_box">
 <strong>Note:</strong> The system's default timezone is currently set to
 <i>"<?php echo $LOCALIZATION['date_timezone']; ?>"</i>.
</div>
<br />
<table>
<tbody>

<tr>
<td colspan="2">Please chose the timezone in which the application runs in</td>
</tr>

<tr>
<td>Application's timezone:</td>
<td><select name="locale_timezone_default">
    <?php for ($i = 0, $len = count($LOCALIZATION['timezone_options']); $i < $len; $i++) { ?>
    <option <?php echo $_SESSION['locale_timezone_default'] == $LOCALIZATION['timezone_options'][$i] ? "selected=\"selected\"" : "" ?> value="<?php echo $LOCALIZATION['timezone_options'][$i]; ?>"><?php echo $LOCALIZATION['timezone_options'][$i]; ?></option>
    <?php } ?>
 </select></td>
</tr>

<tr><td colspan="2">
    <?php echo conjoon_configInfoSnippet('Application\'s timezone', 'application.locale.date.timezone'); ?>
</td></tr>

<tr>
<td colspan="2">&nbsp;</td>
</tr>

<tr>
<td colspan="2"><p>Please chose a fallback timezone</p>
<div class="info_box">
 <strong>Note:</strong>  The fallback timezone is used in case the administrator
changes conjoon's configuration file in a way that the <i>"application.locale.date.timezone"</i>
value cannot be read or is erroneous.
</div>
</td>
</tr>
<tr>
<td>Fallback timezone:</td>
<td><select name="locale_timezone_fallback">
    <?php for ($i = 0, $len = count($LOCALIZATION['timezone_options']); $i < $len; $i++) { ?>
    <option <?php echo $_SESSION['locale_timezone_fallback'] == $LOCALIZATION['timezone_options'][$i] ? "selected=\"selected\"" : "" ?> value="<?php echo $LOCALIZATION['timezone_options'][$i]; ?>"><?php echo $LOCALIZATION['timezone_options'][$i]; ?></option>
    <?php } ?>
 </select></td>
</tr>


<tr>
<td colspan="2">&nbsp;</td>
</tr>


</tbody>
</table>
<input type="hidden" name="localization_check" value="1" />