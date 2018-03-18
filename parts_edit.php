<?php
if (count(get_included_files()) == 1) { // prevent direct access (included only)
    exit("Access restricted.");
}

// Displays page if user is logged in and is activated and has the right privileges
if ($loggedin && $active && $admin) {
    //Set display for the content div 
    if (isset($_GET['content'])) {
        $current_content = test_input($_GET['content']);
    } else {
        $current_content = "";
    }

    // Update procedures
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        include 'scripts/db.php';

        $iserror = FALSE;

        // Check which type of part we're dealing with.  
        if (isset($_GET["ins_id"])) {
            //Code to change insert if part = insert
            // Change insert name 
            if (isset($_POST['insert_name']) && !empty($_POST['insert_name'])) {
                $insert_name = mysqli_real_escape_string($link, test_input($_POST['insert_name']));
                // Check if characters have been removed
                if ($insert_name != $_POST['insert_name']) {
                    $iserror = TRUE;
                    $update_msg = "Insert name contains invalid characters.";
                    goto errorTime;
                }
                $update_sql = "UPDATE ins SET name = ? WHERE id = " . $part_id;
                // Do the change
                if ($stmt = mysqli_prepare($link, $update_sql)) {
                    mysqli_stmt_bind_param($stmt, "s", $insert_name);
                    if (mysqli_stmt_execute($stmt)) {
                        $update_msg = "Successfully updated insert name.";
                    } else {
                        $iserror = TRUE;
                        $update_msg = "Couldn't change insert name, failed to execute statement. " . mysqli_stmt_error($stmt);
                        goto errorTime;
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $iserror = TRUE;
                    $update_msg = "Couldn't change insert name, failed to prepare statement. " . mysqli_stmt_error($stmt);
                    goto errorTime;
                }

                // Change insert type    
            } else if (isset($_POST['insert_type']) && !empty($_POST['insert_type'])) {
                $insert_type = mysqli_real_escape_string($link, test_input($_POST['insert_type']));
                // Match user input against the name of the specific insert type in the database
                $type_query = "SELECT ins_type.id AS type_id FROM ins_type WHERE ins_type.name = '$insert_type'";
                $insert_type_query = mysqli_query($link, $type_query);
                $insert_type_info = mysqli_fetch_assoc($insert_type_query);
                if ($insert_type != $_POST['insert_type']) {
                    $iserror = TRUE;
                    $update_msg = "Insert type contains invalid characters.";
                    goto errorTime;
                }
                // Do the change
                $update_sql = "UPDATE ins SET type = ? WHERE id = " . $part_id;
                if ($stmt = mysqli_prepare($link, $update_sql)) {
                    mysqli_stmt_bind_param($stmt, "i", $insert_type_info["type_id"]);
                    if (mysqli_stmt_execute($stmt)) {
                        $update_msg = "Successfully updated insert type.";
                    } else {
                        $iserror = TRUE;
                        $update_msg = "Couldn't change insert type, failed to execute statement. " . mysqli_stmt_error($stmt);

                        goto errorTime;
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $iserror = TRUE;
                    $update_msg = "Couldn't change insert type, failed to prepare statement. " . mysqli_stmt_error($stmt);

                    goto errorTime;
                }
                // Change privacy of insert    
            } else if (isset($_POST['insert_private'])) {
                if (test_input($_POST['insert_private']) == 'Yes') {
                    $insert_private = 1;
                } else if (test_input($_POST['insert_private']) == 'No') {
                    $insert_private = 0;
                }
                // Do the change
                $update_sql = "UPDATE ins SET private = ? WHERE id = " . $part_id;
                if ($stmt = mysqli_prepare($link, $update_sql)) {
                    mysqli_stmt_bind_param($stmt, "i", $insert_private);
                    if (mysqli_stmt_execute($stmt)) {
                        $update_msg = "Successfully updated insert to private.";
                    } else {
                        $iserror = TRUE;
                        $update_msg = "Couldn't change insert to private, failed to execute statement. " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $iserror = TRUE;
                    $update_msg = "Couldn't change insert to private, failed to prepare statement. " . mysqli_stmt_error($stmt);
                }

                // Change separate values
            } else {
                // Change insert biobrick registry id
                if (isset($_POST['insert_reg']) && !empty($_POST['insert_reg'])) {
                    $to_update = "ins_reg";
                    $user_input = test_input($_POST['insert_reg']);
                    $update_val = mysqli_real_escape_string($link, $user_input);
                    $update_msg = "bb_reg";
                    // Change insert comment
                } else if (isset($_POST['insert_comment']) && !empty($_POST['insert_comment'])) {
                    $to_update = "comment";
                    $user_input = test_input($_POST['insert_comment']);
                    $update_val = mysqli_real_escape_string($link, $user_input);
                    $update_msg = "comment";
                    // Remove insert comment
                } else if (isset($_POST['remove_insert_reg'])) {
                    $remove_sql = "UPDATE ins SET ins_reg = '' WHERE id = " . $part_id;
                    if ($result = mysqli_query($link, $remove_sql)) {
                        $update_msg = "Successfully removed registry ID.";
                    } else {
                        $iserror = TRUE;
                        $update_msg = "Failed to remove registry ID. " . mysqli_error($link);
                    }
                }
                // Execute the change of biobrick id or comment
                if (isset($update_val) && $update_val != "" && !$iserror) {
                    // Check if user input invalid characters
                    if ($update_val != $user_input) {
                        $iserror = TRUE;
                        $update_msg = "Input " . $to_update . " contains invalid characters.";
                        goto errorTime;
                    }
                    // Prepare and execute statement
                    $update_sql = "UPDATE ins SET " . $to_update . " = ? WHERE id = " . $part_id;
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
            }
            // Check which type of part we're dealing with.           
        } else if (isset($_GET["backbone_id"])) {
            // Code to change backbone if part = backbone
            // Change backbone name 
            if (isset($_POST['backbone_name']) && !empty($_POST['backbone_name'])) {
                $backbone_name = mysqli_real_escape_string($link, test_input($_POST['backbone_name']));
                // Check if characters have been removed
                if ($backbone_name != $_POST['backbone_name']) {
                    $iserror = TRUE;
                    $update_msg = "Backbone name contains invalid characters.";
                    goto errorTime;
                }
                $update_sql = "UPDATE backbone SET name = ? WHERE id = " . $part_id;
                // Do the change
                if ($stmt = mysqli_prepare($link, $update_sql)) {
                    mysqli_stmt_bind_param($stmt, "s", $backbone_name);
                    if (mysqli_stmt_execute($stmt)) {
                        $update_msg = "Successfully updated backbone name.";
                    } else {
                        $iserror = TRUE;
                        $update_msg = "Couldn't change backbone name, failed to execute statement. " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $iserror = TRUE;
                    $update_msg = "Couldn't change backbone name, failed to prepare statement. " . mysqli_stmt_error($stmt);
                }
                // Change backbone privacy       
            } else if (isset($_POST['backbone_private'])) {
                if (test_input($_POST['backbone_private']) == 'Yes') {
                    $backbone_private = 1;
                } else if (test_input($_POST['backbone_private']) == 'No') {
                    $backbone_private = 0;
                }
                // Change the privacy
                $update_sql = "UPDATE backbone SET private = ? WHERE id = " . $part_id;
                if ($stmt = mysqli_prepare($link, $update_sql)) {
                    mysqli_stmt_bind_param($stmt, "i", $backbone_private);
                    if (mysqli_stmt_execute($stmt)) {
                        $update_msg = "Successfully updated backbone's privacy.";
                    } else {
                        $iserror = TRUE;
                        $update_msg = "Couldn't change backbone's privacy, failed to execute statement. " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $iserror = TRUE;
                    $update_msg = "Couldn't change backbone's privacy, failed to prepare statement. " . mysqli_stmt_error($stmt);
                }

                // Change separate values
            } else {
                // Change backbone biobrick registry id
                if (isset($_POST['backbone_reg']) && !empty($_POST['backbone_reg'])) {
                    $to_update = "Bb_reg";
                    $user_input = test_input($_POST['backbone_reg']);
                    $update_val = mysqli_real_escape_string($link, $user_input);
                    $update_msg = "bb_reg";
                    // Change backbone comment
                } else if (isset($_POST['backbone_comment']) && !empty($_POST['backbone_comment'])) {
                    $to_update = "comment";
                    $user_input = test_input($_POST['backbone_comment']);
                    $update_val = mysqli_real_escape_string($link, $user_input);
                    $update_msg = "comment";
                    // Remove backbone comment
                } else if (isset($_POST['remove_backbone_comment'])) {
                    $remove_sql = "UPDATE backbone SET comment = '' WHERE id = " . $part_id;
                    if ($result = mysqli_query($link, $remove_sql)) {
                        $update_msg = "Successfully removed comment.";
                    } else {
                        $iserror = TRUE;
                        $update_msg = "Failed to remove comment. " . mysqli_error($link);
                    }
                }
                // Execute the change of backbones biobrick id or comment
                if (isset($update_val) && $update_val != "" && !$iserror) {
                    // Check if user input invalid characters
                    if ($update_val != $user_input) {
                        $iserror = TRUE;
                        $update_msg = "Input " . $to_update . " contains invalid characters.";
                        goto errorTime;
                    }
                    // Prepare and execute statement
                    $update_sql = "UPDATE backbone SET " . $to_update . " = ? WHERE id = " . $part_id;
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
            }
            // Check which type of part we're dealing with.    
        } else if (isset($_GET["strain_id"])) {
            // Code to update strain if part=strain
            // Change strain name 
            if (isset($_POST['strain_name']) && !empty($_POST['strain_name'])) {
                $strain_name = mysqli_real_escape_string($link, test_input($_POST['strain_name']));
                // Check if characters have been removed
                if ($strain_name != $_POST['strain_name']) {
                    $iserror = TRUE;
                    $update_msg = "Strain name contains invalid characters.";
                    goto errorTime;
                }
                $update_sql = "UPDATE strain SET name = ? WHERE id = " . $part_id;
                // Do the change
                if ($stmt = mysqli_prepare($link, $update_sql)) {
                    mysqli_stmt_bind_param($stmt, "s", $strain_name);
                    if (mysqli_stmt_execute($stmt)) {
                        $update_msg = "Successfully updated strain name.";
                    } else {
                        $iserror = TRUE;
                        $update_msg = "Couldn't change strain name, failed to execute statement. " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $iserror = TRUE;
                    $update_msg = "Couldn't change strain name, failed to prepare statement. " . mysqli_stmt_error($stmt);
                }
                // Change privacy of strain       
            } else if (isset($_POST['strain_private'])) {
                if (test_input($_POST['strain_private']) == 'Yes') {
                    $strain_private = 1;
                } else if (test_input($_POST['strain_private']) == 'No') {
                    $strain_private = 0;
                }
                // Change privacy
                $update_sql = "UPDATE strain SET private = ? WHERE id = " . $part_id;
                if ($stmt = mysqli_prepare($link, $update_sql)) {
                    mysqli_stmt_bind_param($stmt, "i", $strain_private);
                    if (mysqli_stmt_execute($stmt)) {
                        $update_msg = "Successfully updated strain to private.";
                    } else {
                        $iserror = TRUE;
                        $update_msg = "Couldn't change strain to private, failed to execute statement. " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $iserror = TRUE;
                    $update_msg = "Couldn't change strain to private, failed to prepare statement. " . mysqli_stmt_error($stmt);
                }

                // Change separate values
            } else {
                // Change strain comment
                if (isset($_POST['strain_comment']) && !empty($_POST['strain_comment'])) {
                    $to_update = "comment";
                    $user_input = test_input($_POST['strain_comment']);
                    $update_val = mysqli_real_escape_string($link, $user_input);
                    $update_msg = "comment";
                    // Remove strain comment
                } else if (isset($_POST['remove_strain_comment'])) {
                    $remove_sql = "UPDATE strain SET comment = '' WHERE id = " . $part_id;
                    if ($result = mysqli_query($link, $remove_sql)) {
                        $update_msg = "Successfully removed comment.";
                    } else {
                        $iserror = TRUE;
                        $update_msg = "Failed to remove comment. " . mysqli_error($link);
                    }
                }
                // Execute the change of strain comment
                if (isset($update_val) && $update_val != "" && !$iserror) {
                    // Check if user input invalid characters
                    if ($update_val != $user_input) {
                        $iserror = TRUE;
                        $update_msg = "Input " . $to_update . " contains invalid characters.";
                        goto errorTime;
                    }
                    // Prepare and execute statement
                    $update_sql = "UPDATE strain SET " . $to_update . " = ? WHERE id = " . $part_id;
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
    ?>
    <!-- Confirm action popup -->
    <script>
        function confirmAction(e, msg) {
            if (!confirm(msg))
                e.preventDefault();
        }
    </script>

    <?php
    // Check if insert part
    if (isset($_GET["ins_id"])) {
        // Show the insert page
        // Fetch insert information from database
        include 'scripts/db.php';

        $insertsql = "SELECT ins.name AS name, ins.ins_reg AS biobrick, ins_type.name AS type, "
                . "ins.date_db AS date, ins.comment AS comment, ins.private AS private, "
                . "users.first_name AS fname, users.last_name AS lname, "
                . "users.user_id AS user_id FROM ins, ins_type, users "
                . "WHERE ins.id = '$id' AND ins.type = ins_type.id AND ins.creator = users.user_id";
        $insertquery = mysqli_query($link, $insertsql);

        $info = mysqli_fetch_assoc($insertquery);

        $typesql = "SELECT name FROM ins_type ORDER BY name ASC";
        $typequery = mysqli_query($link, $typesql);

        mysqli_close($link) or die("Could not close database connection");
        ?>
        <h2 class="search_etc">UpStrain Insert: <?= $info['name'] ?> - Edit</h2>
        <div class="edit_users">
            <table class="edit_users">
                <tr class="edit_users">
                    <th class="edit_users">
                        Name
                    </th>
                    <td class="info">
                        <?= $info['name'] ?>
                    </td>
                    <?php
                    if ($current_content != "insert_name") {
                        ?>
                        <td class="edit_users">
                            <a href="?ins_id=<?php echo $part_id; ?>&edit&content=insert_name">Edit</a>
                        </td>
                        <?php
                    } else {
                        ?>
                        <td class="edit_users">
                            <a href="?ins_id=<?php echo$part_id; ?>&edit">Cancel</a>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
                if ($current_content == "insert_name") {
                    ?>
                    <tr class="mini-table">
                        <td class="mini-table" colspan="2">
                            <form action="parts.php?ins_id=<?php echo $part_id; ?>&edit" method="POST">
                                <label class="mini-table" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block">
                                    New name: 
                                </label>
                                <br>
                                <input type="text" name="insert_name" style="border: 1px solid #001F3F; border-radius: 5px; display: inline-block; padding: 3px;" value="<?= $info['name'] ?>">
                                <br>
                                <input class="edit_entry_button" type="submit" value="Submit" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block; float:left">
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                ?>

                <tr class="edit_users">
                    <th class="edit_users">
                        Insert Type
                    </th>
                    <td class="info">
                        <?= $info['type'] ?>
                    </td>
                    <?php
                    if ($current_content != "insert_type") {
                        ?>
                        <td class="edit_users">
                            <a href="?ins_id=<?php echo $part_id; ?>&edit&content=insert_type">Edit</a>
                        </td>
                        <?php
                    } else {
                        ?>
                        <td class="edit_users">
                            <a href="?ins_id=<?php echo$part_id; ?>&edit">Cancel</a>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
                if ($current_content == "insert_type") {
                    ?>
                    <tr class="mini-table">
                        <td class="mini-table" colspan="2">
                            <form action="parts.php?ins_id=<?php echo $part_id; ?>&edit" method="POST">
                                <label class="mini-table" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block">
                                    New type: 
                                </label>
                                <br>
                                <select class="all" name="insert_type" style="border: 1px solid #001F3F; border-radius: 5px; display: inline-block; padding: 3px;">
                                    <option value=""></option>
                                    <?php
                                    while ($type = mysqli_fetch_assoc($typequery)) {
                                        echo "<option value=\"" . $type['name'] . "\">" . $type['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                                <br>
                                <input class="edit_entry_button" type="submit" value="Submit" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block; float:left">
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr class="edit_users">
                    <th class="edit_users">
                        iGEM Registy ID
                    </th>
                    <td class="info">
                        <?= $info['biobrick'] ?>
                    </td>
                    <?php
                    if ($current_content != "insert_biobrick") {
                        ?>
                        <td class="edit_users">
                            <a href="?ins_id=<?php echo $part_id; ?>&edit&content=insert_biobrick">Edit</a>
                        </td>
                        <td class="edit_users">
                            <form action="parts.php?ins_id=<?php echo $part_id; ?>&edit" method="POST">
                                <input type="hidden" name="remove_insert_reg"></input>
                                <input class="edit_entry_button" type="submit" value="Remove" style="height: 20px; padding: 2px; verticle-align: center; margin-top: 2px;"></input>
                            </form>
                        </td>
                        <?php
                    } else {
                        ?>
                        <td class="edit_users">
                            <a href="?ins_id=<?php echo$part_id; ?>&edit">Cancel</a>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
                if ($current_content == "insert_biobrick") {
                    ?>
                    <tr class="mini-table">
                        <td class="mini-table" colspan="2">
                            <form action="parts.php?ins_id=<?php echo $part_id; ?>&edit" method="POST">
                                <label class="mini-table" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block">
                                    New registry ID: 
                                </label>
                                <br>
                                <input type="text" name="insert_reg" placeholder="BBa_K[X]" pattern="BBa_K\d{4,12}" required title ="Biobrick ID must match pattern BBa_KXXXXX." style="border: 1px solid #001F3F; border-radius: 5px; display: inline-block; padding: 3px;">
                                <br>
                                <input class="edit_entry_button" type="submit" value="Submit" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block; float:left">
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr class="edit_users">
                    <th class="edit_users">
                        Comment
                    </th>
                    <td class="info">
                        <?= $info['comment'] ?>
                    </td>
                    <?php
                    if ($current_content != "insert_comment") {
                        ?>
                        <td class="edit_users">
                            <a href="?ins_id=<?php echo $part_id; ?>&edit&content=insert_comment">Edit</a>
                        </td>
                        <?php
                    } else {
                        ?>
                        <td class="edit_users">
                            <a href="?ins_id=<?php echo$part_id; ?>&edit">Cancel</a>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
                if ($current_content == "insert_comment") {
                    ?>
                    <tr class="mini-table">
                        <td class="mini-table" colspan="2">
                            <form action="parts.php?ins_id=<?php echo $part_id; ?>&edit" method="POST">
                                <label class="mini-table" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block">
                                    New comment: 
                                </label>
                                <br>
                                <textarea class="edit_entry" name="insert_comment" required style="border: 1px solid #001F3F; border-radius: 5px" rows ="8" cols="30"><?= $info['comment'] ?></textarea>
                                <br>
                                <input class="edit_entry_button" type="submit" value="Submit" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block; float:left">
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr class="edit_users">
                    <th class="edit_users">
                        Private?
                    </th>
                    <td class="info">
                        <?php
                        if ($info['private'] == 1): echo "Yes";
                        else: echo "No";
                        endif;
                        ?>
                    </td>
                    <?php
                    if ($current_content != "insert_private") {
                        ?>
                        <td class="edit_users">
                            <a href="?ins_id=<?php echo $part_id; ?>&edit&content=insert_private">Edit</a>
                        </td>
                        <?php
                    } else {
                        ?>
                        <td class="edit_users">
                            <a href="?ins_id=<?php echo$part_id; ?>&edit">Cancel</a>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
                if ($current_content == "insert_private") {
                    ?>
                    <tr class="mini-table">
                        <td class="mini-table" colspan="2">
                            <form action="parts.php?ins_id=<?php echo $part_id; ?>&edit" method="POST">
                                <label class="mini-table" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block">
                                    Private insert? 
                                </label>
                                <br>
                                <label style="display: inline-block"><input type="radio" name="insert_private" value='Yes'/>Yes</label>
                                <label style="display: inline-block"><input type="radio" name="insert_private" value="No"/>No</label>
                                <br>
                                <input class="edit_entry_button" type="submit" value="Submit" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block; float:left">
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <!-- Show success/error message -->
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($update_msg)): echo "<br>" . $update_msg;
            endif;
            ?>
            <!-- Back button -->
            <div class="back">
                <a href="?ins_id=<?php echo $part_id; ?>">Back to insert page</a>
            </div>
        </div>


        <?php
        // Check if Backbone part            
    } else if (isset($_GET["backbone_id"])) {

        // Show the backbone page
        // Fetch backbone information from database
        include 'scripts/db.php';

        $backbonesql = "SELECT backbone.name AS name, backbone.Bb_reg AS biobrick, "
                . "backbone.date_db AS date, backbone.comment AS comment, backbone.private AS private, "
                . "users.first_name AS fname, users.last_name AS lname, "
                . "users.user_id AS user_id FROM backbone, users "
                . "WHERE backbone.id = '$id' AND backbone.creator = users.user_id";
        $backbonequery = mysqli_query($link, $backbonesql);
        mysqli_close($link) or die("Could not close database connection");

        $info = mysqli_fetch_assoc($backbonequery);
        ?>
        <h2 class="search_etc">UpStrain Backbone: <?= $info['name'] ?> - Edit</h2>
        <div class="edit_users">
            <table class="edit_users">
                <tr class="edit_users">
                    <th class="edit_users">
                        Name
                    </th>
                    <td class="info">
                        <?php
                        echo $info['name'];
                        ?>
                    </td>
                    <?php
                    if ($current_content != "backbone_name") {
                        ?>
                        <td class="edit_users">
                            <a href="?backbone_id=<?php echo $part_id; ?>&edit&content=backbone_name">Edit</a>
                        </td>
                        <?php
                    } else {
                        ?>
                        <td class="edit_users">
                            <a href="?backbone_id=<?php echo$part_id; ?>&edit">Cancel</a>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
                if ($current_content == "backbone_name") {
                    ?>
                    <tr class="mini-table">
                        <td class="mini-table" colspan="2">
                            <form action="parts.php?backbone_id=<?php echo $part_id; ?>&edit" method="POST">
                                <label class="mini-table" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block">
                                    New name: 
                                </label>
                                <br>
                                <input type="text" name="backbone_name" required style="border: 1px solid #001F3F; border-radius: 5px; display: inline-block; padding: 3px;" value="<?= $info['name'] ?>">
                                <br>
                                <input class="edit_entry_button" type="submit" value="Submit" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block; float:left">
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                ?>

                <tr class="edit_users">
                    <th class="edit_users">
                        iGEM Registry ID
                    </th>
                    <td class="info">
                        <?php
                        echo $info['biobrick'];
                        ?>
                    </td>
                    <?php
                    if ($current_content != "backbone_biobrick") {
                        ?>
                        <td class="edit_users">
                            <a href="?backbone_id=<?php echo $part_id; ?>&edit&content=backbone_biobrick">Edit</a>
                        </td>
                        <?php
                    } else {
                        ?>
                        <td class="edit_users">
                            <a href="?backbone_id=<?php echo$part_id; ?>&edit">Cancel</a>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
                if ($current_content == "backbone_biobrick") {
                    ?>
                    <tr class="mini-table">
                        <td class="mini-table" colspan="2">
                            <form action="parts.php?backbone_id=<?php echo $part_id; ?>&edit" method="POST">
                                <label class="mini-table" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block">
                                    New registry ID: 
                                </label>
                                <br>
                                <input type="text" name="backbone_reg" placeholder="BBa_K[X]" pattern="BBa_K\d{4,12}" required title ="Biobrick ID must match pattern BBa_KXXXXX." style="border: 1px solid #001F3F; border-radius: 5px; display: inline-block; padding: 3px;">
                                <br>
                                <input class="edit_entry_button" type="submit" value="Submit" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block; float:left">
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                ?>

                <tr class="edit_users">
                    <th class="edit_users">
                        Comment
                    </th>
                    <td class="info">
                        <?php
                        echo $info['comment'];
                        ?>
                    </td>
                    <?php
                    if ($current_content != "backbone_comment") {
                        ?>
                        <td class="edit_users">
                            <a href="?backbone_id=<?php echo $part_id; ?>&edit&content=backbone_comment">Edit</a>
                        </td>
                        <?php
                    } else {
                        ?>
                        <td class="edit_users">
                            <a href="?backbone_id=<?php echo$part_id; ?>&edit">Cancel</a>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
                if ($current_content == "backbone_comment") {
                    ?>
                    <tr class="mini-table">
                        <td class="mini-table" colspan="2">
                            <form action="parts.php?backbone_id=<?php echo $part_id; ?>&edit" method="POST">
                                <label class="mini-table" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block">
                                    New registry ID: 
                                </label>
                                <br>
                                <textarea class="edit_entry" name="backbone_comment" required style="border: 1px solid #001F3F; border-radius: 5px" rows ="8" cols="30"><?= $info['comment'] ?></textarea>
                                <br>
                                <input class="edit_entry_button" type="submit" value="Submit" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block; float:left">
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                ?>

                <tr class="edit_users">
                    <th class="edit_users">
                        Private
                    </th>
                    <td class="info">
                        <?php
                        if ($info['private'] == 1): echo "Yes";
                        else: echo "No";
                        endif;
                        ?>
                    </td>
                    <?php
                    if ($current_content != "backbone_cprivate") {
                        ?>
                        <td class="edit_users">
                            <a href="?backbone_id=<?php echo $part_id; ?>&edit&content=backbone_private">Edit</a>
                        </td>
                        <?php
                    } else {
                        ?>
                        <td class="edit_users">
                            <a href="?backbone_id=<?php echo$part_id; ?>&edit">Cancel</a>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
                if ($current_content == "backbone_private") {
                    ?>
                    <tr class="mini-table">
                        <td class="mini-table" colspan="2">
                            <form action="parts.php?backbone_id=<?php echo $part_id; ?>&edit" method="POST">
                                <label class="mini-table" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block">
                                    Private backbone? 
                                </label>
                                <br>
                                <label style="display: inline-block"><input type="radio" name="backbone_private" value='Yes'/>Yes</label>
                                <label style="display: inline-block"><input type="radio" name="backbone_private" value="No"/>No</label>
                                <br>
                                <input class="edit_entry_button" type="submit" value="Submit" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block; float:left">
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>

            <!-- Show success/error message -->
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($update_msg)): echo "<br>" . $update_msg;
            endif;
            ?>
            <!-- Back button -->
            <div class="back">
                <a href="?backbone_id=<?php echo $part_id; ?>">Back to backbone page</a>
            </div>
        </div>

        <?php
        //Check if strain part      
    } else if (isset($_GET["strain_id"])) {

        // Show the strain page
        // Fetch strain information from database
        include 'scripts/db.php';

        $strainsql = "SELECT strain.name AS name, "
                . "strain.date_db AS date, strain.comment AS comment, strain.private AS private, "
                . "users.first_name AS fname, users.last_name AS lname, "
                . "users.user_id AS user_id FROM strain, users "
                . "WHERE strain.id = '$id' AND strain.creator = users.user_id";
        $strainquery = mysqli_query($link, $strainsql);
        mysqli_close($link) or die("Could not close database connection");

        $info = mysqli_fetch_assoc($strainquery);
        ?>

        <div class="edit_users">
            <h2 class="search_etc">UpStrain Strain: <?= $info['name'] ?> - Edit</h2>
            <table class="edit_users">
                <tr class="edit_users">
                    <th class="edit_users">
                        Name
                    </th>
                    <td class="info">
                        <?php
                        echo $info['name'];
                        ?>
                    </td>
                    <?php
                    if ($current_content != "strain_name") {
                        ?>
                        <td class="edit_users">
                            <a href="?strain_id=<?php echo $part_id; ?>&edit&content=strain_name">Edit</a>
                        </td>
                        <?php
                    } else {
                        ?>
                        <td class="edit_users">
                            <a href="?strain_id=<?php echo$part_id; ?>&edit">Cancel</a>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
                if ($current_content == "strain_name") {
                    ?>
                    <tr class="mini-table">
                        <td class="mini-table" colspan="2">
                            <form action="parts.php?strain_id=<?php echo $part_id; ?>&edit" method="POST">
                                <label class="mini-table" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block">
                                    New name: 
                                </label>
                                <br>
                                <input type="text" name="backbone_reg" required style="border: 1px solid #001F3F; border-radius: 5px; display: inline-block; padding: 3px;" value="<?= $info['name'] ?>">
                                <br>
                                <input class="edit_entry_button" type="submit" value="Submit" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block; float:left">
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                ?>

                <tr class="edit_users">
                    <th class="edit_users">
                        Comment
                    </th>
                    <td class="info">
                        <?php
                        echo $info['comment'];
                        ?>
                    </td>
                    <?php
                    if ($current_content != "strain_comment") {
                        ?>
                        <td class="edit_users">
                            <a href="?strain_id=<?php echo $part_id; ?>&edit&content=strain_comment">Edit</a>
                        </td>
                        <?php
                    } else {
                        ?>
                        <td class="edit_users">
                            <a href="?strain_id=<?php echo$part_id; ?>&edit">Cancel</a>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
                if ($current_content == "strain_comment") {
                    ?>
                    <tr class="mini-table">
                        <td class="mini-table" colspan="2">
                            <form action="parts.php?strain_id=<?php echo $part_id; ?>&edit" method="POST">
                                <label class="mini-table" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block">
                                    New comment: 
                                </label>
                                <br>
                                <textarea class="edit_entry" name="strain_comment" required style="border: 1px solid #001F3F; border-radius: 5px" rows ="8" cols="30"><?= $info['comment'] ?></textarea>
                                <br>
                                <input class="edit_entry_button" type="submit" value="Submit" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block; float:left">
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                ?>

                <tr class="edit_users">
                    <th class="edit_users">
                        Private
                    </th>
                    <td class="info">
                        <?php
                        if ($info['private'] == 1): echo "Yes";
                        else: echo "No";
                        endif;
                        ?>
                    </td>
                    <?php
                    if ($current_content != "strain_private") {
                        ?>
                        <td class="edit_users">
                            <a href="?strain_id=<?php echo $part_id; ?>&edit&content=strain_private">Edit</a>
                        </td>
                        <?php
                    } else {
                        ?>
                        <td class="edit_users">
                            <a href="?strain_id=<?php echo$part_id; ?>&edit">Cancel</a>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
                if ($current_content == "strain_private") {
                    ?>
                    <tr class="mini-table">
                        <td class="mini-table" colspan="2">
                            <form action="parts.php?strain_id=<?php echo $part_id; ?>&edit" method="POST">
                                <label class="mini-table" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block">
                                    Private strain? 
                                </label>
                                <br>
                                <label style="display: inline-block"><input type="radio" name="strain_private" value='Yes'/>Yes</label>
                                <label style="display: inline-block"><input type="radio" name="strain_private" value="No"/>No</label>
                                <br>
                                <input class="edit_entry_button" type="submit" value="Submit" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block; float:left">
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <!-- Show success/error message -->
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($update_msg)): echo "<br>" . $update_msg;
            endif;
            ?>

            <!-- Back button -->
            <div class="back">
                <a href="?strain_id=<?php echo $part_id; ?>">Back to strain page</a>
            </div>
        </div>


        <?php
    }
// Hides page if the user is not logged in or activated
} else {
    if (isset($_GET["ins_id"])) {
        if (!$loggedin) {
            ?>
            <h3 style="color:red">Access denied (you are not logged in).</h3>
            <br>
            <a href="parts.php?ins_id=<?php echo "$ins_id" ?> ">Go back to insert page</a>
            <?php
        } else if (!$active) {
            ?>
            <h3 style="color:red">Access denied (your account is not activated).</h3>
            <br>
            <a href="parts.php?ins_id=<?php echo "$ins_id" ?> ">Go back to insert page</a>
            <?php
        } else {
            ?>
            <h3 style="color:red">You are not allowed to edit inserts (you are not an admin).</h3>
            <br>
            <a href="parts.php?ins_id=<?php echo "$ins_id" ?> ">Go back to insert page</a>
            <?php
        }
    } else if (isset($_GET["backbone_id"])) {
        if (!$loggedin) {
            ?>
            <h3 style="color:red">Access denied (you are not logged in).</h3>
            <br>
            <a href="parts.php?backbone_id=<?php echo "$backbone_id" ?> ">Go back to backbone page</a>
            <?php
        } else if (!$active) {
            ?>
            <h3 style="color:red">Access denied (your account is not activated).</h3>
            <br>
            <a href="parts.php?backbone_id=<?php echo "$backbone_id" ?> ">Go back to backbone page</a>
            <?php
        } else {
            ?>
            <h3 style="color:red">You are not allowed to edit backbones (you are not an admin).</h3>
            <br>
            <a href="parts.php?backbone_id=<?php echo "$backbone_id" ?> ">Go back to backbone page</a>
            <?php
        }
    } else if (isset($_GET["strain_id"])) {
        if (!$loggedin) {
            ?>
            <h3 style="color:red">Access denied (you are not logged in).</h3>
            <br>
            <a href="parts.php?strain_id=<?php echo "$strain_id" ?> ">Go back to strain page</a>
            <?php
        } else if (!$active) {
            ?>
            <h3 style="color:red">Access denied (your account is not activated).</h3>
            <br>
            <a href="parts.php?strain_id=<?php echo "$strain_id" ?> ">Go back to strain page</a>
            <?php
        } else {
            ?>
            <h3 style="color:red">You are not allowed to edit strains (you are not an admin).</h3>
            <br>
            <a href="parts.php?strain_id=<?php echo "$strain_id" ?> ">Go back to strain page</a>
            <?php
        }
    }
}
