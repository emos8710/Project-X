<?php
if (count(get_included_files()) == 1)
    exit("Access restricted."); // prevent direct access (included only)


    
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
                } else if (isset($_POST['remove_insert_comment'])) {
                    $remove_sql = "UPDATE ins SET comment = '' WHERE id = " . $part_id;
                    if ($result = mysqli_query($link, $remove_sql)) {
                        $update_msg = "Successfully removed comment.";
                    } else {
                        $iserror = TRUE;
                        $update_msg = "Failed to remove comment. " . mysqli_error($link);
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
        mysqli_close($link) or die("Could not close database connection");

        $info = mysqli_fetch_assoc($insertquery);
        ?>
        <h1>Edit Insert</h1>
        <div class="edit_users">
            <ul>
                <!-- Edit name -->
                <li><div class="edit_title">Name</div>	<?php
        echo $info["name"];
        if ($current_content != "insert_name") {
            ?>
                        <div class="edit_info"><a href="?ins_id=<?php echo $part_id; ?>&edit&content=insert_name">Edit</a></div> 
                    <?php } ?></li>
                    <?php if ($current_content == "insert_name") { ?>
                    <li><form action="parts.php?ins_id=<?php echo $part_id; ?>&edit" method="POST">
                            New insert name
                            <input type="text" name="insert_name">
                            <input type="submit" value="Submit">
                            <a href="?ins_id=<?php echo $part_id; ?>&edit">Cancel</a>
                        </form></li>
        <?php } ?>

                <!-- Edit insert type -->
                <li><div class="edit_title">Insert type</div> <?php
        echo $info["type"];
        if ($current_content != "insert_type") {
            ?>
                        <div class="edit_info"><a href="?ins_id=<?php echo $part_id; ?>&edit&content=insert_type">Edit</a></div>
                    <?php } ?></li>
                    <?php if ($current_content == "insert_type") { ?>
                    <li><form action="parts.php?ins_id=<?php echo $part_id; ?>&edit" method="POST">
                            New Insert Type
                            <select class="all" name="insert_type">
                                <option value=""></option>    
                                <option value="Promotor">Promotor</option>
                                <option value="Coding">Coding</option>
                                <option value="RBS">RBS</option>
                            </select>
                            <input type="submit" value="Submit">
                            <a href="?ins_id=<?php echo $part_id; ?>&edit">Cancel</a>
                        </form></li>
                <?php } ?>                        

                <!-- Edit biobrick registry id -->
                <li><div class="edit_title">Biobrick registry id</div> <?php
                echo $info["biobrick"];
                if ($current_content != "insert_biobrick") {
                    ?>
                        <div class="edit_info"><a href="?ins_id=<?php echo $part_id; ?>&edit&content=insert_biobrick">Edit</a></div>
                    <?php } ?></li>
                <?php if ($current_content == "insert_biobrick") { ?>
                    <li><form action="parts.php?ins_id=<?php echo $part_id; ?>&edit" method="POST">
                            New biobrick id
                            <input type="text" name="insert_reg" placeholder="BBa_K[X]" pattern="BBa_K\d{4,12}" required title ="Biobrick ID must match pattern BBa_KXXXXX."/>
                            <input type="submit" value="Submit">
                            <a href="?ins_id=<?php echo $part_id; ?>&edit">Cancel</a>
                        </form></li>
                <?php } ?>

                <!-- Edit comment -->
                <li><div class="edit_title">Comment</div> <?php
                    echo $info["comment"];
                    if ($current_content != "insert_comment") {
                        ?>
                        <div class="edit_info"><a href="?ins_id=<?php echo $part_id; ?>&edit&content=insert_comment">Edit</a></div>
                        <form action="parts.php?ins_id=<?php echo $part_id; ?>&edit" method="POST">
                            <input type="hidden" name="remove_insert_comment">
                            <input type="submit" value="Remove">
                        </form>
                <?php } ?></li>
        <?php if ($current_content == "comment") { ?>
                    <li><form action="parts.php?ins_id=<?php echo $part_id; ?>&edit" method="POST">
                            New comment
                            <input type="text" name="insert_comment"> 
                            <input type="submit" value="Submit">
                            <a href="?ins_id=<?php echo $part_id; ?>&edit">Cancel</a>
                        </form></li>
                <?php }
                ?>

                <!-- Edit private -->
                <li><div class="edit_title">Private</div> <?php
                    if ($info["private"] == 1) {
                        echo "Yes";
                    } else {
                        echo "No";
                    }
                    if ($current_content != "insert_private") {
                        ?>
                        <div class="edit_info"><a href="?ins_id=<?php echo $part_id; ?>&edit&content=insert_private">Edit</a></div>
        <?php } ?></li>
        <?php if ($current_content == "insert_private") { ?>
                    <li><form action="parts.php?ins_id=<?php echo $part_id; ?>&edit" method="POST">
                            Private insert?
                            <input type="radio" name="insert_private" value='Yes'/>Yes
                            <input type="radio" name="insert_private" value="No"/>No<br>
                            <input type="submit" value="Submit">
                            <a href="?ins_id=<?php echo $part_id; ?>&edit">Cancel</a>
                        </form></li>
                    <?php } ?>                        

                <ul>

                    <div class="clear"></div>
                    <!-- Show success/error message -->
                    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($update_msg)): echo "<br>" . $update_msg;
                    endif;
                    ?>
                    <!-- Back button -->
                    <div class="back"><a href="?ins_id=<?php echo $part_id; ?>">Back to insert page</a></div>

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
                    <h1>Edit Backbone</h1>
                    <div class="edit_users">
                        <ul>
                            <!-- Edit name -->
                            <li><div class="edit_title">Name</div>	<?php
                        echo $info["name"];
                        if ($current_content != "backbone_name") {
                            ?>
                                    <div class="edit_info"><a href="?backbone_id=<?php echo $part_id; ?>&edit&content=backbone_name">Edit</a></div> 
                            <?php } ?></li>
                            <?php if ($current_content == "backbone_name") { ?>
                                <li><form action="parts.php?backbone_id=<?php echo $part_id; ?>&edit" method="POST">
                                        New backbone name
                                        <input type="text" name="backbone_name">
                                        <input type="submit" value="Submit">
                                        <a href="?backbone_id=<?php echo $part_id; ?>&edit">Cancel</a>
                                    </form></li>
                                <?php } ?>


                            <!-- Edit biobrick registry id -->
                            <li><div class="edit_title">Biobrick registry id</div> <?php
                                echo $info["biobrick"];
                                if ($current_content != "backbone_biobrick") {
                                    ?>
                                    <div class="edit_info"><a href="?backbone_id=<?php echo $part_id; ?>&edit&content=backbone_biobrick">Edit</a></div>
                            <?php } ?></li>
        <?php if ($current_content == "backbone_biobrick") { ?>
                                <li><form action="parts.php?backbone_id=<?php echo $part_id; ?>&edit" method="POST">
                                        New biobrick id
                                        <input type="text" name="backbone_reg" placeholder="BBa_K[X]" pattern="BBa_K\d{4,12}" required title ="Biobrick ID must match pattern BBa_KXXXXX."/>
                                        <input type="submit" value="Submit">
                                        <a href="?backbone_id=<?php echo $part_id; ?>&edit">Cancel</a>
                                    </form></li>
        <?php } ?>

                            <!-- Edit comment -->
                            <li><div class="edit_title">Comment</div> <?php
                            echo $info["comment"];
                            if ($current_content != "backbone_comment") {
                                ?>
                                    <div class="edit_info"><a href="?backbone_id=<?php echo $part_id; ?>&edit&content=backbone_comment">Edit</a></div>
                                    <form action="parts.php?backbone_id=<?php echo $part_id; ?>&edit" method="POST">
                                        <input type="hidden" name="remove_backbone_comment">
                                        <input type="submit" value="Remove">
                                    </form>
                            <?php } ?></li>
        <?php if ($current_content == "backbone_comment") { ?>
                                <li><form action="parts.php?backbone_id=<?php echo $part_id; ?>&edit" method="POST">
                                        New comment
                                        <input type="text" name="backbone_comment"> 
                                        <input type="submit" value="Submit">
                                        <a href="?backbone_id=<?php echo $part_id; ?>&edit">Cancel</a>
                                    </form></li>
                            <?php }
                            ?>

                            <!-- Edit private -->
                            <li><div class="edit_title">Private</div> <?php
                            if ($info["private"] == 1) {
                                echo "Yes";
                            } else {
                                echo "No";
                            }
                            if ($current_content != "backbone_private") {
                                ?>
                                    <div class="edit_info"><a href="?backbone_id=<?php echo $part_id; ?>&edit&content=backbone_private">Edit</a></div>
                                <?php } ?></li>
                                <?php if ($current_content == "backbone_private") { ?>
                                <li><form action="parts.php?backbone_id=<?php echo $part_id; ?>&edit" method="POST">
                                        Private backbone?
                                        <input type="radio" name="backbone_private" value='Yes'/>Yes
                                        <input type="radio" name="backbone_private" value="No"/>No<br>
                                        <input type="submit" value="Submit">
                                        <a href="?backbone_id=<?php echo $part_id; ?>&edit">Cancel</a>
                                    </form></li>
                                <?php } ?>                        

                            <ul>

                                <div class="clear"></div>
                                <!-- Show success/error message -->
                                <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($update_msg)): echo "<br>" . $update_msg;
                                endif;
                                ?>
                                <!-- Back button -->
                                <div class="back"><a href="?backbone_id=<?php echo $part_id; ?>">Back to backbone page</a></div>

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
                                <h1>Edit Strain</h1>
                                <div class="edit_users">
                                    <ul>
                                        <!-- Edit name -->
                                        <li><div class="edit_title">Name</div>	<?php
                                echo $info["name"];
                                if ($current_content != "strain_name") {
                                    ?>
                                                <div class="edit_info"><a href="?strain_id=<?php echo $part_id; ?>&edit&content=strain_name">Edit</a></div> 
        <?php } ?></li>
        <?php if ($current_content == "strain_name") { ?>
                                            <li><form action="parts.php?strain_id=<?php echo $part_id; ?>&edit" method="POST">
                                                    New strain name
                                                    <input type="text" name="strain_name">
                                                    <input type="submit" value="Submit">
                                                    <a href="?strain_id=<?php echo $part_id; ?>&edit">Cancel</a>
                                                </form></li>
        <?php } ?>


                                        <!-- Edit comment -->
                                        <li><div class="edit_title">Comment</div> <?php
                                        echo $info["comment"];
                                        if ($current_content != "strain_comment") {
                                            ?>
                                                <div class="edit_info"><a href="?strain_id=<?php echo $part_id; ?>&edit&content=strain_comment">Edit</a></div>
                                                <form action="parts.php?strain_id=<?php echo $part_id; ?>&edit" method="POST">
                                                    <input type="hidden" name="remove_strain_comment">
                                                    <input type="submit" value="Remove">
                                                </form>
                                            <?php } ?></li>
                                        <?php if ($current_content == "strain_comment") { ?>
                                            <li><form action="parts.php?strain_id=<?php echo $part_id; ?>&edit" method="POST">
                                                    New comment
                                                    <input type="text" name="strain_comment"> 
                                                    <input type="submit" value="Submit">
                                                    <a href="?strain_id=<?php echo $part_id; ?>&edit">Cancel</a>
                                                </form></li>
                                        <?php }
                                        ?>

                                        <!-- Edit private -->
                                        <li><div class="edit_title">Private</div> <?php
                                        if ($info["private"] == 1) {
                                            echo "Yes";
                                        } else {
                                            echo "No";
                                        }
                                        if ($current_content != "strain_private") {
                                            ?>
                                                <div class="edit_info"><a href="?strain_id=<?php echo $part_id; ?>&edit&content=strain_private">Edit</a></div>
                                            <?php } ?></li>
                                            <?php if ($current_content == "strain_private") { ?>
                                            <li><form action="parts.php?strain_id=<?php echo $part_id; ?>&edit" method="POST">
                                                    Private strain?
                                                    <input type="radio" name="strain_private" value='Yes'/>Yes
                                                    <input type="radio" name="strain_private" value="No"/>No<br>
                                                    <a href="?strain_id=<?php echo $part_id; ?>&edit">Cancel</a>
                                                </form></li>
                                            <?php } ?>                        

                                        <ul>

                                            <div class="clear"></div>
                                            <!-- Show success/error message -->
                                            <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($update_msg)): echo "<br>" . $update_msg;
                                            endif;
                                            ?>
                                            <!-- Back button -->
                                            <div class="back"><a href="?strain_id=<?php echo $part_id; ?>">Back to strain page</a></div>

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
