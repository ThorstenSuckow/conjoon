<!--
 conjoon
 (c) 2002-2012 siteartwork.de/conjoon.org
 licensing@conjoon.org

 $Author: T. Suckow $
 $Id: cache.tpl 1540 2012-11-06 23:19:11Z T. Suckow $
 $Date: 2012-11-07 00:19:11 +0100 (Mi, 07 Nov 2012) $
 $Revision: 1540 $
 $LastChangedDate: 2012-11-07 00:19:11 +0100 (Mi, 07 Nov 2012) $
 $LastChangedBy: T. Suckow $
 $URL: http://svn.conjoon.org/trunk/src/www/htdocs/install/view/cache.tpl $
-->

<?php
    $FOLDER_CREATE_ERROR = "Sorry, I tried to create the specified folder but "
                           ."could not succeed. Please make sure your webserver and PHP have "
                           ."appropriate rights to create a folder at the specified location.";

?>

<script type="text/javascript">

    function showCacheOptions(show)
    {
        document.getElementById('cacheOptionsContainer').style.display =
            show ? '' : 'none';
    }



</script>

<h3>Library specific Settings</h3>
<p>
This step allows for editing settings related to various software tools conjoon uses.
</p>


<h3>HTMLPurifier</h3>
<h4>Preload HTMLPurifier library files</h4>
<p>
    Set this to either "Yes" or "No". <br />
    Preloading files neccessary for HTMLPurifier is recommended on systems using an opcode cache, such as APC. It might
    result in performance loss on systems that do not provide this functionality.
    <br />
    <br />
    Do you want to preload HTMLPurifier related files?

<div style="margin:5px">
    <input id="preload_all_radio_no" <?php echo !$LIB_SETTINGS['htmlpurifier.preload_all'] ? "checked=\"checked\"" : ""; ?> type="radio" name="htmlpurifier.preload_all" value="0" /><label for="preload_all_radio_no">No</label>
    <br />
    <input id="preload_all_radio_yes" <?php echo $LIB_SETTINGS['htmlpurifier.preload_all'] ? "checked=\"checked\"" : ""; ?> type="radio" name="htmlpurifier.preload_all" value="1" /><label for="preload_all_radio_yes">Yes</label>
    </div>
    <?php echo conjoon_cacheEnabledSnippet('Preload HTMLPurifier files', 'application.htmlpurifier.preload_all'); ?>

</p>

<h4>Enable cache</h4>
<p>
Set this to either "Yes" or "No". This setting enables HTMLPurifier to cache data for increasing performance.
<br />
<br />
Do you want to enable the HTMLPurifier cache?
<div style="margin:5px">
    <input onclick="showCacheOptions(false);" id="use_cache_radio_no" <?php echo !$LIB_SETTINGS['htmlpurifier.use_cache'] ? "checked=\"checked\"" : ""; ?> type="radio" name="htmlpurifier.use_cache" value="0" /><label for="use_cache_radio_no">No</label>
    <br />
    <input onclick="showCacheOptions(true);" id="use_cache_radio_yes" <?php echo $LIB_SETTINGS['htmlpurifier.use_cache'] ? "checked=\"checked\"" : ""; ?> type="radio" name="htmlpurifier.use_cache" value="1" /><label for="use_cache_radio_yes">Yes</label>
</div>
<?php echo conjoon_cacheEnabledSnippet('HTMLPurifier Cache enabled', 'application.htmlpurifier.use_cache'); ?>

</p>

<div id="cacheOptionsContainer" style="<?php echo $LIB_SETTINGS['htmlpurifier.use_cache'] ? "" : "display:none;" ?>">

<!-- HTMLPURIFIER CACHE PATH-->
 <h4>Path to HTMLPurifier cache</h4>
 <p>
 Specify the path where the HTMLPurifier cache should save its files.
<div class="info_box">
    <strong>Note:</strong> If you do not specify absolute paths, the specified directory will
    be treated relative to the application path, which was chosen in Step 3, and points to
    <i><strong><?php echo rtrim($_SESSION['app_path']) . '/' . $_SESSION['setup_ini']['app_path']['folder']; ?></strong></i>.
</div>
 <!-- ERRORS -->
 <?php if (isset($LIB_SETTINGS['htmlpurifier.cache_dir.install_failed'])) { ?>
     <?php if ($LIB_SETTINGS['htmlpurifier.cache_dir.install_failed'] === true) { ?>
         <div class="error_box">
         <b>ERROR</b><br />
         <?php echo $FOLDER_CREATE_ERROR; ?>
         </div>
     <?php } ?>
 <?php } ?>
 <!-- ^^ EO ERRORS -->
  Path:
  <br />
  <input style="width:100%" type="text" name="htmlpurifier.cache_dir" value="<?php echo $LIB_SETTINGS['htmlpurifier.cache_dir']; ?>" />
  <?php echo conjoon_cacheDirSnippet('HTMLPurifier cache directory', 'application.htmlpurifier.cache_dir'); ?>
 </p>
<!-- ^^ EO HTMLPURIFIER CACHE -->

</div>



<input type="hidden" name="lib_settings_post" value="1" />

