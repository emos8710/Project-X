<?php
if (count(get_included_files()) == 1)
    exit("Access restricted");

$current_url = "control_panel.php?content=manage_users";
?>

<h3>Manage users</h3>

<?php
// Perform form requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['history'])) {
    ?>
    <p>
        <?php
        include 'scripts/db.php';

        $delete = FALSE;
        $make_admin = FALSE;
        if (isset($_POST['delete'])) {
            $user_id = $_POST['delete'];
            $id = mysqli_real_escape_string($link, $user_id);
            $delete = TRUE;
        } else if (isset($_POST['admin'])) {
            $user_id = $_POST['admin'];
            $id = mysqli_real_escape_string($link, $user_id);
            $make_admin = TRUE;
        } else {
            echo "This should never happen";
        }

        $check_admin_sql = "SELECT admin, active from users WHERE user_id = " . $id;
        $check_admin_query = mysqli_query($link, $check_admin_sql) or die("MySQL error: " . mysqli_error($link));
        $is_admin = (mysqli_fetch_array($check_admin_query)[0] == '1');
        $is_active = (mysqli_fetch_array($check_admin_query)[1] == '1');

        if ($delete) {

            if ($user_id == $_SESSION['user_id']) {
                $delete_msg = "<strong style=\"color:red\">You cannot remove yourself!</strong>";
            } else if ($is_admin) {
                $delete_msg = "<strong style=\"color:red\">You cannot remove an admin!</strong>";
            } else {
                $deletesql = "DELETE FROM users WHERE user_id = " . $id;
                if (!$deletequery = mysqli_query($link, $deletesql)): $delete_msg = "<strong style=\"color:red\">Database error: Cannot remove user (user probably has entries).</strong>";
                else: $delete_msg = "<strong style=\"color:green\">User successfully deleted!</strong>";
                endif;
            }
            echo $delete_msg;
        }

        if ($make_admin) {

            if ($user_id == $_SESSION['user_id']) {
                ?>
                <strong style="color:red">You are already an admin!</strong>
                <?php
            } else if ($is_admin) {
                $admin_msg = "<strong style=\"color:red\">User is already an admin!</strong>";
            } else if (!$is_active) {
                $admin_msg = "<strong style=\"color:red\">User is not activated!</strong>";
            } else {
                $adminsql = "UPDATE users SET admin='1' WHERE user_id = " . $id;
                $adminquery = mysqli_query($link, $adminsql);
                $admin_msg = "<strong style=\"color:green\">User " . $user_id . "is now an admin!</strong>";
            }
            echo $admin_msg;
        }

        mysqli_close($link) or die("Could not close connection to database");
        ?>
        <br>
        Reloading in 10 seconds... <a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Reload now</a>
        <?php
        header("Refresh: 10; url=" . $_SERVER['REQUEST_URI']);
        ?>
    </p>
    <?php
}
?>


<p>
    <?php if (mysqli_num_rows($userquery) < 1) {
        ?>
        <strong>No users to show</strong>
        <?php
    } else {
        ?>

    <table class="control-panel-users">
        <col><col><col><col><col><col><col><col><col><col><col>
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Name</th>
            <th>E-mail address</th>
            <th>Phone number</th>
            <th>User level</th>
            <th colspan="4">Actions</th>
        </tr>

        <?php
        while ($user = mysqli_fetch_assoc($userquery)) {
            ?>
            <tr>
                <td><a href="user.php?user_id=<?php echo $user['user_id']; ?>"><?php echo $user['user_id']; ?></a></td>
                <td><?php echo $user['username']; ?></td>
                <td><?php echo $user['first_name'] . " " . $user['last_name']; ?></td>
                <td><?php echo $user['email']; ?></td>
                <td><?php echo $user['phone']; ?></td>
                <td><?php
                    if ($user['admin'] == 1): echo "Admin";
                    else: echo "User";
                    endif;
                    ?></td>
                <td>
                    <form class="control-panel" action="user.php" method="GET">
                        <input type="hidden" name="user_id" value="<?php echo "" . $user['user_id'] . ""; ?>">
                        <input type="hidden" name="edit">
                        <button class="control-panel-edit" title="Edit user" type="submit"/>
                    </form>
                </td>
                <td>
                    <form class="control-panel" action="<?php echo $current_url; ?>&history=user" method="POST">
                        <input type="hidden" name="history" value="<?php echo $user['user_id']; ?>">
                        <button type="submit" class="control-panel-history" title="View user info history"/>
                    </form>
                </td>
                <td>
                    <form class="control-panel" action="<?php echo $current_url; ?>" method="POST">
                        <input type="hidden" name="admin" value="<?php echo $user['user_id']; ?>">
                        <button type="submit" class="control-panel-admin" title="Make admin" onclick="confirmAction(event, 'Really want to make this user admin?')"/>
                    </form>
                </td>
                <td>
                    <form class="control-panel" action="<?php echo $current_url; ?>" method="POST">
                        <input type="hidden" name="delete" value="<?php echo $user['user_id']; ?>">
                        <button type="submit" class="control-panel-delete" title="Delete user" onclick="confirmAction(event, 'Really want to delete this user?')"/>
                    </form>
                </td>
            </tr>
            <?php
        }
    }
    ?>
</table>
</p>

