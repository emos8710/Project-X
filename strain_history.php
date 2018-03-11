<?php
if (count(get_included_files()) == 1)
    exit("Access restricted");

include 'scripts/db.php';

$id = mysqli_real_escape_string($link, test_input($_GET['id']));

$current_info_sql = "SELECT strain.id AS sid, strain.name AS name, strain.comment AS cmt, "
        . "strain.private AS private, strain.date_db AS date, users.user_id AS uid, "
        . "users.first_name AS fname, users.last_name AS lname "
        . "FROM strain "
        . "LEFT JOIN users ON creator = users.user_id "
        . "WHERE strain.id = " . $id;
$current_info_query = mysqli_query($link, $current_info_sql);
$old_info_sql = "SELECT old_data_id AS oid, id AS sid, FROM_UNIXTIME(time) AS time, type, name, "
        . "comment AS cmt, private "
        . "FROM strain_log "
        . "WHERE id = " . $id;
$old_info_query = mysqli_query($link, $old_info_sql);

$is_deleted = (mysqli_num_rows($current_info_query) < 1);
$has_history = (mysqli_num_rows($old_info_query) >= 1);

mysqli_close($link) or die("Could not close connection to database");
?>
<p>
<h3>Strain <?php echo $id; ?> info history</h3>
<em>Logged history is automatically removed after 30 days.</em>
</p>

<table class="control-panel-history">
    <col><col><col><col><col><col>
    <tr>
        <th class="top" colspan="5">Current data</th>
    </tr>
    <?php
    if ($is_deleted) {
        ?>
        <tr>
            <td colspan="5"><strong>No active data (user has been removed).</strong></td>
        </tr>
        <?php
    } else {
        $data = mysqli_fetch_assoc($current_info_query);
        ?>
        <tr>
            <th>Name</th>
            <th>Private</th>
            <th>Added by</th>
            <th>Date added</th>
            <th>Comment</th>
        </tr>
        <tr>
            <td><?php echo $data['name']; ?></td>
            <td><?php
                if ($data['private'] == 1): echo "Yes";
                else: echo "No";
                endif;
                ?></td>
            <td><a href="user.php?user_id=<?php echo $data['uid']; ?>"><?php echo $data['fname'] . " " . $data['lname']; ?></a></td>
            <td><?php echo $data['date']; ?></td>
            <td><?php echo $data['cmt']; ?></td>
        </tr>
        <?php
    }
    ?>
    <tr>
        <th class="top" colspan="6">Old data</th>
    </tr>
    <?php
    if ($has_history) {
        ?>
        <tr>
            <th>Name</th>
            <th>Private</th>
            <th>Comment</th>
            <th>Event type</th>
            <th>Time recorded</th>
            <th>Restore</th>
        </tr>
        <?php
        while ($row = mysqli_fetch_assoc($old_info_query)) {
            ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['private']; ?></td>
                <td><?php echo $row['cmt']; ?></td>
                <td><?php echo $row['type']; ?></td>
                <td><?php echo $row['time']; ?></td>
                <td>
                    <form class="control-panel" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
                        <input type="hidden" name="restore_data" value="<?php echo $row['oid']; ?>">
                        <input type="hidden" name="restore_strain" value="<?php echo $row['sid']; ?>">
                        <input type="hidden" name="header" value="refresh">
                        <button type="submit" class="control-panel-restore" title="Restore" onclick="confirmAction(event, 'Restore strain <?php echo $row['sid']; ?> to this record?')"/>
                    </form>
                </td>
            </tr>
            <?php
        }
    } else {
        ?>
        <tr>
            <td colspan="6"><strong>No old data recorded.</strong></td>
        </tr>
        <?php
    }
    ?>
</table>
