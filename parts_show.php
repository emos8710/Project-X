<?php
if (count(get_included_files()) == 1)
    exit("Access restricted."); //restrict direct access

include 'scripts/db.php';

if (isset($_GET["ins_id"])) {
    $insertsql = "SELECT ins.name AS name, ins.ins_reg AS biobrick, ins_type.name AS type, "
            . "ins.date_db AS date, ins.comment AS comment, ins.private AS private, "
            . "users.first_name AS fname, users.last_name AS lname, "
            . "users.user_id AS user_id FROM ins, ins_type, users "
            . "WHERE ins.id = '$id' AND ins.type = ins_type.id AND ins.creator = users.user_id";
    $insertquery = mysqli_query($link, $insertsql);


    $mysqlerror = FALSE;
    $rowserror = FALSE;
    if (!$insertquery) {
        $mysqlerror = TRUE;
        $errormsg = mysqli_error($link);
    } else {
        $insertrows = mysqli_num_rows($insertquery);

        if ($insertrows < 1) {
            $rowserror = TRUE;
            $errormsg = '<h3 style=\"color:red\">Error: Database returned unexpected number of rows</h3>';
        }
    }

    if ($rowserror || $mysqlerror) {
        echo $errormsg;
    } else {
        $insertdata = mysqli_fetch_assoc($insertquery);
        ?>
        <h2>UpStrain Insert: <?php echo $insertdata['name']; ?></h2>

        <?php
        if ($loggedin && $active && $admin) {
            ?>
            <p>
                <a class="edit" href="<?php echo $_SERVER['REQUEST_URI'] ?>&edit">Edit insert</a>
            </p>
            <?php
        }

        if ($insertdata['private'] == 1 && !($loggedin && $active)) {
            ?>
            <h3>
                Access denied
            </h3>
            This insert is private (you need to be logged in).
            <br>
            <a href="javascript:history.go(-1)">Go back</a>
            <?php
        } else {

            mysqli_close($link) or die("Could not close database connection");
            ?>
            <div class="insert_table">
                <table class="insert">
                    <col><col>
                    <tr>
                        <th colspan="2"> Insert details</th>
                    </tr>
                    <tr>
                        <td><strong>Type:</strong></td>
                        <td><?php echo $insertdata["type"] ?></td>
                    </tr>
                    <tr>
                        <td><strong>iGEM registry entry:</strong></td>
                        <td><?php
                            if ($insertdata["biobrick"] === null || $insertdata["biobrick"] == '') {
                                echo "N/A";
                            } else {
                                echo "<a class=\"external\" href=\"http://parts.igem.org/Part:" . $insertdata["biobrick"] . "\" target=\"_blank\">" . $insertdata["biobrick"] . "</a>";
                            }
                            ?></td>
                    </tr>
                    <tr>
                        <td><strong>Added by:</strong></td>
                        <td><?php echo "<a href=\"user.php?user_id=" . $insertdata["user_id"] . "\">" . $insertdata["fname"] . " " . $insertdata["lname"] . "</a>"; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Date added:</strong></td>
                        <td><?php echo $insertdata["date"]; ?> </td>
                    </tr>
                    <tr>
                        <td><strong>Comment:</strong></td>
                        <td rowspan="2"><?php echo $insertdata["comment"]; ?></td>
                    </tr>
                    <tr>
                    </tr>
                </table>
            </div>
            <?php
        }
    }
} else if (isset($_GET["backbone_id"])) {
    $backbonesql = "SELECT backbone.name AS name, backbone.Bb_reg AS biobrick, "
            . "backbone.date_db AS date, backbone.comment AS comment, "
            . "backbone.private AS private, users.first_name AS fname, users.last_name AS lname, "
            . "users.user_id AS user_id FROM backbone, users WHERE backbone.id = '$id' "
            . "AND backbone.creator = users.user_id";
    $backbonequery = mysqli_query($link, $backbonesql);


    $mysqlerror = FALSE;
    $rowserror = FALSE;
    if (!$backbonequery) {
        $mysqlerror = TRUE;
        $errormsg = mysqli_error($link);
    } else {
        $backbonerows = mysqli_num_rows($backbonequery);

        if ($backbonerows < 1) {
            $rowserror = TRUE;
            $errormsg = '<h3 style=\"color:red\">Error: Database returned unexpected number of rows</h3>';
        }
    }

    if ($rowserror || $mysqlerror) {
        echo $errormsg;
    } else {
        $backbonedata = mysqli_fetch_assoc($backbonequery);
        ?>
        <h2>UpStrain Backbone: <?php echo $backbonedata['name']; ?></h2>

        <?php
        if ($loggedin && $active && $admin) {
            ?>
            <p>
                <a class="edit" href="<?php echo $_SERVER['REQUEST_URI'] ?>&edit">Edit backbone</a>
            </p>
            <?php
        }

        if ($backbonedata['private'] == 1 && !($loggedin && $active)) {
            ?>
            <h3>
                Access denied
            </h3>
            This backbone is private (you need to be logged in).
            <br>
            <a href="javascript:history.go(-1)">Go back</a>
            <?php
        } else {

            mysqli_close($link) or die("Could not close database connection");
            ?>
            <div class="backbone_table">
                <table class="backbone">
                    <col><col>
                    <tr>
                        <th colspan="2"> Backbone details</th>
                    </tr>
                    <tr>
                        <td><strong>iGEM registry entry:</strong></td>
                        <td><?php
            if ($backbonedata["biobrick"] === null || $backbonedata["biobrick"] == '') {
                echo "N/A";
            } else {
                echo "<a class=\"external\" href=\"http://parts.igem.org/Part:" . $backbonedata["biobrick"] . "\" target=\"_blank\">" . $backbonedata["biobrick"] . "</a>";
            }
            ?></td>
                    </tr>
                    <tr>
                        <td><strong>Added by:</strong></td>
                        <td><?php echo "<a href=\"user.php?user_id=" . $backbonedata["user_id"] . "\">" . $backbonedata["fname"] . " " . $backbonedata["lname"] . "</a>"; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Date added:</strong></td>
                        <td><?php echo $backbonedata["date"]; ?> </td>
                    </tr>
                    <tr>
                        <td><strong>Comment:</strong></td>
                        <td rowspan="2"><?php echo $backbonedata["comment"]; ?></td>
                    </tr>
                    <tr>
                    </tr>
                </table>
            </div>
            <?php
        }
    }
} else if (isset($_GET["strain_id"])) {
    $strainsql = "SELECT strain.name AS name, strain.date_db AS date, strain.comment AS comment, "
            . "strain.private AS private, users.first_name AS fname, users.last_name AS lname, "
            . "users.user_id AS user_id FROM strain, users "
            . "WHERE strain.id = '$id' AND strain.creator = users.user_id";
    $strainquery = mysqli_query($link, $strainsql);


    $mysqlerror = FALSE;
    $rowserror = FALSE;
    if (!$strainquery) {
        $mysqlerror = TRUE;
        $errormsg = mysqli_error($link);
    } else {
        $strainrows = mysqli_num_rows($strainquery);

        if ($strainrows < 1) {
            $rowserror = TRUE;
            $errormsg = '<h3 style=\"color:red\">Error: Database returned unexpected number of rows</h3>';
        }
    }

    if ($rowserror || $mysqlerror) {
        echo $errormsg;
    } else {
        $straindata = mysqli_fetch_assoc($strainquery);
        ?>
        <h2>UpStrain Strain: <?php echo $straindata['name']; ?></h2>

        <?php
        if ($loggedin && $active && $admin) {
            ?>
            <p>
                <a class="edit" href="<?php echo $_SERVER['REQUEST_URI'] ?>&edit">Edit strain</a>
            </p>
            <?php
        }

        if ($straindata['private'] == 1 && !($loggedin && $active)) {
            ?>
            <h3>
                Access denied
            </h3>
            This strain is private (you need to be logged in).
            <br>
            <a href="javascript:history.go(-1)">Go back</a>
            <?php
        } else {

            mysqli_close($link) or die("Could not close database connection");
            ?>
            <div class="strain_table">
                <table class="strain">
                    <col><col>
                    <tr>
                        <th colspan="2"> Strain details</th>
                    </tr>
                    <tr>
                        <td><strong>Added by:</strong></td>
                        <td><?php echo "<a href=\"user.php?user_id=" . $straindata["user_id"] . "\">" . $straindata["fname"] . " " . $straindata["lname"] . "</a>"; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Date added:</strong></td>
                        <td><?php echo $straindata["date"]; ?> </td>
                    </tr>
                    <tr>
                        <td><strong>Comment:</strong></td>
                        <td rowspan="2"><?php echo $straindata["comment"]; ?></td>
                    </tr>
                    <tr>
                    </tr>
                </table>
            </div>
            <?php
        }
    }
}
    