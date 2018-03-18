<?php
if (count(get_included_files()) == 1): exit("Access restricted.");
endif;

// prevent direct access (included only)
// Displays page if user is logged in and is activated and has the right privileges
if ($loggedin && $active && $userpage_owner_or_admin) {
//Set display for the content div
    if (isset($_GET['content'])) {
        $current_content = $_GET['content'];
    } else {
        $current_content = "";
    }

// Update procedures
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        include 'scripts/db.php';

        $iserror = FALSE;

// Change first and last name at the same time
        if (isset($_POST['first_name']) && isset($_POST['last_name']) && !empty($_POST['first_name']) && !empty($_POST['last_name'])) {
            $fname = mysqli_real_escape_string($link, test_input($_POST['first_name']));
            $lname = mysqli_real_escape_string($link, test_input($_POST['last_name']));
// Check if characters have been removed
            if ($fname != $_POST['first_name']) {
                $iserror = TRUE;
                $update_msg = "First name contains invalid characters.";
                goto errorTime;
            } else if ($lname != $_POST['last_name']) {
                $iserror = TRUE;
                $update_msg = "Last name contains invalid characters.";
                goto errorTime;
            }
            $update_sql = "UPDATE users SET first_name = ?, last_name = ? WHERE user_id = " . $user_id;
// Do the change
            if ($stmt = mysqli_prepare($link, $update_sql)) {
                mysqli_stmt_bind_param($stmt, "ss", $fname, $lname);
                if (mysqli_stmt_execute($stmt)) {
                    $update_msg = "Successfully updated first and last name.";
                } else {
                    $iserror = TRUE;
                    $update_msg = "Couldn't change first and last name, failed to execute statement. " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                $iserror = TRUE;
                $update_msg = "Couldn't change first and last name, failed to prepare statement. " . mysqli_stmt_error($stmt);
            }
// Change separate values
        } else {
// Change first name
            if (isset($_POST['first_name']) && !empty($_POST['first_name']) && $_POST['last_name'] === "") {
                $to_update = "first_name";
                $user_input = test_input($_POST['first_name']);
                $update_val = mysqli_real_escape_string($link, $user_input);
                $update_msg = "first name";
// Change last name
            } else if (isset($_POST['last_name']) && !empty($_POST['last_name']) && $_POST['first_name'] === "") {
                $to_update = "last_name";
                $user_input = test_input($_POST['last_name']);
                $update_val = mysqli_real_escape_string($link, $user_input);
                $update_msg = "last name";
// Change user name
            } else if (isset($_POST['user_name']) && !empty($_POST['user_name'])) {
// Check if an admin is trying to change another admin's username
                if ($adminpage && !$isowner) {
                    $iserror = TRUE;
                    $update_msg = "Can't change other admin's username.";
                } else {
                    $to_update = "username";
                    $user_input = test_input($_POST['user_name']);
                    $update_val = mysqli_real_escape_string($link, $user_input);
                    $update_msg = "username";
                }
// Change email
            } else if (isset($_POST['email']) && !empty($_POST['email'])) {
                $to_update = "email";
                $user_input = test_input($_POST['email']);
                $update_val = mysqli_real_escape_string($link, $user_input);
                $update_msg = "email";
// Change phone number
            } else if (isset($_POST['phone']) && !empty($_POST['phone'])) {
                $to_update = "phone";
                $user_input = test_input($_POST['phone']);
                $update_val = mysqli_real_escape_string($link, $user_input);
                $update_msg = "phone number";
// Remove phone number
            } else if (isset($_POST['remove_phone'])) {
                $remove_sql = "UPDATE users SET phone = '' WHERE user_id = " . $user_id;
                if ($result = mysqli_query($link, $remove_sql)) {
                    $update_msg = "Successfully removed phone number.";
                } else {
                    $iserror = TRUE;
                    $update_msg = "Failed to remove phone number. " . mysqli_error($link);
                }
// Change password
            } else if (isset($_POST['old_password']) && isset($_POST['new_password']) && isset($_POST['conf_password']) && !empty($_POST['old_password']) && !empty($_POST['new_password']) && !empty($_POST['conf_password'])) {

// Check that the input old password matches the stored password
                $result = mysqli_query($link, "SELECT password FROM users WHERE user_id = '$user_id'");
                $password = mysqli_fetch_assoc($result);
                $password = $password['password'];
                if (!password_verify($_POST['old_password'], $password)) {
                    $iserror = TRUE;
                    $update_msg = "Wrong password.";
                    goto errorTime;
                }
// Make sure user is changing their own password, and that the new password qualifies
                if ($_SESSION['user_id'] != $user_id) {
                    $iserror = TRUE;
                    $update_msg = "Can't change someone else's password!";
                    goto errorTime;
                } else if (strlen($_POST['new_password']) < 8) {
                    $iserror = TRUE;
                    $update_msg = "The new password is too short.";
                    goto errorTime;
                } else if ($_POST['new_password'] != $_POST['conf_password']) {
                    $iserror = TRUE;
                    $update_msg = "The new passwords don't match.";
                    goto errorTime;
                }

// Hash and change to new password
                $hash = mysqli_real_escape_string($link, password_hash($_POST['new_password'], PASSWORD_BCRYPT));
                $password_sql = "UPDATE users SET password = '$hash' WHERE user_id = '$user_id'";
                if ($result = mysqli_query($link, $password_sql)) {
                    $update_msg = "Successfully changed the password.";
                } else {
                    $iserror = TRUE;
                    $update_msg = "Failed to change the password. " . mysqli_error($link);
                }
            }
// Execute the change of first name, last name, username, email or phone number
            if (isset($update_val) && $update_val != "" && !$iserror) {
// Check if user input invalid characters
                if ($update_val != $user_input) {
                    $iserror = TRUE;
                    $update_msg = "Input " . $to_update . " contains invalid characters.";
                    goto errorTime;
                }
// Prepare and execute statement
                $update_sql = "UPDATE users SET " . $to_update . " = ? WHERE user_id = " . $user_id;
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
// Show the user page
// Fetch user information from database
    include 'scripts/db.php';

    $usersql = "SELECT first_name AS fname, last_name AS lname, "
            . "email, phone, username AS uname, admin FROM users WHERE user_id = '$id'";
    $user_result = mysqli_query($link, $usersql);
    mysqli_close($link) or die("Could not close database connection");

    $info = mysqli_fetch_assoc($user_result);
    ?>
    <h2 class="search_etc">User <?php echo $info['uname']; ?> - Edit</h2>    
    <div class="edit_users">
        <table class="edit_users">
            <!-- Edit name -->
            <tr class="edit_users">
                <th class="edit_users" style="border-bottom: none; text-align: left;">
                    Name:
                </th>
                <td class="edit_users">
                    <?php
                    echo $info["fname"] . " " . $info["lname"];
                    ?>
                </td>
                <?php
                if ($current_content != "name") {
                    ?>
                    <td class="edit_users">
                        <a href="?user_id=<?php echo $user_id; ?>&edit&content=name">Edit</a>
                    </td>
                    <?php
                } else {
                    ?>
                    <td class="mini-table">
                        <a href="?user_id=<?php echo $user_id; ?>&edit">Cancel</a>
                    </td>
                    <?php
                }
                ?>
            </tr>
            <?php
            if ($current_content == "name") {
                ?>
                <tr class="mini-table">
                    <td class="mini-table" colspan="2">
                        <form action="user.php?user_id=<?php echo $user_id; ?>&edit" method="POST">
                            <label class="mini-table" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block">
                                First name:
                            </label>
                            <input type="text" name="first_name" style="border: 1px solid #001F3F; border-radius: 5px; display: inline-block; padding: 3px;" value="<?= $info['fname'] ?>">
                            <br>
                            <label class="mini-table" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block">
                                Last name:
                            </label>
                            <input type="text" name="last_name" style="border: 1px solid #001F3F; border-radius: 5px; display: inline-block; padding: 3px;" value="<?= $info['lname'] ?>">
                            <br>
                            <input class="edit_entry_button" type="submit" value="Submit" style="height: 20px; padding: 2px; verticle-align: center; margin-top: 3px; float: left;">
                        </form>
                    </td>
                </tr>
                <?php
            }
            ?>

            <!-- Edit username -->
            <tr class="edit_users">
                <th class="edit_users" style="border-bottom: none; text-align: left;">
                    Username:	
                </th>
                <td class="edit_users">
                    <?php
                    echo $info["uname"];
                    ?>
                </td>
                <td class="edit_users">
                    <?php
                    if ($adminpage && !$isowner) {
                        echo " Can't change";
                    } else if ($current_content != "user_name") {
                        ?>
                        <a href="?user_id=<?php echo $user_id; ?>&edit&content=user_name">Edit</a>
                        <?php
                    } else {
                        ?>
                        <a href="?user_id=<?php echo $user_id; ?>&edit">Cancel</a>
                        <?php
                    }
                    ?>
                </td>
            </tr>
            <?php
            if ($current_content == "user_name" && ($isowner || $admin)) {
                ?>
                <tr class="mini-table">
                    <td class="mini-table" colspan="2">
                        <form action="user.php?user_id=<?php echo $user_id; ?>&edit" method="POST">
                            <label class="mini-table" style="width: 100px;font-size: 14px; font-style: normal; text-align: left; display: inline-block"> 
                                New username:
                            </label>
                            <input type="text" name="user_name" pattern=".{3,50}" required title="The username must be 3-50 characters long" style="border: 1px solid #001F3F; border-radius: 5px; padding: 3px; display: inline-block" value="<?= $info['uname'] ?>"></input>
                            <?php
                            if ($isowner) {
                                $conf_msg = 'Do you really want to change your username?';
                            } else {
                                $conf_msg = 'This is not your account! Do you still want to change the username?';
                            }
                            ?>
                            <br>
                            <input class="edit_entry_button" type="submit" value="Submit" onclick="confirmAction(event, '<?= $conf_msg ?>')" style="height: 20px; padding: 2px; verticle-align: center; float: left">
                        </form>
                    </td>
                </tr>
                <?php
            }
            ?>

            <!-- Edit email -->
            <tr class="edit_users">
                <th class="edit_users" style="border-bottom: none; text-align: left;"> 
                    Email-address:
                </th>
                <td class="edit_users">
                    <?php echo $info["email"]; ?>
                </td>
                <td class="edit_users">
                    <?php
                    if ($current_content != "email") {
                        ?>
                        <a href="?user_id=<?php echo $user_id; ?>&edit&content=email">Edit</a>
                        <?php
                    } else {
                        ?>
                        <a href="?user_id=<?php echo $user_id; ?>&edit">Cancel</a>
                        <?php
                    }
                    ?>
                </td>
            </tr>
            <?php
            if ($current_content == "email") {
                ?>
                <tr class="mini-table">
                    <td class="mini-table" colspan="2">
                        <form action="user.php?user_id=<?php echo $user_id; ?>&edit" method="POST">
                            <label class="mini-table" style="width: 100px;font-size: 14px; font-style: normal; text-align: left; display: inline-block">
                                New email:
                            </label>
                            <input type="email" name="email" required style="border: 1px solid #001F3F; border-radius: 5px; display: inline-block; width: 250px; padding: 3px;" value="<?= $info['email'] ?>"> 
                            <br>
                            <input class="edit_entry_button" type="submit" value="Submit" style="height: 20px; padding: 2px; verticle-align: center; margin-top: 3px; float:left">
                        </form>
                    </td>
                </tr>
                <?php
            }
            ?>

            <!-- Edit phone number -->
            <tr class="edit_users">
                <th class="edit_users" style="border-bottom: none; text-align: left;">
                    Phone number:
                </th>
                <td class="edit_users">
                    <?php echo $info["phone"]; ?>
                </td>
                <?php
                if ($current_content != "phone") {
                    if ($info["phone"] != "") {
                        ?>
                        <td class="edit_users">
                            <a href="?user_id=<?php echo $user_id; ?>&edit&content=phone">Edit</a>
                        </td>
                        <td class="edit_users">
                            <form action="user.php?user_id=<?php echo $user_id; ?>&edit" method="POST">
                                <input type="hidden" name="remove_phone"></input>
                                <input class="edit_entry_button" type="submit" value="Remove" style="height: 20px; padding: 2px; verticle-align: center; margin-top: 2px;"></input>
                            </form>
                        </td>
                        <?php
                    } else {
                        ?>
                        <td class = "edit_users">
                            <a href = "?user_id=<?php echo $user_id; ?>&edit&content=phone">Add</a>
                        </td>
                        <?php
                    }
                } else {
                    ?>
                    <td class="edit_users">
                        <a href="?user_id=<?php echo $user_id; ?>&edit">Cancel</a>
                    </td>
                    <?php
                }
                ?>
            </tr>
            <?php
            if ($current_content == "phone") {
                ?>
                <tr class="mini-table">
                    <td class="mini-table" colspan="2">
                        <form action="user.php?user_id=<?php echo $user_id; ?>&edit" method="POST">
                            <label class="mini-table" style="width: 100px;font-size: 14px; font-style: normal; text-align: left; display: inline-block">New number:</label>
                            <input type="text" name="phone" style="border: 1px solid #001F3F; border-radius: 5px; padding: 3px; display: inline-block" value="<?= $info['phone'] ?>">
                            <br>
                            <input class="edit_entry_button" type="submit" value="Submit" style="height: 20px; padding: 2px; verticle-align: center; margin-top: 3px; float: left">
                        </form>
                    </td>
                </tr>
                <?php
            }
            ?>

            <?php
            if ($isowner) {
                // Change password
                ?>
                <tr class="edit_users">
                    <td class="edit_users">
                        <?php if ($current_content != "password") {
                            ?>
                            <a href="?user_id=<?php echo $user_id; ?>&edit&content=password">Change password</a>
                            <?php
                        } else {
                            ?>
                            <a href="?user_id=<?php echo $user_id; ?>&edit">Cancel</a>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <?php if ($current_content == "password") { ?>
                    <tr class="mini-table">
                        <td class="mini-table" colspan="2">
                            <form action="user.php?user_id=<?php echo $user_id; ?>&edit" method="POST">
                                <label class="mini-table" style="width: 100px;font-size: 14px; font-style: normal; text-align: left; display: inline-block">
                                    Old password:
                                </label>
                                <input type="password" name="old_password" required style="border: 1px solid #001F3F; border-radius: 5px; padding: 3px; display: inline-block" autocomplete="off">
                                <br>
                                <label class="mini-table" style="width: 100px;font-size: 14px; font-style: normal; text-align: left; display: inline-block">
                                    New password:
                                </label>
                                <input type="password" name="new_password" required pattern=".{8,}" title="The password must be at least 8 characters" style="border: 1px solid #001F3F; border-radius: 5px; padding: 3px; display: inline-block" autocomplete="off">
                                <br>
                                <label class="mini-table" style="width: 100px;font-size: 14px; font-style: normal; text-align: left; display: inline-block">
                                    Confirm:
                                </label>
                                <input type="password" name="conf_password" required pattern=".{8,}" title="The password must be at least 8 characters" style="border: 1px solid #001F3F; border-radius: 5px; padding: 3px; display: inline-block" autocomplete="off">
                                <br>
                                <input class="edit_entry_button" type="submit" value="Submit" style="height: 20px; padding: 2px; verticle-align: center; margin-top: 3px; float: left">
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </td>
                </tr>
                <?php
            }
            ?>

        </table>

        <div class="clear"></div>
        <!-- Show success/error message -->
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($update_msg)): echo "<br>" . $update_msg;
        endif;
        ?>
        <!-- Back button -->
        <div class="back" style="margin-top: 50px;"><a href="?user_id=<?php echo $user_id; ?>">Back to user page</a></div>

        <?php
// Hides page if the user is not logged in or activated
    } else {
        if (!$loggedin) {
            ?>
            <h3 style="color:red">Access denied (you are not logged in).</h3>
            <?php
        } else if (!$active) {
            ?>
            <h3 style="color:red">Access denied (your account is not activated yet).</h3>
            <?php
        } else {
            ?>
            <h3 style="color:red">You are not allowed to edit this profile (you are not the owner or an admin).</h3>
            <?php
        }
        ?>
        <br>
        <a href="javascript:history.go(-1)">Go back</a>
        <?php
    }
    ?>
</div>

<script>
    function confirmAction(event, msg) {
        if !confirm(msg) {
            event.preventDefault();
        }
    }
</script>