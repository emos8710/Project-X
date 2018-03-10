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
            
            if ($result = mysqli_query($link, $remove_sql)) {
                $update_msg = "Successfully removed insert.";
            } else {
                $iserror = TRUE;
                $update_msg = "Failed to remove insert. " . mysqli_error($link);
                goto errorTime;
            }
            // Add new insert
        } else if (isset($_POST['new_insert']) && !empty($_POST['new_insert']) && isset($_POST['position']) && !empty($_POST['position'])){
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

            // Change comment
        } else if (isset($_POST['comment']) && !empty($_POST['comment'])){
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
        } else if (isset($_POST['created']) && !empty($_POST['created'])){
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
        }

        // Execute the change of registry link, strain, backbone or comment
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

    mysqli_close($link);
    ?>

    <!-- Edit forms -->
    <h2>UpStrain ID <?php echo $id; ?></h2>
    <ul>
        <!-- Edit registry ID -->
        <li>Registry ID <?php
            echo $entry_info["biobrick"];
            if ($current_content != "biobrick") {
                ?>
                <a href="?upstrain_id=<?php echo $id; ?>&edit&content=biobrick">Edit</a>
            <?php } ?></li>
        <?php if ($current_content == "biobrick") { ?>
            <li><form action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                    New registry ID
                    <input type="text" name="biobrick" required> 
                    <input type="submit" value="Submit">
                    <a href="?upstrain_id=<?php echo $id; ?>&edit">Cancel</a>
                </form></li>
        <?php } ?>

        <!-- Edit strain -->
        <li>Strain <?php
            echo $entry_info["sname"];
            if ($current_content != "strain") {
                ?>
                <a href="?upstrain_id=<?php echo $id; ?>&edit&content=strain">Edit</a>
            <?php } ?></li>
        <?php if ($current_content == "strain") { ?>
            <li><form action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                    New strain
                    <select name="strain" required>
                        <?php
                        include 'scripts/db.php';
                        $sql_strain = mysqli_query($link, "SELECT name FROM strain");
                        while ($row = $sql_strain->fetch_assoc()) {
                            echo "<option>" . $row['name'] . "</option>";
                        }
                        mysqli_close($link);
                        ?>
                    </select>
                    <input type="submit" value="Submit">
                    <a href="?upstrain_id=<?php echo $id; ?>&edit">Cancel</a>
                </form></li>
        <?php } ?>

        <!-- Edit backbone -->
        <li>Backbone <?php
            echo $entry_info["bname"];
            if ($current_content != "backbone") {
                ?>
                <a href="?upstrain_id=<?php echo $id; ?>&edit&content=backbone">Edit</a>
            <?php } ?></li>
        <?php if ($current_content == "backbone") { ?>
            <li><form action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                    New backbone
                    <select name="backbone" required>
                        <?php
                        include 'scripts/db.php';
                        $sql_strain = mysqli_query($link, "SELECT name FROM backbone");
                        while ($row = $sql_strain->fetch_assoc()) {
                            echo "<option>" . $row['name'] . "</option>";
                        }
                        mysqli_close($link);
                        ?>
                    </select>
                    <input type="submit" value="Submit">
                    <a href="?upstrain_id=<?php echo $id; ?>&edit">Cancel</a>
                </form></li>
        <?php } ?>

        <!-- Edit inserts -->
        <li>Inserts</li>
        <ol>
            <?php while ($insert_row = mysqli_fetch_assoc($insert_result)) { ?>
                <li>Insert <?php echo $insert_row['name'] ?> Type <?php
                    echo $insert_row['type'];
                    if ($current_content != "insert" . $insert_row['position']) {
                        ?>
                        <a href="?upstrain_id=<?php echo $id; ?>&edit&content=insert<?php echo $insert_row['position'] ?>">Edit</a>
                        <form action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                            <input name="remove_insert" type="hidden">
                            <input name="position" type="hidden" value="<?php echo $insert_row['position'] ?>">
                            <input type="submit" value="Remove">
                        </form>
                    <?php } ?>
                </li>
                <?php if ($current_content == "insert" . $insert_row['position']) { ?>
                    <br>
                    <form action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                        Insert type
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

                        Insert name
                        <select name="insert" id ="Ins" required>
                            <option value="">Select insert name</option>
                        </select>

                        <!-- Send insert position -->
                        <input name="position" type="hidden" value="<?php echo $insert_row['position'] ?>">

                        <input type="submit" value="Submit" >
                        <a href="?upstrain_id=<?php echo $id; ?>&edit">Cancel</a>
                    </form>
                <?php } ?>
            <?php } ?>
        </ol>

        <!-- Add new insert -->
        <ul>
            <li>
                <a href="?upstrain_id=<?php echo $id; ?>&edit&content=add_insert">Add insert</a>
            </li>
            <?php if ($current_content == "add_insert") { ?>
                <li><form action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                        Insert type
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

                        Insert name
                        <select name="new_insert" id ="Ins" required>
                            <option value="">Select insert name</option>
                        </select>

                        <?php
                        include 'scripts/db.php';
                        $pos_query = "SELECT MAX(position) FROM entry_inserts WHERE entry_id = '$entry_id'";
                        $pos_result = mysqli_query($link, $pos_query);
                        $position = mysqli_fetch_array($pos_result)[0] + 1;
                        ?>

                        <input name="position" type="hidden" value="<?php echo $position ?>">

                        <input type="submit" value="Submit" >
                        <a href="?upstrain_id=<?php echo $id; ?>&edit">Cancel</a>
                    </form></li>
            <?php } ?>
        </ul>

        <!-- Edit comment -->
        <li>Comment <?php
            echo $entry_info["comment"];
            if ($current_content != "comment") {
                ?>
                <a href="?upstrain_id=<?php echo $id; ?>&edit&content=comment">Edit</a>
            <?php } ?></li>
        <?php
        if ($current_content == "comment") {
            include 'scripts/db.php';
            $sql_comment = mysqli_query($link, "SELECT comment FROM entry WHERE id = '$entry_id'");
            $old_comment = mysqli_fetch_array($sql_comment)[0];
            ?>
            <li><form action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                    Edit comment
                    <input type="text" name="comment" required value="<?php echo $old_comment ?>"> 
                    <input type="submit" value="Submit">
                    <a href="?upstrain_id=<?php echo $id; ?>&edit">Cancel</a>
                </form></li>
        <?php } ?>

        <!-- Edit year created -->
        <li>Year created <?php
            echo $entry_info["year_created"];
            if ($current_content != "year_created") {
                ?>
                <a href="?upstrain_id=<?php echo $id; ?>&edit&content=year_created">Edit</a>
            <?php } ?></li>
        <?php if ($current_content == "year_created") { ?>
            <li><form action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                    New year
                    <input type="text" name="year_created" required> 
                    <input type="submit" value="Submit">
                    <a href="?upstrain_id=<?php echo $id; ?>&edit">Cancel</a>
                </form></li>
        <?php } ?>

        <!-- Edit created -->
        <?php if ($entry_info['created']) { ?>
            <li>This entry has been created in the lab
                <form action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                    <input type="hidden" name="created" value="0">
                    <input type="submit" value="This is wrong">
                </form></li>
        <?php } else { ?>
            <li>This entry has NOT been created in the lab
                <form action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                    <input type="hidden" name="created" value="1">
                    <input type="submit" value="It's been created">
                </form></li>
        <?php } ?>

        <!-- Edit private -->
        <?php if ($entry_info['private']) { ?>
            <li>This entry is private
                <form action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                    <input type="hidden" name="private" value="0">
                    <input type="submit" value="Make public">
                </form></li>
        <?php } else { ?>
            <li>This entry is public
                <form action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                    <input type="hidden" name="private" value="1">
                    <input type="submit" value="Make private">
                </form></li>
        <?php } ?>
    </ul>

    <div class="clear"></div>
    <!-- Show success/error message -->
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($update_msg)): echo "<br>" . $update_msg;
    endif;
    ?>
    <!-- Back button -->
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
</script>
