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
      if ($keys[0] != $VIEW['action'] && $currInd != $navCount-1) {
          echo "<input id='prevButton' onclick=\"disableButtons();location.href='./?action=".$keys[$currInd-1]."'\" class=\"proceed_button\" type=\"button\" value=\"&lt; Previous\" />";
      }

      if ($keys[count($keys)-1] != $VIEW['action']) {

          if (isset($VIEW['navigation'][$VIEW['action']][2])) {
              echo "<input id='nextButton' onclick=\"disableButtons();document.forms[0].action='./".$VIEW['navigation'][$VIEW['action']][2]."';document.forms[0].submit();\" class=\"proceed_button\" type=\"button\" value=\"Next &gt;\" />";
          } else {
              echo "<input id='nextButton' onclick=\"disableButtons();document.forms[0].action='./?action=".$keys[$currInd+1]."';document.forms[0].submit();\" class=\"proceed_button\" type=\"button\" value=\"Next &gt;\" />";
          }
      }
  ?>
 </div>
</div>