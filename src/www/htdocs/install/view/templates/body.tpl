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

<div id="content" class="page_template">

<?php if (isset($_SESSION['check_failed']) && $_SESSION['check_failed']) { ?>
<div class="warning_box">
<b>WARNING</b><br />
Your server does not match the pre-requisites for a sucessfull conjoon installation.
You can continue with the setup process, but be warned that your installation might
not work as expected.
</div>
<?php } ?>

 <div class="post">
  <div class="entry">
   <h2><?php echo $VIEW['navigation'][$VIEW['action']][0]; ?></h2>
   <form method="post" style="text-align:left;margin:0px;padding:0px;">
   <?php echo $VIEW['content']; ?>
   </form>
  </div>
 </div>
 <div style="text-align:right">
    <script type="text/javascript">

        function disableButtons()
        {
            var prev = document.getElementById('prevButton');
            var next = document.getElementById('nextButton');

            if (prev) {
                prev.disabled = true;
            }

            if (next) {
                next.disabled = true;
            }
        }

    </script>

  <?php
      $keys    = array_keys($VIEW['navigation']);
      $currInd = 0;
      $navCount = count($keys);
      for ($i = 0, $len = count($keys); $i < $len; $i++) {
          if ($keys[$i] == $VIEW['action']) {
             $currInd = $i;
             break;
          }
      }
      if ($keys[0] != $VIEW['action'] && $currInd != $navCount-1 && $action !== 'install_process') {
          echo "<input id='prevButton' onclick=\"disableButtons();location.href='./?action=".$keys[$currInd-1]."'\" class=\"proceed_button\" type=\"button\" value=\"&lt; Previous\" />";
      }

      if ($keys[count($keys)-1] != $VIEW['action'] && $action !== 'install_process') {
          if (isset($VIEW['navigation'][$VIEW['action']][2])) {
              echo "<input id='nextButton' onclick=\"disableButtons();document.forms[0].action='./".$VIEW['navigation'][$VIEW['action']][2]."';document.forms[0].submit();\" class=\"proceed_button\" type=\"button\" value=\"Next &gt;\" />";
          } else {
              echo "<input id='nextButton' onclick=\"disableButtons();document.forms[0].action='./?action=".$keys[$currInd+1]."';document.forms[0].submit();\" class=\"proceed_button\" type=\"button\" value=\"Next &gt;\" />";
          }
      } else if ($action === 'install_process') {
            echo "<input disabled=\"disabled\" onclick=\"document.forms[0].action='./index.php?action=install_success';document.forms[0].submit();\" id='nextButton' class=\"proceed_button\" type=\"button\" value=\"Next &gt;\" />";
      }
  ?>
 </div>
</div>