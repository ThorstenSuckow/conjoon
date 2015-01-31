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

    function conjoon_getthisinfobox() {

        return "<div class=\"info_box\">
                <strong>Note:</strong> If you do not specify absolute paths, the specified directory will
                be treated relative to the application path, which was chosen in Step 4, and points to
                <i><strong>" .
                rtrim($_SESSION['app_path']) . '/' . $_SESSION['setup_ini']['app_path']['folder'] .
                "</strong></i></div>";

    }

?>

<script type="text/javascript">

    function showCacheOptions(show, libName)
    {
        document.getElementById('cacheOptionsContainer_' + libName).style.display =
            show ? '' : 'none';
    }



</script>

<h3>Library specific Settings</h3>
<p>
This step allows for editing settings related to various software tools conjoon uses.
</p>

<div class="settingsContainer type_1">
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
    <input onclick="showCacheOptions(false, 'htmlpurifier');" id="use_cache_radio_no" <?php echo !$LIB_SETTINGS['htmlpurifier.use_cache'] ? "checked=\"checked\"" : ""; ?> type="radio" name="htmlpurifier.use_cache" value="0" /><label for="use_cache_radio_no">No</label>
    <br />
    <input onclick="showCacheOptions(true, 'htmlpurifier');" id="use_cache_radio_yes" <?php echo $LIB_SETTINGS['htmlpurifier.use_cache'] ? "checked=\"checked\"" : ""; ?> type="radio" name="htmlpurifier.use_cache" value="1" /><label for="use_cache_radio_yes">Yes</label>
</div>
<?php echo conjoon_cacheEnabledSnippet('HTMLPurifier Cache enabled', 'application.htmlpurifier.use_cache'); ?>

</p>

<div id="cacheOptionsContainer_htmlpurifier" style="<?php echo $LIB_SETTINGS['htmlpurifier.use_cache'] ? "" : "display:none;" ?>">

<!-- HTMLPURIFIER CACHE PATH-->
 <h4>Path to HTMLPurifier cache</h4>
 <p>
 Specify the path where the HTMLPurifier cache should save its files.
<?php echo conjoon_getthisinfobox(); ?>
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
</div>
<!-- +--------------+ -->
<!-- |   DOCTRINE   | -->
<!-- +--------------+ -->

<div class="settingsContainer type_2">
<h3>Doctrine</h3>
<h4>Enable cache</h4>
<p>
    Set this to either "Yes" or "No". This setting enables Doctrine to cache data for increasing performance.
    <br />
    <br />
    Do you want to enable the Doctrine cache?
<div style="margin:5px">
    <input onclick="showCacheOptions(false, 'doctrine');" id="use_cache_radio_no_doctrine" <?php echo !$LIB_SETTINGS['doctrine.cache.enabled'] ? "checked=\"checked\"" : ""; ?> type="radio" name="doctrine.cache.enabled" value="0" /><label for="use_cache_radio_no_doctrine">No</label>
    <br />
    <input onclick="showCacheOptions(true, 'doctrine');" id="use_cache_radio_yes_doctrine" <?php echo $LIB_SETTINGS['doctrine.cache.enabled'] ? "checked=\"checked\"" : ""; ?> type="radio" name="doctrine.cache.enabled" value="1" /><label for="use_cache_radio_yes_doctrine">Yes</label>
</div>
<?php echo conjoon_cacheEnabledSnippet('Doctrine Cache enabled', 'application.doctrine.cache.enabled'); ?>

</p>

<div id="cacheOptionsContainer_doctrine" style="<?php echo $LIB_SETTINGS['doctrine.cache.enabled'] ? "" : "display:none;" ?>">

<!-- Doctrine different cache settings -->

