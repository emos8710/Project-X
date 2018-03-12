<?php
if (count(get_included_files()) == 1)
    exit("Access restricted.");

if ($loggedin && $active && $admin) {

    // Set display for the content div
    if (isset($_GET['content'])) {
        $current_content = test_input($_GET['content']);
    } else {
        $current_content = "";
    }
    include 'scripts/db.php';

    // Find the database entry id
    $id_sql = "SELECT entry_id FROM entry_upstrain WHERE upstrain_id = '$id'";
    $entry_id = mysqli_query($link, $id_sql);
    $entry_id = mysqli_fetch_array($entry_id)[0];

    mysqli_close($link);

    // Update procedures
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        include 'scripts/db.php';

        $iserror = FALSE;
        // Change registry ID
        if (isset($_POST['biobrick']) && $_POST['biobrick'] != "") {
            $to_update = "entry_reg";
            $user_input = test_input($_POST['biobrick']);
            $update_val = mysqli_real_escape_string($link, $user_input);
            $update_msg = "registry ID";
            // Change strain
        } else if (isset($_POST['strain']) && $_POST['strain'] != "") {
            $to_update = "strain";
            $user_input = test_input($_POST['strain']);
            $update_name = mysqli_real_escape_string($link, $user_input);
            if ($update_id = mysqli_query($link, "SELECT id FROM strain WHERE name = '$update_name'")) {
                $update_val = mysqli_fetch_array($update_id)[0];
            } else {
                $iserror = TRUE;
                $update_msg = "Strain does not exist in database.";
            }
            $update_msg = "strain";
            // Change backbone
        } else if (isset($_POST['backbone']) && $_POST['backbone'] != "") {
            $to_update = "backbone";
            $user_input = test_input($_POST['backbone']);
            $update_name = mysqli_real_escape_string($link, $user_input);
            if ($update_id = mysqli_query($link, "SELECT id FROM backbone WHERE name = '$update_name'")) {
                $update_val = mysqli_fetch_array($update_id)[0];
            } else {
                $iserror = TRUE;
                $update_msg = "Backbone does not exist in database.";
            }
            $update_msg = "backbone";
            // Change insert
        } else if (isset($_POST['insert']) && $_POST['insert'] != "" && isset($_POST['position']) && !empty($_POST['position'])) {
            $insert_id = mysqli_real_escape_string($link, test_input($_POST['insert']));
            $insert_pos = mysqli_real_escape_string($link, test_input($_POST['position']));
            $insert_sql = "UPDATE entry_inserts SET insert_id = '$insert_id' WHERE position = '$insert_pos' AND entry_id = '$entry_id'";
            if ($result = mysqli_query($link, $insert_sql)) {
                $update_msg = "Successfully updated insert.";
            } else {
                $iserror = TRUE;
                $update_msg = "Failed to update insert. " . mysqli_error($link);
                goto errorTime;
            }
            // Remove insert
        } else if (isset($_POST['remove_insert']) && isset($_POST['position']) && !empty($_POST['position'])) {
            $insert_pos = mysqli_real_escape_string($link, test_input($_POST['position']));
            $remove_sql = "DELETE FROM entry_inserts WHERE position = '$insert_pos' AND entry_id = '$entry_id';";
            $move_sql = "UPDATE entry_inserts SET position = position-1 WHERE position > '$insert_pos' AND entry_id = '$entry_id';";

            mysqli_begin_transaction($link);
            mysqli_query($link, $remove_sql);
            mysqli_query($link, $move_sql);
            $commit = mysqli_commit($link);

            if ($commit) {
                $update_msg = "Successfully removed insert.";
            } else {
                $iserror = TRUE;
                $update_msg = "Failed to remove insert. " . mysqli_error($link);
                goto errorTime;
            }
            // Add new insert
        } else if (isset($_POST['new_insert']) && !empty($_POST['new_insert']) && isset($_POST['position']) && !empty($_POST['position'])) {
            $insert_pos = mysqli_real_escape_string($link, test_input($_POST['position']));
            $new_insert = mysqli_real_escape_string($link, test_input($_POST['new_insert']));
            $add_sql = "INSERT INTO entry_inserts (insert_id, entry_id, position) VALUES (?,?,?)";
            if ($stmt = mysqli_prepare($link, $add_sql)) {
                mysqli_stmt_bind_param($stmt, "iii", $new_insert, $entry_id, $insert_pos);
                if (mysqli_stmt_execute($stmt)) {
                    $update_msg = "Successfully added insert.";
                } else {
                    $iserror = TRUE;
                    $update_msg = "Couldn't add insert, failed to execute statement. " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                $iserror = TRUE;
                $update_msg = "Couldn't update add insert, failed to prepare statement. " . mysqli_stmt_error($stmt);
            }
            //Edit file
        } else if (isset($_POST['my_file'])) {
            if (is_uploaded_file($_FILES['my_file']['tmp_name']) && $_FILES['my_file']['error'] == 0) {
                include 'scripts/db.php';

                $old = mysqli_query($link, "SELECT name_new AS file FROM upstrain_file WHERE upstrain_id = (SELECT upstrain_id FROM entry_upstrain "
                        . "WHERE entry_id = '$entry_id');");
                
                mysqli_close($link) or die("Could not close database connection");

                $old_file = mysqli_fetch_array($old)[0];
                $new_file = $_FILES['my_file']['name'];

                $path = "files/" . $id . ".fasta";
                $lines = file($_FILES['my_file']['tmp_name']);
                $n_lines = count($lines);
                
                if ($n_lines < 2) {
                    $iserror = TRUE;
                    $update_msg = "Uploaded file has no sequence data!";
                    goto errorTime;
                } else {
                    $header = trim($lines[0]);
                    $first_char = $header[0];
                    for ($i = 1; $i < $n_lines; $i++) {
                        $seq .= trim($lines[$i]);
                    }
                    if ($first_char === '>' && preg_match("/^[ATCGatcg*\-\s]+$/", $seq)) {
                        if (file_exists($path)) {
                            if (!unlink($path)) {
                                $iserror = TRUE;
                                $update_msg = "Failed to remove old sequence file.";
                                goto errorTime;
                            }
                        }
                        if (move_uploaded_file($_FILES['my_file']['tmp_name'], $path)) {
                            $edit_sql = "UPDATE upstrain_file SET name_original = '$new_file' WHERE upstrain_id = '$id'";
                            if (mysqli_query($link, $edit_sql)) {
                                $update_msg = "Sequence file successfully updated!";
                            } else {
                                $iserror = TRUE;
                                $update_msg = "Couldn't update database file info. " . mysqli_error($link);
                                goto errorTime;
                            }
                        } else {
                            $iserror = TRUE;
                            $update_msg = "Failed to upload new file!";
                            goto errorTime;
                        }
                    } else {
                        $iserror = TRUE;
                        $update_msg = "The specified file has an invalid format (FASTA files only!)";
                        goto errorTime;
                    }
                }
            } else {
                $iserror = TRUE;
                $update_msg = "There was no file to upload";
                goto errorTime;
            }
            // Change comment
        } else if (isset($_POST['comment']) && !empty($_POST['comment'])) {
            $to_update = "comment";
            $user_input = test_input($_POST['comment']);
            $update_val = mysqli_real_escape_string($link, $user_input);
            $update_msg = "comment";
        } else if (isset($_POST['year_created']) && !empty($_POST['year_created'])) {
            $to_update = "year_created";
            $user_input = test_input($_POST['year_created']);
            $update_val = mysqli_real_escape_string($link, $user_input);
            $update_msg = "year created";
            // Change created
        } else if (isset($_POST['created']) && !empty($_POST['created'])) {
            $to_update = "created";
            $user_input = test_input($_POST['created']);
            $update_val = mysqli_real_escape_string($link, $user_input);
            $update_msg = "creation status";
            // Change private
        } else if (isset($_POST['private']) && !empty($_POST['private'])) {
            $to_update = "private";
            $user_input = test_input($_POST['private']);
            $update_val = mysqli_real_escape_string($link, $user_input);
            $update_msg = "privacy setting";
            // Change file
        }


        // Execute changes
        if (isset($update_val) && $update_val != "" && !$iserror) {
            // Check if user input invalid characters
            if ($update_val != $user_input && $update_name != $user_input) {
                $iserror = TRUE;
                $update_msg = "Input " . $to_update . " contains invalid characters.";
                goto errorTime;
            }
            // Prepare and execute statement
            $update_sql = "UPDATE entry SET " . $to_update . " = ? WHERE id = " . $entry_id;
            if ($stmt = mysqli_prepare($link, $update_sql)) {
                mysqli_stmt_bind_param($stmt, "s", $update_val);
                if (mysqli_stmt_execute($stmt)) {
                    $update_msg = "Successfully updated " . $update_msg . ".";
                } else {
                    $iserror = TRUE;
                    $update_msg = "Couldn't update " . $update_msg . ", failed to execute statement. " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                $iserror = TRUE;
                $update_msg = "Couldn't update " . $update_msg . ", failed to prepare statement. " . mysqli_stmt_error($stmt);
            }
        }

        errorTime: // Go here when there is an error
        // Style the success or error message. 
        if ($iserror) {
            $update_msg = "<strong style=\"color:red\">Error: " . $update_msg . "</strong>";
        } else if (isset($update_msg)) {
            $update_msg = "<strong style=\"color:green\">" . $update_msg . "</strong>";
        }

        mysqli_close($link);
    }

    include 'scripts/db.php';

    // Fetch entry information (comment, strain, backbone, private, created)
    $entry_sql = "SELECT entry.comment, entry.entry_reg AS biobrick, backbone.name AS bname, strain.name AS sname, entry.year_created, entry.private, entry.created FROM entry, backbone, strain "
            . "WHERE entry.id = '$entry_id' AND backbone.id = entry.backbone AND strain.id = entry.strain";
    $entry_result = mysqli_query($link, $entry_sql);
    $entry_info = mysqli_fetch_assoc($entry_result);

    // Fetch insert information (name and type)
    $insert_sql = "SELECT ins.name AS name, ins_type.name AS type, entry_inserts.position FROM ins, ins_type, entry_inserts "
            . "WHERE entry_inserts.entry_id = '$entry_id' AND entry_inserts.insert_id = ins.id AND ins.type = ins_type.id "
            . "ORDER BY entry_inserts.position";
    $insert_result = mysqli_query($link, $insert_sql);

    // Fetch sequence file
    $file_sql = "SELECT name_new AS file, upstrain_id AS uid FROM upstrain_file WHERE upstrain_id = (SELECT upstrain_id FROM entry_upstrain "
            . "WHERE entry_id = '$entry_id')";
    $file_result = mysqli_query($link, $file_sql);
    $file_info = mysqli_fetch_assoc($file_result);

    mysqli_close($link);
    ?>

    <!-- Edit forms -->
    <h2 class="search_etc">UpStrain ID <?php echo $id; ?></h2>
    <div class="edit_entry">
        <table class="edit_entry">
            <!-- Edit registry ID -->
            <tr class="edit_entry">
                <th class="title"> Registry ID: </th>
                <td class="info"> 
                    <?php echo $entry_info["biobrick"]; ?> 
                </td>
                <td class="edit"> 
                    <?php
                    if ($current_content != "biobrick") {
                        ?>
                        <a href="?upstrain_id=<?php echo $id; ?>&edit&content=biobrick">Edit</a>
                        <?php
                    }
                    if ($current_content == "biobrick") {
                        ?>
                        <table class="mini-table">
                            <tr class="mini-table">
                            <form class="edit_entry" action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                                <td class="mini-table" style="padding: 0px;">
                                    <label class="edit_entry" style="font-size: 14px; font-style: normal; padding: 0px;">New registry ID</label>
                                </td>
                                <td class="mini-table">
                                    <input class="edit_entry" type="text" name="biobrick" required style="border: 1px solid #001F3F; border-radius: 5px"> 
                                </td>
                                <td class="mini-table">
                                    <input class="edit_entry_button" type="submit" value="Submit" style="height: 20px; padding:2px; margin-top: 2px;">
                                </td>
                                <td class="">
                                    <a style="float:right; margin-left: 2px;" href="?upstrain_id=<?php echo $id; ?>&edit">Cancel</a>
                                </td>
                            </form>
                </tr>
            </table>
        <?php } ?>
    </td>
    </tr>

    <!-- Edit strain -->
    <tr class="edit_entry">
        <th class="title"> Strain: </th> 
        <td class="info"> 
            <?php echo $entry_info["sname"]; ?>
        </td>
        <td class="edit">
            <?php
            if ($current_content != "strain") {
                ?>
                <a href="?upstrain_id=<?php echo $id; ?>&edit&content=strain">Edit</a>
                <?php
            }
            if ($current_content == "strain") {
                ?>
                <table class="mini-table">
                    <tr class="mini-table">
                    <form class="edit_entry" action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                        <td class="mini-table">
                            <label class="edit_entry" style="font-size: 14px; font-style: normal; padding: 0px;">New strain</label>
                        </td>
                        <td class="mini-table">
                            <select class="edit_entry" name="strain" required style="border: 1px solid #001F3F; border-radius: 5px">
                                <?php
                                include 'scripts/db.php';
                                $sql_strain = mysqli_query($link, "SELECT name FROM strain");
                                while ($row = $sql_strain->fetch_assoc()) {
                                    echo "<option>" . $row['name'] . "</option>";
                                }
                                mysqli_close($link);
                                ?>
                            </select>
                        </td>
                        <td class="mini-table">
                            <input class="edit_entry_button" type="submit" value="Submit" style="height: 20px; padding:2px; margin-top: 2px;">
                        </td>
                        <td class="mini-table">
                            <a style="float:right; margin-left: 2px;" href="?upstrain_id=<?php echo $id; ?>&edit">Cancel</a>
                        </td>
                    </form>
        </tr>
        </table>
    <?php } ?>
    </td>
    </tr>

    <!-- Edit backbone -->
    <tr class="edit_entry">
        <th class="title"> Backbone: </th> 
        <td class="info" style="padding:0px;"> 
            <?php echo $entry_info["bname"]; ?>
        </td>
        <td class="edit">
            <?php
            if ($current_content != "backbone") {
                ?>
                <div class="edit_info"><a href="?upstrain_id=<?php echo $id; ?>&edit&content=backbone">Edit</a></div>
                <?php
            }
            if ($current_content == "backbone") {
                ?>
                <table class="mini-table" style="margin-top: 0px;">
                    <tr class="mini-table">
                    <form class="edit_entry" action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                        <td class="mini-table"> 
                            <label class="edit_entry" style="font-size: 14px; font-style: normal; padding: 0px;"> New backbone </label>
                        </td>
                        <td class="mini-table"> 
                            <select class="edit_entry" style="border: 1px solid #001F3F; border-radius: 5px"> name="backbone" required>
                                <?php
                                include 'scripts/db.php';
                                $sql_strain = mysqli_query($link, "SELECT name FROM backbone");
                                while ($row = $sql_strain->fetch_assoc()) {
                                    echo "<option>" . $row['name'] . "</option>";
                                }
                                mysqli_close($link);
                                ?>
                            </select>
                        </td>
                        <td class="mini-table"> 
                            <input class="edit_entry_button" type="submit" value="Submit" style="height: 20px; padding:2px; margin-top: 2px;">
                        </td>
                        <td class="mini-table"> 
                            <a style="float:right; margin-left: 2px;" href="?upstrain_id=<?php echo $id; ?>&edit">Cancel</a>
                        </td>
                    </form>
        </tr>
        </table>
    <?php } ?>
    </td>
    </tr>

    <!-- Edit inserts -->
    <tr class="edit_entry"> 
        <th class="title"> Inserts: </th>
        <td class="info"> 
            <div class="mini-table">
                <?php while ($insert_row = mysqli_fetch_assoc($insert_result)) { ?>
                    <table class="mini-table">
                        <tr class="mini-table">
                            <th class="mini-table" style="text-align: left; width:50px; border: none; border-bottom: none;"> 
                                Insert: 
                            </th> 
                            <td class="mini-table" style="width: 200px">
                                <?php echo $insert_row['name'] ?>
                            </td>
                            <th class="mini-table" style="text-align: left; width: 50px; border: none; border-bottom: none;"> 
                                Type:
                            </th> 
                            <td class="mini-table" style="width: 200px">
                                <?php echo $insert_row['type']; ?>
                            </td>
                            <?php
                            if ($current_content != "insert" . $insert_row['position']) {
                                ?>
                                <td class="mini-table" style="verticle-align: center;">
                                    <a href="?upstrain_id=<?php echo $id; ?>&edit&content=insert<?php echo $insert_row['position'] ?>">Edit</a>
                                </td>
                                <td class="mini-table">
                                    <form action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                                        <input name="remove_insert" type="hidden">
                                        <input name="position" type="hidden" value="<?php echo $insert_row['position'] ?>">
                                        <input class="edit_entry_button" type="submit" value="Remove" style="height: 20px; padding: 2px; verticle-align: center; margin-top: 3px;">
                                    </form>
                                </td>
                            <?php } ?>
                        </tr>
                    </table>
                    <?php if ($current_content == "insert" . $insert_row['position']) { ?>
                        <div class="field-wrap">
                            <form action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                                <label>Insert type</label>
                                <select name="insert_type" id="Ins_type" required>
                                    <option value="">Select insert type</option>
                                    <?php
                                    include 'scripts/db.php';
                                    $sql_ins_type = mysqli_query($link, "SELECT * FROM ins_type");
                                    while ($row = $sql_ins_type->fetch_assoc()) {
                                        echo '<option value="' . $row['id'] . '">' . $row['name'] . "</option>";
                                    }
                                    ?>
                                </select>

                                <label class="edit_entry">Insert name</label>
                                <select name="insert" id ="Ins" required>
                                    <option value="">Select insert name</option>
                                </select>

                                <!-- Send insert position -->
                                <input name="position" type="hidden" value="<?php echo $insert_row['position'] ?>">
                                <input class="edit_entry_button" type="submit" value="Submit" >
                                <div class="clear"></div>
                                <a style="float:right; margin-left: 2px;" href="?upstrain_id=<?php echo $id; ?>&edit">Cancel</a>
                            </form>
                        </div>
                    <?php } ?>

                <?php } ?>
            </div>
        </td>
        <td class="edit">
            <a href="?upstrain_id=<?php echo $id; ?>&edit&content=add_insert">Add insert</a>
            <!-- Add new insert -->
            <?php if ($current_content == "add_insert") { ?>
                <table class="mini-table" style="margin-top: 0px;">
                    <tr class="mini-table" style="margin-top: 0px;">
                    <form class="edit_entry" action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                        <td class="mini-table" style="margin-top: 0px;">
                            <div class="field-wrap" style="float: left; margin-right: 5px; margin-top: 0px;">
                                <label class="edit_entry" style="font-size: 14px; font-style: normal; padding: 0px;"> Insert type </label>
                                <br><select class="edit_entry" name="insert_type" id="Ins_type" required style="border: 1px solid #001F3F; border-radius: 5px">
                                    <option value="">Select insert type</option>
                                    <?php
                                    include 'scripts/db.php';
                                    $sql_ins_type = mysqli_query($link, "SELECT * FROM ins_type");
                                    while ($row = $sql_ins_type->fetch_assoc()) {
                                        echo '<option value="' . $row['id'] . '">' . $row['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </td>

                        <td class="mini-table">
                            <div class="field-wrap">
                                <label class="edit_entry" style="font-size: 14px; font-style: normal; padding: 0px;">Insert name</label>
                                <br><select class="edit_entry" name="new_insert" id ="Ins" required style="border: 1px solid #001F3F; border-radius: 5px">
                                    <option value="">Select insert name</option>
                                </select>
                            </div>
                        </td>

                        <?php
                        include 'scripts/db.php';
                        $pos_query = "SELECT MAX(position) FROM entry_inserts WHERE entry_id = '$entry_id'";
                        $pos_result = mysqli_query($link, $pos_query);
                        $position = mysqli_fetch_array($pos_result)[0] + 1;
                        ?>

                        <td class="mini-table" style="margin-top: 0px;">
                            <input name="position" type="hidden" value="<?php echo $position ?>" style="border: 1px solid #001F3F; border-radius: 5px">
                        </td>
                        <td class="mini-table" style="margin-top: 0px;">
                            <br>
                            <input class="edit_entry_button" type="submit" value="Submit" style="height: 20px; padding: 2px; verticle-align: center; margin-top: 3px;">
                        </td>
                        <td class="mini-table" style="margin-top: 0px;">
                            <br>
                            <a style="float:right; margin-left: 2px;" href="?upstrain_id=<?php echo $id; ?>&edit">Cancel</a>
                        </td>
                    </form>
        </tr>
        </table>
    <?php } ?>
    </td>
    </tr>
    <!-- Edit comment -->
    <tr class="edit_entry">
        <th class="title">Comment:</th> 
        <td class="info"> 
            <?php
            echo $entry_info["comment"];
            ?>
        </td>
        <td>
            <?php
            if ($current_content != "comment") {
                ?>
                <a href="?upstrain_id=<?php echo $id; ?>&edit&content=comment">Edit</a>
                <?php
            } else {
                include 'scripts/db.php';
                $sql_comment = mysqli_query($link, "SELECT comment FROM entry WHERE id = '$entry_id'");
                $old_comment = mysqli_fetch_array($sql_comment)[0];
                ?>
                <table class="mini-table">
                    <tr class="mini-table">
                    <form class="edit_entry" action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                        <td class="mini-table">
                            <label class="edit_entry" style="font-size: 14px; font-style: normal; padding: 0px;"> 
                                Edit comment 
                            </label>
                        </td>
                        <td class="mini-table">
                            <input class="edit_entry" type="text" name="comment" required value="<?php echo $old_comment ?> " style="border: 1px solid #001F3F; border-radius: 5px"> 
                        </td>
                        <td class="mini-table">
                            <input class="edit_entry_button" type="submit" value="Submit" style="height: 20px; padding: 2px; verticle-align: center; margin-top: 3px;">
                        </td>
                        <td class="mini-table">
                            <a style="float:right; margin-left: 2px;" href="?upstrain_id=<?php echo $id; ?>&edit">Cancel</a>
                        </td>
                    </form>
        </tr>
        </table>
        <?php
    }
    ?>
    </td>
    </tr>

    <!-- Edit file -->
    <tr class="edit_entry">
        <th class="title"> Sequence file: </th>
        <?php
        if (mysqli_num_rows($file_result) < 1 && !file_exists("files/" . $id . ".fasta")) {
            ?>
            <td class="info">
                No file uploaded
            </td>
            <?php
        } else {
            ?>
            <td class="info"><a href="files/<?php echo $file_info['file']; ?>" download><?php echo $file_info['file']; ?></a></td>
            <?php
        }
        ?>
        <td>
            <?php
            if ($current_content != "file") {
                ?>
                <a href="<?= $_SERVER['PHP_SELF'] ?>?upstrain_id=<?= $id ?>&edit&content=file">Edit</a>
                <?php
            } else {
                ?>
                <table class="mini-table">
                    <tr class="mini-table">
                    <form class="edit-entry" action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST" enctype="multipart/form-data">
                        <td class="mini-table">
                            <label class="edit_entry" style="font-size: 14px; font-style: normal; padding: 0px;">
                                Upload new file
                            </label>
                        </td>
                        <td class="mini-table">
                            <input class="edit_entry_button" type="file" name="my_file" id="my_file" style="border: 1px solid #001F3F; border-radius: 5px" required>
                        </td>
                        <td class="mini-table">
                            <input class="edit_entry_button" type="submit" value="Submit" style="height: 20px; padding: 2px; verticle-align: center; margin-top: 3px;" onclick="confirmAction(event, 'Really want to replace the sequence? The old file will be deleted!')">
                        </td>
                        <td class="mini-table">
                            <a style="float:right; margin-left: 2px;" href="?upstrain_id=<?php echo $id; ?>&edit">Cancel</a>
                        </td>
                    </form>
        </tr>
        </table>
        <?php
    }
    ?>
    </td>
    </tr>

    <!-- Edit year created -->
    <tr class="edit_entry">
        <th class="title"> Year created: </th> 
        <td class="info">
            <?php echo $entry_info["year_created"]; ?>
        </td>
        <td class="edit">
            <?php
            if ($current_content != "year_created") {
                ?>
                <a href="?upstrain_id=<?php echo $id; ?>&edit&content=year_created">Edit</a>
            <?php } else {
                ?>
                <table class="mini-table">
                    <tr class="mini-table">
                    <form class="edit_entry" action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                        <td class="mini-table">
                            <label class="edit_entry" style="font-size: 14px; font-style: normal; padding: 0px;">New year</label>
                        </td>
                        <td class="mini-table">
                            <input class="edit_entry" type="text" name="year_created" required style="border: 1px solid #001F3F; border-radius: 5px"> 
                        </td>
                        <td class="mini-table">
                            <input class="edit_entry_button" type="submit" value="Submit" style="height: 20px; padding: 2px; verticle-align: center; margin-top: 3px;">
                        </td>
                        <td class="mini-table">
                            <a style="float:right; margin-left: 2px;" href="?upstrain_id=<?php echo $id; ?>&edit">Cancel</a>
                        </td>
                    </form>	
        </tr>
        </table>
        <?php
    }
    ?>
    </td>
    </tr>

    <!-- Edit created -->
    <tr class="edit_entry">
        <th class="title"> Created in lab? </th>
        <?php if ($entry_info['created']) { ?>
            <td class="info"> 
                Yes!
            </td>
            <td class="edit">
                <form action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                    <input type="hidden" name="created" value="0">
                    <input style="float: left;" class="edit_entry_button" type="submit" value="This is wrong">
                </form>
            </td>
        <?php } else { ?>
            <td class="info">
                No!
            </td>
            <td class="edit">
                <form action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                    <input type="hidden" name="created" value="1">
                    <input style="float: left;" class="edit_entry_button" type="submit" value="It's been created">
                </form>
            </td>
        <?php } ?>
    </tr>

    <!-- Edit private -->
    <tr class="edit_entry">
        <th class="title"> Private? </th>
        <?php if ($entry_info['private']) { ?>
            <td class="info">
                Yes!
            </td>
            <td class="edit">
                <form action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                    <input type="hidden" name="private" value="0">
                    <input style="float: left;" class="edit_entry_button" type="submit" value="Make public">
                </form>
            </td>
        <?php } else { ?>
            <td class="info">
                No!
            </td>
            <td class="edit">
                <form action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                    <input type="hidden" name="private" value="1">
                    <input style="float: left;" class="edit_entry_button" type="submit" value="Make private">
                </form>
            </td>
        <?php } ?>
    </tr>
    </table>

    <div class="clear"></div>
    <!-- Show success/error message -->
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($update_msg)): echo "<br>" . $update_msg;
    endif;
    ?>
    <!-- Back button -->
    <br>
    <br>
    <div class="back"><a href="?upstrain_id=<?php echo $id; ?>">Back to entry page</a></div>

    <?php
} else {
    if (!$loggedin) {
        ?>
        <h3 style="color:red">Access denied (you are not logged in).</h3>
        <br>
        <a href="entry.php?upstrain_id=<?php echo "$upstrain_id" ?> ">Go back to entry page</a>
        <?php
    } else if (!$active) {
        ?>
        <h3 style="color:red">Access denied (your account is not activated).</h3>
        <br>
        <a href="entry.php?upstrain_id=<?php echo "$upstrain_id" ?> ">Go back to entry page</a>
        <?php
    } else {
        ?>
        <h3 style="color:red">You are not allowed to edit entries (you are not an admin).</h3>
        <br>
        <a href="entry.php?upstrain_id=<?php echo "$upstrain_id" ?> ">Go back to entry page</a>
        <?php
    }
}
?>
</div>
<!-- Script that determines which insert options to show after picking an insert type -->
<script>
    $(document).ready(function () {
        $("#Ins_type").change(function () {
            var type_id = $(this).val();
            $.ajax({
                url: 'dropdown.php',
                method: "POST",
                data: {inst: type_id},
                dataType: "text",
                success: function (data) {
                    $("#Ins").html(data);
                }
            });

        });
    });

    function confirmAction(e, msg) {
        if (!confirm(msg))
            e.preventDefault();
    }
</script>
