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