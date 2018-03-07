<?php
if (count(get_included_files()) == 1)
    exit("Access restricted");

include 'scripts/db.php';
$restore_id = mysqli_real_escape_string($link, $_POST['restore_data']);
$user_id = mysqli_real_escape_string($link, $_POST['restore_user']);

$check_exists = mysqli_query($link, "SELECT * from users WHERE user_id = " . $user_id);

if (mysqli_num_rows($check_exists) < 1) {
    $restore_sql = "INSERT INTO users(active, admin, email, first_name, hash, last_name, password, phone, username, user_id) "
            . "SELECT active, admin, email, first_name, hash, last_name, password, phone, username, user_id FROM users_log "
            . "WHERE old_data_id = " . $restore_id . ";";
} else {
    $old_data_sql = "SELECT email, first_name, last_name, phone, username "
            . "FROM users_log WHERE old_data_id = " . $restore_id . ";";

    $old_data = mysqli_fetch_assoc(mysqli_query($link, $old_data_sql));
    $restore_sql = "UPDATE users SET email = '" . $old_data['email'] . "', first_name = '" . $old_data['first_name'] . "', last_name = '" . $old_data['last_name']
            . "', phone = '" . $old_data['phone'] . "', username = '" . $old_data['username'] . "' WHERE user_id = " . $user_id . ";";
}

$restore_query = mysqli_query($link, $restore_sql);
?>
<p>
    <?php
    if (!$restore_query) {
        ?>
        <strong style="color:red">Database error: <?php echo mysqli_error($link); ?></strong>
        <?php
    } else {
        ?>
        <strong style="color:green">User info successfully restored!</strong>
        <?php
    }
    mysqli_close($link) or die("Could not close database connection");
    ?>
    <br>
    Reloading in 10 seconds... <a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Reload now</a>
</p>
