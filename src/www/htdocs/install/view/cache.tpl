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

<h3>Cache Settings</h3>
<p>
conjoon will cache often used data to prevent frequent database or network access. <br />
You can chose whether to use caching and specify the location for the cache.
For fine tuning the cache options, you can edit the configuration file for conjoon
once the installation has finished.
<br />
If you enable multiple cache options, please make sure you are specifying individual
directories for each option.
</p>

<div class="info_box">
 <strong>Note:</strong> If you do not specify absolute paths, the specified directories will
 be treated relative to the application path, which was chosen in Step 4, and points to
 <i><strong><?php echo rtrim($_SESSION['app_path']) . '/' . $_SESSION['setup_ini']['app_path']['folder']; ?></strong></i>.
</div>


<h3>Enable cache</h3>
<p>
Set this to either "Yes" or "No". If caching is disabled, you can activate it later on by
editing the configuration file of conjoon.
<br />
<br />
Do you want to enable caching?
<div style="margin:5px">
 <input onclick="showCacheOptions(true);" id="radio_yes" <?php echo $CACHE['default.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="default.caching" value="1" /><label for="radio_yes">Yes</label>
  <br />
 <input onclick="showCacheOptions(false);" id="radio_no" <?php echo !$CACHE['default.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="default.caching" value="0" /><label for="radio_no">No</label>
</div>
<?php echo conjoon_cacheEnabledSnippet('Cache enabled', 'cache.default.caching'); ?>

</p>

<div id="cacheOptionsContainer" style="<?php echo $CACHE['default.caching'] ? "" : "display:none;" ?>">

<!-- DB CACHE -->
 <h4>1. Database Cache</h4>
 <p>
 This cache is used to store often needed metadata information. It is recommended to enable this
 cache, otherwise conjoon has to read out metadata information on each request.
 <br />
 <br />
 Do you want to enable database metadata caching?
 <div style="margin:5px">
  <input id="dbcache_yes" <?php echo $CACHE['db.metadata.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="db.metadata.caching" value="1" /><label for="dbcache_yes">Yes</label>
   <br />
  <input id="dbcache_no" <?php echo !$CACHE['db.metadata.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="db.metadata.caching" value="0" /><label for="dbcache_no">No</label>
 </div>
 <?php echo conjoon_cacheEnabledSnippet('Database metadata cache enabled', 'cache.db.metadata.caching'); ?>


 <!-- ERRORS -->
 <?php if (isset($CACHE['db.metadata.install_failed'])) { ?>
     <?php if ($CACHE['db.metadata.install_failed'] === true) { ?>
         <div class="error_box">
         <b>ERROR</b><br />
         <?php echo $FOLDER_CREATE_ERROR; ?>
         </div>
     <?php } ?>
 <?php } ?>
 <!-- ^^ EO ERRORS -->
  Path:
  <br />
  <input style="width:100%" id="db_cache_path" type="text" name="db.metadata.backend.cache_dir" value="<?php echo $CACHE['db.metadata.backend.cache_dir']; ?>" />
  <?php echo conjoon_cacheDirSnippet('Database metadata cache directory', 'cache.db.metadata.backend.cache_dir'); ?>
 </p>
<!-- ^^ EO DB CACHE -->

<!-- EMAIL CACHE -->
<br />
<br />
 <h4>2. Email Cache</h4>
 <p>
 This option provides caching functionality for email messages and email accounts.

 <!-- EMAIL MESSAGES -->
 <br />
 <br />
 <h5>2.1 Email messages</h5>
 This option allows for caching email messages when they are read. Enabling this cache will
 prevent conjoon from parsing already read email messages over and over again. It is
 recommended that the specified cache folder is not readable by unauthorized users.
 <br />
 <br />
 Do you want to enable caching of single email messages?
 <div style="margin:5px">
  <input id="emailmessage_yes" <?php echo $CACHE['email.message.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="email.message.caching" value="1" /><label for="emailmessage_yes">Yes</label>
   <br />
  <input id="emailmessage_no" <?php echo !$CACHE['email.message.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="email.message.caching" value="0" /><label for="emailmessage_no">No</label>
 </div>
 <?php echo conjoon_cacheEnabledSnippet('Email message cache enabled', 'cache.email.message.caching'); ?>


 <!-- ERRORS -->
 <?php if (isset($CACHE['email.message.install_failed'])) { ?>
     <?php if ($CACHE['email.message.install_failed'] === true) { ?>
         <div class="error_box">
         <b>ERROR</b><br />
         <?php echo $FOLDER_CREATE_ERROR; ?>
         </div>
     <?php } ?>
 <?php } ?>
 <!-- ^^ EO ERRORS -->
  Path:
  <br />
  <input style="width:100%" id="emailmessage_cache_path" type="text" name="email.message.backend.cache_dir" value="<?php echo $CACHE['email.message.backend.cache_dir']; ?>" />
  <?php echo conjoon_cacheDirSnippet('Email message cache directory', 'cache.email.message.backend.cache_dir'); ?>
 <!-- ^^ EO EMAIL MESSAGES -->

 <!-- EMAIL ACCOUNTS -->
 <br />
 <br />
 <h5>2.2 Email accounts</h5>
 Enabling this option speeds up querying the server for email accounts belonging to a specific user.
 No passwords will be stored in this cache. However, the specified folder should not be readable by
 unauthorized users.
 <br />
 <br />
 Do you want to enable caching of email accounts?
 <div style="margin:5px">
  <input id="emailaccounts_yes" <?php echo $CACHE['email.accounts.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="email.accounts.caching" value="1" /><label for="emailaccounts_yes">Yes</label>
   <br />
  <input id="emailaccounts_no" <?php echo !$CACHE['email.accounts.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="email.accounts.caching" value="0" /><label for="emailaccounts_no">No</label>
 </div>
 <?php echo conjoon_cacheEnabledSnippet('Email accounts cache enabled', 'cache.email.accounts.caching'); ?>
 <!-- ERRORS -->
 <?php if (isset($CACHE['email.accounts.install_failed'])) { ?>
     <?php if ($CACHE['email.accounts.install_failed'] === true) { ?>
         <div class="error_box">
         <b>ERROR</b><br />
         <?php echo $FOLDER_CREATE_ERROR; ?>
         </div>
     <?php } ?>
 <?php } ?>
 <!-- ^^ EO ERRORS -->
  Path:
  <br />
  <input style="width:100%" id="emailaccounts_cache_path" type="text" name="email.accounts.backend.cache_dir" value="<?php echo $CACHE['email.accounts.backend.cache_dir']; ?>" />
  <?php echo conjoon_cacheDirSnippet('Email accounts cache directory', 'cache.email.accounts.backend.cache_dir'); ?>
 <!-- ^^ EO EMAIL ACCOUNTS -->

<!-- EMAIL FOLDERS ROOT TYPES -->
<br />
<br />
<h5>2.3 Email Folders' Root Type</h5>
Enabling this option speeds up querying the type of an email folder's root folder.
<br />
<br />
Do you want to enable caching of an email folder's root type?
<div style="margin:5px">
    <input id="emailfoldersroottype_yes" <?php echo $CACHE['email.folders_root_type.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="email.foldersRootType.caching" value="1" /><label for="emailfoldersroottype_yes">Yes</label>
    <br />
    <input id="emailfoldersroottype_no" <?php echo !$CACHE['email.folders_root_type.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="email.foldersRootType.caching" value="0" /><label for="emailfoldersroottype_no">No</label>
</div>
<?php echo conjoon_cacheEnabledSnippet('Email Folders\' Root Type cache enabled', 'cache.email.folders_root_type.caching'); ?>
<!-- ERRORS -->
<?php if (isset($CACHE['email.folders_root_type.install_failed'])) { ?>
    <?php if ($CACHE['email.folders_root_type.install_failed'] === true) { ?>
        <div class="error_box">
            <b>ERROR</b><br />
            <?php echo $FOLDER_CREATE_ERROR; ?>
        </div>
    <?php } ?>
<?php } ?>
<!-- ^^ EO ERRORS -->
Path:
<br />
<input style="width:100%" id="emailfoldersroottype_cache_path" type="text" name="email.foldersRootType.backend.cache_dir" value="<?php echo $CACHE['email.folders_root_type.backend.cache_dir']; ?>" />
<?php echo conjoon_cacheDirSnippet('Email Folders\' Root Type cache directory', 'cache.email.folders_root_type.backend.cache_dir'); ?>
<!-- ^^ EO EMAIL ACCOUNTS -->


 </p>
<!-- ^^ EO EMAIL CACHE -->


<!-- FEED CACHE -->
<br />
<br />
 <h4>3. Feed Cache</h4>
 <p>
 This option provides caching functionality for feed entries and feed accounts.

 <!-- FEED ITEMS-->
 <br />
 <br />
 <h5>3.1 Feed items</h5>
 This option allows for caching feed items when they are read. Enabling this cache will
 prevent conjoon from parsing already read feed items over and over again.
 <br />
 <br />
 Do you want to enable caching of single feed items?
 <div style="margin:5px">
  <input id="fi_yes" <?php echo $CACHE['feed.item.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="feed.item.caching" value="1" /><label for="fi_yes">Yes</label>
   <br />
  <input id="fi_no" <?php echo !$CACHE['feed.item.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="feed.item.caching" value="0" /><label for="fi_no">No</label>
 </div>
 <?php echo conjoon_cacheEnabledSnippet('Feed item cache enabled', 'cache.feed.item.caching'); ?>

 <!-- ERRORS -->
 <?php if (isset($CACHE['feed.item.install_failed'])) { ?>
     <?php if ($CACHE['feed.item.install_failed'] === true) { ?>
         <div class="error_box">
         <b>ERROR</b><br />
         <?php echo $FOLDER_CREATE_ERROR; ?>
         </div>
     <?php } ?>
 <?php } ?>
 <!-- ^^ EO ERRORS -->
  Path:
  <br />
  <input style="width:100%" id="fi_cache_path" type="text" name="feed.item.backend.cache_dir" value="<?php echo $CACHE['feed.item.backend.cache_dir']; ?>" />
  <?php echo conjoon_cacheDirSnippet('Feed item cache directory', 'cache.feed.item.backend.cache_dir'); ?>
 <!-- ^^ EO FEED ITEMS -->

 <!-- FEED LISTS -->
 <br />
 <br />
 <h5>3.2 Feed lists</h5>
 Enabling this option speeds up querying the server for lists of feed items.
 <br />
 <br />
 Do you want to enable caching of feed lists?
 <div style="margin:5px">
  <input id="fl_yes" <?php echo $CACHE['feed.item_list.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="feed.itemList.caching" value="1" /><label for="fl_yes">Yes</label>
   <br />
  <input id="fl_no" <?php echo !$CACHE['feed.item_list.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="feed.itemList.caching" value="0" /><label for="fl_no">No</label>
 </div>
 <?php echo conjoon_cacheEnabledSnippet('Feed item list cache enabled', 'cache.feed.item_list.caching'); ?>

 <!-- ERRORS -->
 <?php if (isset($CACHE['feed.item_list.install_failed'])) { ?>
     <?php if ($CACHE['feed.item_list.install_failed'] === true) { ?>
         <div class="error_box">
         <b>ERROR</b><br />
         <?php echo $FOLDER_CREATE_ERROR; ?>
         </div>
     <?php } ?>
 <?php } ?>
 <!-- ^^ EO ERRORS -->
  Path:
  <br />
  <input style="width:100%" id="fl_cache_path" type="text" name="feed.itemList.backend.cache_dir" value="<?php echo $CACHE['feed.item_list.backend.cache_dir']; ?>" />
  <?php echo conjoon_cacheDirSnippet('Feed item list cache directory', 'cache.feed.item_list.backend.cache_dir'); ?>
<!-- ^^ EO FEED LISTS -->


 <!-- FEED ACCOUNTS -->
 <br />
 <br />
 <h5>3.3 Feed accounts</h5>
 Enabling this option speeds up querying the server for feed accounts belonging to a specific user.
 <br />
 <br />
 Do you want to enable caching of single feed accounts?
 <div style="margin:5px">
  <input id="fa_yes" <?php echo $CACHE['feed.account.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="feed.account.caching" value="1" /><label for="fa_yes">Yes</label>
   <br />
  <input id="fa_no" <?php echo !$CACHE['feed.account.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="feed.account.caching" value="0" /><label for="fa_no">No</label>
 </div>
<?php echo conjoon_cacheEnabledSnippet('Feed accounts cache enabled', 'cache.feed.account.caching'); ?>

 <!-- ERRORS -->
 <?php if (isset($CACHE['feed.account.install_failed'])) { ?>
     <?php if ($CACHE['feed.account.install_failed'] === true) { ?>
         <div class="error_box">
         <b>ERROR</b><br />
         <?php echo $FOLDER_CREATE_ERROR; ?>
         </div>
     <?php } ?>
 <?php } ?>
 <!-- ^^ EO ERRORS -->
  Path:
  <br />
  <input style="width:100%" id="fa_cache_path" type="text" name="feed.account.backend.cache_dir" value="<?php echo $CACHE['feed.account.backend.cache_dir']; ?>" />
  <?php echo conjoon_cacheDirSnippet('Feed accounts cache directory', 'cache.feed.account.backend.cache_dir'); ?>
<!-- ^^ EO FEED ACCOUNTS -->


 <!-- FEED ACCOUNTS -->
 <br />
 <br />
 <h5>3.4 Feed account lists</h5>
 Enabling this option speeds up querying the server for feed account lists belonging to a specific user.
 <br />
 <br />
 Do you want to enable caching of feed account lists?
 <div style="margin:5px">
  <input id="fal_yes" <?php echo $CACHE['feed.account_list.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="feed.accountList.caching" value="1" /><label for="fal_yes">Yes</label>
   <br />
  <input id="fal_no" <?php echo !$CACHE['feed.account_list.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="feed.accountList.caching" value="0" /><label for="fal_no">No</label>
 </div>
 <?php echo conjoon_cacheEnabledSnippet('Feed account list cache enabled', 'cache.feed.account_list.caching'); ?>

 <!-- ERRORS -->
 <?php if (isset($CACHE['feed.account_list.install_failed'])) { ?>
     <?php if ($CACHE['feed.account_list.install_failed'] === true) { ?>
         <div class="error_box">
         <b>ERROR</b><br />
         <?php echo $FOLDER_CREATE_ERROR; ?>
         </div>
     <?php } ?>
 <?php } ?>
 <!-- ^^ EO ERRORS -->
  Path:
  <br />
  <input style="width:100%" id="fal_cache_path" type="text" name="feed.accountList.backend.cache_dir" value="<?php echo $CACHE['feed.account_list.backend.cache_dir']; ?>" />
  <?php echo conjoon_cacheDirSnippet('Feed account list cache directory', 'cache.feed.account_list.backend.cache_dir'); ?>
 <!-- ^^ EO FEED ACCOUNTS -->



 <!-- FEED READER -->
 <br />
 <br />
 <h5>3.5 Feed Reader</h5>
 The reader responsible for querying servers for feeds is capable of deciding whether it shoould read out
 and parse feeds from a server or not. If it decides to not read out feeds' contents due to the fact that
 no content has been modified since the last query, the reader can use an internal cache to speed up delivering
 feed items to the user.<br /> It is recommended to enable this option to reduce network traffic.
 <br />
 <br />
 Do you want to enable caching-options for the feed reader?
 <div style="margin:5px">
  <input id="fr_yes" <?php echo $CACHE['feed.reader.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="feed.reader.caching" value="1" /><label for="fr_yes">Yes</label>
   <br />
  <input id="fr_no" <?php echo !$CACHE['feed.reader.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="feed.reader.caching" value="0" /><label for="fr_no">No</label>
   <?php echo conjoon_cacheEnabledSnippet('Feed reader cache enabled', 'cache.feed.reader.caching'); ?>
 </div>

 <!-- ERRORS -->
 <?php if (isset($CACHE['feed.reader.install_failed'])) { ?>
     <?php if ($CACHE['feed.reader.install_failed'] === true) { ?>
         <div class="error_box">
         <b>ERROR</b><br />
         <?php echo $FOLDER_CREATE_ERROR; ?>
         </div>
     <?php } ?>
 <?php } ?>
 <!-- ^^ EO ERRORS -->
  Path:
  <br />
  <input style="width:100%" id="fr_cache_path" type="text" name="feed.reader.backend.cache_dir" value="<?php echo $CACHE['feed.reader.backend.cache_dir']; ?>" />
  <?php echo conjoon_cacheDirSnippet('Feed reader cache directory', 'cache.feed.reader.backend.cache_dir'); ?>
 <!-- ^^ EO FEED READER -->

 </p>
<!-- ^^ EO FEED CACHE -->


<!-- DB CACHE -->
 <br />
 <br />
 <h4>4. Twitter Cache</h4>
 <p>
 When working with the Twitter client, account information will be fetched from the Twitter servers.
 To prevent frequent querying the Twitter servers, a cache will keep informations about the used accounts
 for a specific time until the cache is invalidated.<br />
 It is recommended to enable this cache to prevent frequent network access.
 <br />
 <br />
 Do you want to enable Twitter account caching?
 <div style="margin:5px">
  <input id="ta_yes" <?php echo $CACHE['twitter.accounts.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="twitter.accounts.caching" value="1" /><label for="ta_yes">Yes</label>
   <br />
  <input id="ta_no" <?php echo !$CACHE['twitter.accounts.caching'] ? "checked=\"checked\"" : ""; ?> type="radio" name="twitter.accounts.caching" value="0" /><label for="ta_no">No</label>
 </div>
 <?php echo conjoon_cacheEnabledSnippet('Twitter account cache enabled', 'cache.twitter.accounts.caching'); ?>

 <!-- ERRORS -->
 <?php if (isset($CACHE['twitter.accounts.install_failed'])) { ?>
     <?php if ($CACHE['twitter.accounts.install_failed'] === true) { ?>
         <div class="error_box">
         <b>ERROR</b><br />
         <?php echo $FOLDER_CREATE_ERROR; ?>
         </div>
     <?php } ?>
 <?php } ?>
 <!-- ^^ EO ERRORS -->
  Path:
  <br />
  <input style="width:100%" id="ta_cache_path" type="text" name="twitter.accounts.backend.cache_dir" value="<?php echo $CACHE['twitter.accounts.backend.cache_dir']; ?>" />
  <?php echo conjoon_cacheDirSnippet('Twitter account cache directory', 'cache.twitter.accounts.backend.cache_dir'); ?>
 </p>
<!-- ^^ EO DB CACHE -->

</div>


<input type="hidden" name="cache_post" value="1" />
</p>
