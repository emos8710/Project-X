<?php
if (count(get_included_files()) == 1)
    exit("Access restricted");

include 'scripts/db.php';

$id = mysqli_real_escape_string($link, $_POST['history']);

$current_info_sql = "SELECT username, first_name, last_name, email, phone, admin FROM users WHERE user_id = " . $id;
$current_info_query = mysqli_query($link, $current_info_sql);
$old_info_sql = "SELECT old_data_id AS id, user_id AS uid, username, first_name, last_name, email, phone, admin, type, FROM_UNIXTIME(time) AS time FROM users_log WHERE user_id = " . $id . " ORDER BY time DESC";
$old_info_query = mysqli_query($link, $old_info_sql);

$is_deleted = (mysqli_num_rows($current_info_query) < 1);
$has_history = (mysqli_num_rows($old_info_query) >= 1);

mysqli_close($link) or die("Could not close connection to database");
?>

<h3>User <?php echo $id; ?> info history</h3>
<em>Logged history is automatically removed after 30 days.</em>

<table class="control-panel-history">
    <col><col><col><col><col><col><col><col>
    <tr>
        <th class="top" colspan="7">Current data</th>
    </tr>
    <tr>
        <?php
        if ($is_deleted) {
            ?>
            <td colspan="7"><strong>No active data (user has been removed).</strong></td>
            <?php
        } else {
            $data = mysqli_fetch_assoc($current_info_query);
            ?>
            <th>Username</th>
            <th>Name</th>
            <th>E-mail address</th>
            <th>Phone number</th>
            <th>Admin</th>
        </tr>
        <tr>
            <td><?php echo $data['username']; ?></td>
            <td><?php echo $data['first_name'] . " " . $data['last_name']; ?></td>
            <td><?php echo $data['email']; ?></td>
            <td><?php echo $data['phone']; ?></td>
            <td><?php
                if ($data['admin'] == '1'): echo "Yes";
                else: echo "No";
                endif;
                ?></td>
            <?php
        }
        ?>
    </tr>
    <tr>
        <th class="top" colspan="7">Old data</th>
    </tr>
    <?php
    if ($has_history) {
        ?>
        <tr>
            <th>Event type</th>
            <th>Username</th>
            <th>Name</th>
            <th>E-mail address</th>
            <th>Phone number</th>
            <th>Time recorded</th>
            <th>Restore</th>
        </tr>
        <?php
        while ($data = mysqli_fetch_assoc($old_info_query)) {
            ?>
            <tr>
                <td><?php echo $data['type']; ?></td>
                <td><?php echo $data['username']; ?></td>
                <td><?php echo $data['first_name'] . " " . $data['last_name']; ?></td>
                <td><?php echo $data['email']; ?></td>
                <td><?php echo $data['phone']; ?></td>
                <td><?php echo $data['time']; ?></td>
                <td>
                    <form class="control-panel" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
                        <input type="hidden" name="restore_data" value="<?php echo $data['id']; ?>">
                        <input type="hidden" name="restore_user" value="<?php echo $data['uid']; ?>">
                        <input type="hidden" name="history" value="<?php echo $id; ?>"> 
                        <input type="hidden" name="header" value="refresh">
                        <button type="submit" class="control-panel-restore" title="Restore" onclick="confirmAction(event, 'Restore user <?php echo $data['uid']; ?> to this record?')"/>
                    </form>
                </td>
            </tr>
            <?php
        }
    } else {
        ?>
        <tr>
            <td colspan="7"><strong>No old data recorded.</strong></td>
        </tr>
        <?php
    }
    ?>
</table>