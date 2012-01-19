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
<script type="text/javascript">

    function togglePatchDisabled(el, disable)
    {
        try {
            el.disabled = disable;
        } catch(e) {
            // ignore
        }
        if (el.childNodes && el.childNodes.length > 0 && el.tagName.toLowerCase() != "select") {
            for (var x = 0; x < el.childNodes.length; x++) {
                togglePatchDisabled(el.childNodes[x], disable);
            }
        }
    }
</script>
<h3>Patching</h3>
<p>
Patches are needed in order to keep previously stored data of conjoon compatible with data written
to the data storage by future versions of conjoon.
</p>
<?php if (empty($PATCH_NOTES)) { ?>
<div class="warning_box">
 <strong>Note:</strong> there are currently no patches available for your version.
</div>
<?php } else { ?>
<div class="info_box">
 <strong>Note:</strong> It is recommended that you apply all patches listed on this page during
 installation.
</div>
<?php } ?>
<br />
<table>
<tbody>

<?php foreach ($PATCH_NOTES as $version => $info) { ?>
<tr>
 <td>
  <h4><?php echo $info['headline']; ?></h4>
 </td>
</tr>
<?php if (in_array($version, $ignoredPatches)) { ?>
<tr>
 <td>
  <div class="warning_box">
   <strong>Note:</strong> You ignored this patch during a previous update of conjoon. You can apply this patch
   now if you want.
  </div>
 </td>
</tr>
<?php } ?>
<tr>
 <td>
  <?php echo $info['title']; ?>
 </td>
</tr>
<tr>
 <td>
  <?php echo $info['description']; ?>
 </td>
</tr>
<?php if (!empty($info['link'])) { ?>
<tr>
 <td>
  For more information about this patch, see:
  <ul>
    <?php foreach ($info['link'] as $link) { ?>
     <li><a href="<?php echo $link?>" target="_blank"><?php echo $link?></a></li>
    <?php } ?>
  </ul>
 </td>
</tr>
<?php } ?>
<tr>
 <td>
 <?php
    if (file_exists('./patches/'.$version.'/view.php')) {
        include_once './patches/'.$version.'/view.php';
    }
 ?>
 </td>
</tr>
<tr>
 <td>
   <?php $rversion = str_replace('.', '_', $version); ?>
   <script type="text/javascript">
       function activate<?php echo $rversion;?>(elem)
       {
           var cont = document.getElementById('<?php echo $rversion; ?>_container');
           if (!cont) {
               return;
           }

           if (elem.value == "1") {
               togglePatchDisabled(cont, false);
           } else {
               togglePatchDisabled(cont, true);
           }
       }
   </script>
   <input onclick="activate<?php echo $rversion;?>(this);" checked="checked" id="<?php echo $version; ?>_1" type="radio" name="patch[<?php echo str_replace('.', '_', $version); ?>]" value="1" />
   <label for="<?php echo $version; ?>_1"><strong>YES! Apply this patch!</strong></label>
   <br />
   <input onclick="activate<?php echo $rversion;?>(this);" id="<?php echo $version; ?>_0" type="radio" name="patch[<?php echo str_replace('.', '_', $version); ?>]" value="0" />
   <label for="<?php echo $version; ?>_0">No, thanks, do not apply this patch. Don't ask, I absolutely know what I'm doing!</label>
 </td>
</tr>


<tr>
<td colspan="2">&nbsp;</td>
</tr>

<?php } ?>


</tbody>
</table>
<input type="hidden" name="patch_check" value="1" />