<?php
/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
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
 * Tests the path where the application folder was moved to.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

/**
 * check if user is authorized to load script
 */
include('./scripts/check_auth.php');

$DOCPATH = array();

$_SESSION['doc_path_failed'] = false;

if (!isset($_SESSION['doc_path']) && !isset($_SESSION['installation_info']['doc_path'])) {
    $docPath = "";
    $pos = strrpos($_SERVER['REQUEST_URI'], '/install/index.php?action=doc_path');
    if ($pos === false) {
        $pos = strrpos($_SERVER['REQUEST_URI'], '/install/?action=doc_path');
    }

    if ($pos !== false) {
        $docPath = substr($_SERVER['REQUEST_URI'], 0, $pos);
    }

    $_SESSION['doc_path'] = $docPath == "" ? "/" : $docPath;
} else if (!isset($_SESSION['doc_path']) && isset($_SESSION['installation_info']['doc_path'])) {
    $_SESSION['doc_path'] = $_SESSION['installation_info']['doc_path'];
}

if (isset($_POST['doc_path_post'])) {

    $docPath = trim((string)$_POST['doc_path']);

    $docPath = '/' . ltrim($docPath, '/');

    $_SESSION['doc_path'] = $docPath;

    header("Location: ./?action=doc_path_success");
    die();


}

include_once './view/doc_path.tpl';