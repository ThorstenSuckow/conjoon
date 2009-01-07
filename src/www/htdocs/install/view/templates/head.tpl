<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<!--
 conjoon
 (c) 2002-2009 siteartwork.de/conjoon.org
 licensing@conjoon.org

 $Author$
 $Id$
 $Date$
 $Revision$
 $LastChangedDate$
 $LastChangedBy$
 $URL$
-->

<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>conjoon install - <?php echo $VIEW['navigation'][$VIEW['action']][0]; ?></title>

<link rel="stylesheet" href="./css/style.css" type="text/css" media="screen" />

</head>
<body>
<div id="page">
<div id="content_wrap">
<div class="container-bg-fix"></div>
<div id="header">

    <h1>
        <a href="./">conjoon install</a>
    </h1>


    <div id="menu_primary">
<?php foreach ($VIEW['navigation'] as $key => $nav) { ?>
    <li class="page_item page-item-3 <?php echo ($VIEW['action'] == $key ? 'current_page_item': ''); ?>"><a href="#"><?php echo $nav[0]; ?></a></li>
<?php } ?>
    </div>
</div>

<div id="breadcrumb">
    <?php
        if ($VIEW['action'] == '') {
            echo $VIEW['navigation'][''][0];
        } else {
            echo "<a href=\"".$VIEW['navigation'][''][1]."\">" .
                 $VIEW['navigation'][''][0] .
                 "</a> &gt; " .
                 $VIEW['navigation'][$VIEW['action']][0];
        }
    ?>
</div>