<?php
    foreach ($DOCTRINE_CACHE_TYPES as $doctrineCacheKey => $doctrineCacheValues) {
?>
<h4>Enable Cache: "<?php echo $doctrineCacheValues['name']; ?>"</h4>
<p>
    Set this to either "Yes" or "No". This setting enables Doctrine to use the
    <?php echo $doctrineCacheValues['name']; ?> for increasing performance.
    <br />
    <br />
    Do you want to enable the <?php echo $doctrineCacheValues['name']; ?>?
<div style="margin:5px">
    <input
        onclick="showCacheOptions(false, 'doctrine_<?php echo $doctrineCacheKey; ?>');"
        id="use_cache_radio_no_doctrine_<?php echo $doctrineCacheKey; ?>"
        <?php echo !$LIB_SETTINGS['doctrine.cache.'.$doctrineCacheKey.'.enabled'] ? "checked=\"checked\"" : ""; ?>
        type="radio" name="doctrine.cache.<?php echo $doctrineCacheKey;?>.enabled" value="0" />
            <label for="use_cache_radio_no_doctrine_<?php echo $doctrineCacheKey; ?>">No</label>
    <br />
    <input
        onclick="showCacheOptions(true, 'doctrine_<?php echo $doctrineCacheKey; ?>');"
        id="use_cache_radio_yes_doctrine_<?php echo $doctrineCacheKey; ?>"
        <?php echo $LIB_SETTINGS['doctrine.cache.'.$doctrineCacheKey.'.enabled'] ? "checked=\"checked\"" : ""; ?>
        type="radio" name="doctrine.cache.<?php echo $doctrineCacheKey;?>.enabled" value="1" />
        <label for="use_cache_radio_yes_doctrine_<?php echo $doctrineCacheKey; ?>">Yes</label>
</div>
<?php echo conjoon_cacheEnabledSnippet(
    'Doctrine ' . $doctrineCacheValues['name'] . ' enabled',
    'application.doctrine.cache.' . $doctrineCacheKey . '.enabled');
    ?>
</p>

<div id="cacheOptionsContainer_doctrine_<?php echo $doctrineCacheKey; ?>"
     style="<?php echo $LIB_SETTINGS['doctrine.cache.' . $doctrineCacheKey . '.enabled'] ? "" : "display:none;" ?>">
    <h5>Select a cache driver to use for the "<?php echo $doctrineCacheValues['name']; ?>"</h5>
    <?php foreach ($DOCTRINE_CACHE_EXTENSIONS as $doctrineCacheExtension) { ?>
        <input
        onclick="showCacheOptions(<?php echo $doctrineCacheExtension == 'file' ? 'true' : 'false'; ?>, 'doctrine_<?php echo $doctrineCacheKey; ?>_file');"
        <?php if($doctrineCacheExtension != 'file' && !extension_loaded($doctrineCacheExtension)) { ?> disabled="disabled" <?php } ?>
        id="use_cache_radio_yes_doctrine_<?php echo $doctrineCacheKey; ?>_<?php echo $doctrineCacheExtension; ?>"
        <?php echo $LIB_SETTINGS['doctrine.cache.'.$doctrineCacheKey.'.type'] == $doctrineCacheExtension ? "checked=\"checked\"" : ""; ?>
        type="radio" name="doctrine.cache.<?php echo $doctrineCacheKey; ?>.type" value="<?php echo $doctrineCacheExtension; ?>" />
        <label
            <?php if($doctrineCacheExtension != 'file' && !extension_loaded($doctrineCacheExtension)) { ?> disabled="disabled" <?php } ?>
            for="use_cache_radio_yes_doctrine_<?php echo $doctrineCacheKey; ?>_<?php echo $doctrineCacheExtension; ?>">use <?php echo $doctrineCacheExtension; ?>
        </label>
        <br />
    <?php } ?>
    <?php echo conjoon_cacheDirSnippet(
    'Doctrine ' . $doctrineCacheValues['name'] . ' type',
    'application.doctrine.cache.' . $doctrineCacheKey . '.type');
    ?>
    <div id="cacheOptionsContainer_doctrine_<?php echo $doctrineCacheKey; ?>_file"
         style="margin-top:5px;<?php echo $LIB_SETTINGS['doctrine.cache.' . $doctrineCacheKey . '.type'] == 'file' ? "" : "display:none;" ?>">
        Path:
        <br />
        <input style="width:100%" type="text" name="doctrine.cache.<?php echo $doctrineCacheKey;?>.dir" value="<?php echo $LIB_SETTINGS['doctrine.cache.'.$doctrineCacheKey.'.dir']; ?>" />
        <?php echo conjoon_cacheDirSnippet(
            'Doctrine '.$doctrineCacheValues['name'].' directory',
            'application.doctrine.cache.' . $doctrineCacheKey . '.dir'
            ); ?>
        <?php echo conjoon_getthisinfobox(); ?>
        <!-- ERRORS -->
        <?php if (isset($LIB_SETTINGS['doctrine.cache.'.$doctrineCacheKey.'.dir.install_failed'])) { ?>
        <?php if ($LIB_SETTINGS['doctrine.cache.'.$doctrineCacheKey.'.dir.install_failed'] === true) { ?>
        <div class="error_box">
            <b>ERROR</b><br />
            <?php echo $FOLDER_CREATE_ERROR; ?>
        </div>
        <?php } ?>
        <?php } ?>
        <!-- ^^ EO ERRORS -->
    </div>

</div>
<?php
    }
?>
<!-- ^^ EO Doctrine different cache settings -->


</div>
</div>

<input type="hidden" name="lib_settings_post" value="1" />

