<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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