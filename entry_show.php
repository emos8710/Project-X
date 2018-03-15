<?php
if (count(get_included_files()) == 1)
    exit("Access restricted."); //restrict direct access

include 'scripts/db.php';

$entrysql = "SELECT entry.comment AS cmt, entry.year_created AS year, entry_upstrain.upstrain_id AS uid, "
        . "entry.date_db AS date, entry.entry_reg AS biobrick, entry.private AS private, entry.created AS created, "
        . "users.first_name AS fname, users.last_name AS lname, users.user_id AS user_id FROM entry, entry_upstrain, "
        . "users, strain WHERE entry_upstrain.upstrain_id = '$id' AND entry_upstrain.entry_id = "
        . "entry.id AND entry.creator = users.user_id AND entry.strain = strain.id";
$entryquery = mysqli_query($link, $entrysql);
if (!$entryquery)
    $mysql_error = "Entry: " . mysqli_error($link);

$backbonesql = "SELECT backbone.id AS bid, backbone.name AS name, backbone.Bb_reg AS biobrick, "
        . "backbone.date_db AS date, backbone.private AS private, users.first_name AS fname, users.last_name AS lname, "
        . "users.user_id AS user_id FROM backbone, entry, entry_upstrain, users WHERE "
        . "entry_upstrain.upstrain_id = '$id' AND entry_upstrain.entry_id = entry.id AND "
        . "entry.backbone = backbone.id AND backbone.creator = users.user_id";
$backbonequery = mysqli_query($link, $backbonesql);
if (!$backbonequery)
    $mysql_error = "Backbone: " . mysqli_error($link);

$insertsql = "SELECT ins.id, ins.name AS name, ins.ins_reg AS biobrick, ins_type.name AS type, ins.date_db AS date, entry_inserts.position AS pos, "
        . "users.first_name AS fname, users.last_name AS lname, users.user_id AS user_id "
        . "FROM entry_inserts "
        . "LEFT JOIN ins ON entry_inserts.insert_id = ins.id "
        . "LEFT JOIN ins_type ON ins.type = ins_type.name "
        . "LEFT JOIN entry ON entry_inserts.entry_id = entry.id "
        . "LEFT JOIN users ON ins.creator = users.user_id "
        . "LEFT JOIN entry_upstrain ON entry_upstrain.entry_id = entry.id "
        . "WHERE entry_upstrain.upstrain_id = '$id' "
        . "ORDER BY entry_inserts.position ASC";
$insertquery = mysqli_query($link, $insertsql);
if (!$insertquery)
    $mysql_error = "Insert: " . mysqli_error($link);

$strainsql = "SELECT strain.name AS name, strain.id AS sid, strain.comment AS cmt, strain.date_db AS date, "
        . "strain.private AS private, users.user_id AS uid, users.first_name AS fname, users.last_name AS lname "
        . "FROM strain "
        . "LEFT JOIN entry ON entry.strain = strain.id "
        . "LEFT JOIN entry_upstrain ON entry_upstrain.entry_id = entry.id "
        . "LEFT JOIN users ON strain.creator = users.user_id "
        . "WHERE entry_upstrain.upstrain_id = '$id'";
$strainquery = mysqli_query($link, $strainsql);
if (!$strainquery)
    $mysql_error = "Strain: " . mysqli_error($link);

$filesql = "SELECT name_new AS filename FROM upstrain_file WHERE upstrain_file.upstrain_id = '$id'";
$filequery = mysqli_query($link, $filesql);
if (!$filequery)
    $mysql_error = "File: " . mysqli_error($link);

$error = (!$entryquery || !$backbonequery || !$insertquery || !$strainquery || !$filequery);
if ($error) {
    $errormsg = $mysql_error;
} else {
    $filerows = mysqli_num_rows($filequery);
    $error = ($filerows > 1);
    if ($error) {
        $errormsg = '<h3 style=\"color:red\">Error: Database returned unexpected number of rows</h3>';
    } else {
        $hasfile = ($filerows == 1);

        $entryrows = mysqli_num_rows($entryquery);
        $backbonerows = mysqli_num_rows($backbonequery);
        $insertrows = mysqli_num_rows($insertquery);
        $strainrows = mysqli_num_rows($strainquery);
        $error = ($entryrows != 1 || $backbonerows != 1 || $strainrows != 1);

        if ($error) {
            $errormsg = '<h3 style=\"color:red\">Error: Database returned unexpected number of rows</h3>';
        }
    }
}

