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
 * Patch notes view
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
?>
<div id="0_1_4RC5_container">
 <table>
  <tbody>
   <tr>
    <td colspan="2">
     The following lists the character set previously used by PHP when
     communicating with MySQL.
    </td>
   </tr>
   <tr>
    <td style="width:150px;vertical-align:top">
      Info:
    </td>
    <td>
    <table>
    <?php

        $dbAdapter  = $_SESSION['db_adapter'];
        $prefix     = $_SESSION['db_table_prefix'];
        $dbHost     = $_SESSION['db_host'];
        $db         = $_SESSION['db'];
        $dbPort     = $_SESSION['db_port'];
        $dbUser     = $_SESSION['db_user'];
        $dbPassword = $_SESSION['db_password'];
        $dbType     = strtolower(str_replace("pdo_", "", $dbAdapter));

        switch ($dbType) {
            case 'mysql':
                $db = new PDO(
                $dbType . ":" .
                    "host=" . $dbHost . ";".
                    "dbname=".$db.";".
                    "port=".$dbPort,
                $dbUser, $dbPassword
            );
            break;

            default:
                die("No support for adapter \"$dbType\"");
                break;
        }

        $sql = "SHOW VARIABLES LIKE 'character_set%'";
        $res = $db->query($sql);

        $variables = array();

        foreach ($res as $row) {
            if (!in_array($row['Variable_name'], array(
                'character_set_client', 'character_set_results',
                'character_set_connection'
            ))) {
                continue;
            }

            $variables[] = $row['Value'];

        ?>
            <tr>
                <td><?php echo $row['Variable_name']; ?>:</td>
                <td><?php echo $row['Value']; ?></td>
                <td></td>
            </tr>
        <?php
        }
    ?>
    </table>
    </td>
   </tr>
  <tbody>
 </table>

    <?php
    $variables = array_unique($variables);

    if (count($variables) != 1) {
        die("A critical error occurred. Could not proceed. "
            . "Expected exactly one charset, but got: "
            . implode(", ", $variables)
        );
    }


    ?>
    <input type="hidden" name="patchdata[0_1_4RC5][in_charset]"
           value="<?php echo $variables[0]; ?>" />
</div>