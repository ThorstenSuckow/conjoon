<?php
/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
 * licensing@conjoon.org
 *
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */

/**
 * Patch notes view
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
?>
<div id="0_1_4RC3_container">
 <table>
  <tbody>
   <tr>
    <td colspan="2">
     Choose the time zone your PHP scripts did use before conjoon V0.1.4RC3.
     <br />
     We have tried to autodetect this time zone, and most likely this is
     the time zone you should select for patching the data.
    </td>
   </tr>
   <tr>
    <td style="width:150px">
     Time zone to use:
    </td>
    <td>
     <select name="patchdata[0_1_4RC3][timezone]">
    <?php
        $timezones = file_get_contents('./timezones.txt');
        $timezones = explode("\n", $timezones);

        $detectedTimezone = date_default_timezone_get();

        foreach ($timezones as $timezone) {
            $timezone = trim($timezone);
    ?>
       <option value="<?php echo $timezone; ?>"
               <?php echo ($detectedTimezone == $timezone ? "selected=\"selected\"" : ""); ?>
       />
        <?php echo $timezone; ?>
       </option>
    <?php
        }
    ?>
     </select>
    </td>
   </tr>
  <tbody>
 </table>
</div>