if ($error) {
    echo $errormsg;
} else {
    $entrydata = mysqli_fetch_assoc($entryquery);
    $backbonedata = mysqli_fetch_assoc($backbonequery);
    $straindata = mysqli_fetch_assoc($strainquery);
    ?>
    <h2 class="search_etc">UpStrain Entry <?php echo $entrydata['uid']; ?></h2>

    <?php
    if ($loggedin && $active && $admin) {
        ?>
        <p>
            <a class="edit" href="<?php echo $_SERVER['REQUEST_URI'] ?>&edit">Edit entry</a>
        </p>
        <?php
    }

    if ($entrydata['private'] == 1 && !($loggedin && $active)) {
        ?>
        <h3>
            Access denied
        </h3>
        This entry is private (you need to be logged in).
        <br>
        <a href="javascript:history.go(-1)">Go back</a>
        <?php
    } else {

        $inserts = [];
        while ($row = mysqli_fetch_assoc($insertquery)) {
            array_push($inserts, $row);
        }

        if ($hasfile) {
            $filedata = mysqli_fetch_assoc($filequery);
        }

        mysqli_close($link) or die("Could not close database connection");
        ?>
        <div class="entry_table">
            <table class="entry">
                <col><col>
                <thead>
                    <tr>    
                        <th style="font-style: normal; font-weight: 300px;">Entry details</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>iGEM registry entry:</strong></td>
                        <td>
                            <?php
                            if ($entrydata["biobrick"] === null || $entrydata["biobrick"] == '') {
                                echo "N/A";
                            } else {
                                ?>
                                <a class="external" href="http://parts.igem.org/Part:<?php echo $entrydata["biobrick"]; ?>" target="_blank"><?php echo $entrydata["biobrick"]; ?></a>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Year created:</strong></td>
                        <td><?php echo $entrydata["year"] ?></td>
                    </tr>
                    <tr>
                        <td><strong>Added by:</strong></td>
                        <td><?php echo "<a href=\"user.php?user_id=" . $entrydata["user_id"] . "\">" . $entrydata["fname"] . " " . $entrydata["lname"] . "</a>"; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Date added:</strong></td>
                        <td><?php echo $entrydata["date"]; ?> </td>
                    </tr>
                    <tr>
                        <td><strong>Comment:</strong></td>
                        <td><?php echo $entrydata["cmt"]; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Private?</strong></td>
                        <td>
                            <?php
                            if ($entrydata['private'] == 1): echo "Yes";
                            else: echo "No";
                            endif;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Created physically?</strong></td>
                        <td>
                            <?php
                            if ($entrydata['created'] == 1): echo "Yes";
                            else: echo "No";
                            endif;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            if ($hasfile) {
                                ?>
                                <strong>Sequence file (FASTA):</strong>
                                <?php
                            }
                            ?></td>
                        <td>
                            <?php
                            if ($hasfile && file_exists("files/" . $id . ".fasta")) {
                                ?>
                                <a class="download" href="files/<?php echo $filedata["filename"]; ?>"  download><?php echo $filedata["filename"] ?></a>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
		<div class="clear"></div>
		<br>
        <div class="backbone_strain_table">
            <table class="strain" style="margin-right: 80px; margin-top: 40px;">
                <col><col>
                <thead>
                    <tr>
                        <th colspan="2" style="font-style: normal; font-weight: 300px;"><a href="parts.php?strain_id=<?php echo $straindata['sid']; ?>">Strain</a></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td><?php echo $straindata['name']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Added by:</strong></td>
                        <td><a href="user.php?user_id=<?php echo $straindata['uid']; ?>"><?php echo $straindata['fname'] . " " . $straindata['lname']; ?></a></td>
                    </tr>
                    <tr>
                        <td><strong>Date added:</strong></td>
                        <td><?php echo $straindata['date']; ?></td>
                    </tr>
                </tbody>
            </table>
            <table class="backbone">
                <col><col>
                <thead>
                    <tr>
                        <th colspan="2" style="font-style: normal; font-weight: 300px;"><a href="parts.php?backbone_id=<?php echo $backbonedata['bid']; ?>">Backbone</a></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td><?php echo $backbonedata['name']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>iGEM registry entry:</strong></td>
                        <td>
                            <?php
                            if ($backbonedata['biobrick'] === null || $backbonedata['biobrick'] === '') {
                                ?>
                                <a class="external" href="http://parts.igem.org/Part:<?php echo $backbonedata['biobrick']; ?>" target="_blank"><?php echo $backbonedata['biobrick']; ?></a>
                                <?php
                            } else {
                                echo "N/A";
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Added by:</strong></td>
                        <td><a href = "user.php?user_id=<?php echo $backbonedata['user_id']; ?>"><?php echo $backbonedata['fname'] . " " . $backbonedata['lname']; ?></a></td>
                    </tr>
                    <tr>
                        <td><strong>Date added:</strong></td>
                        <td><?php echo $backbonedata['date']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="insert_table">            
            <?php
            for ($i = 0; $i < $insertrows; $i++) {
                ?>
                <table class="insert">
                    <col><col>
                    <thead>
                        <tr>
                            <th colspan="2" style="font-style: normal;font-weight: 300px;"><a href="parts.php?ins_id=<?php echo $inserts[$i]['id']; ?>"> Insert <?php echo $i + 1; ?></a></th>
                        </tr>
                    </thead>
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td><?php echo $inserts[$i]["name"]; ?></td>
                    </tr>
                    <tr>
                    <tr>
                        <td><strong>iGEM registry entry:</strong></td>
                        <td><?php
                            if ($inserts[$i]["biobrick"] === null || $inserts[$i]["biobrick"] === '') {
                                echo "N/A";
                            } else {
                                echo "<a class=\"external\" href=\"http://parts.igem.org/Part:" . $inserts[$i]["biobrick"] . "\" target=\"_blank\">" . $inserts[$i]["biobrick"] . "</a>";
                            }
                            ?></td>
                    </tr>
                    <tr>
                        <td><strong>Added by:</strong></td>
                        <td><?php echo "<a href=\"user.php?user_id=" . $inserts[$i]["user_id"] . "\">" . $inserts[$i]["fname"] . " " . $inserts[$i]["lname"] . "</a>"; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Date added:</strong></td>
                        <td><?php echo $inserts[$i]["date"]; ?> </td>
                    </tr>
                    <tr>
                        <td><strong>Type:</strong></td>
                        <td><?php echo $inserts[$i]["type"]; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
        <?php
    }
}    