<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
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