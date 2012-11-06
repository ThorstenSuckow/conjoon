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