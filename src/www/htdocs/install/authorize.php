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
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

$authorized = false;

$AUTH_ERRORS = array(
    'key_match'     => false,
    'key_empty'     => false,
    'key_not_found' => false,
    'file_issue'    => false,
    'no_submit'     => false
);

if (isset($_POST['key'])) {
    $key = trim($_POST['key']);

    if ($key != "") {

        $res = @parse_ini_file('./SETUP_AUTHORIZATION_KEY.ini.php');

        if (is_array($res)) {

            if (isset($res['CONJOON_AUTHORIZATION_KEY'])) {

                $authKey = trim($res['CONJOON_AUTHORIZATION_KEY']);

                if ($authKey != "") {
                    if ($authKey === $key) {
                        $authorized = true;
                    } else {
                        $AUTH_ERRORS['key_match'] = true;
                    }
                } else {
                    $AUTH_ERRORS['key_empty'] = true;
                }

            } else {
                $AUTH_ERRORS['key_not_found'] = true;
            }
        } else {
            $AUTH_ERRORS['file_issue'] = true;
        }

    } else {
        $AUTH_ERRORS['no_submit'] = true;
    }

}

if ($authorized === true) {
    $_SESSION['com.conjoon.session.install.authorized'] = true;
    header("Location: ./index.php?action=authorize_success");
    die();
}

include_once './view/authorize.tpl';