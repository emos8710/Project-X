<?php
if (count(get_included_files()) == 1)
    exit("Access restricted");

include 'scripts/db.php';

$id = mysqli_real_escape_string($link, test_input($_GET['id']));

$current_info_sql = "SELECT ins.name AS name, ins.ins_reg AS biobrick, ins.comment AS cmt, ins.date_db AS date, "
        . "ins_type.name AS type, users.user_id AS uid, users.first_name AS fname, users.last_name AS lname "
        . "FROM ins "
        . "LEFT JOIN ins_type ON ins.type = ins_type.id "
        . "LEFT JOIN users ON ins.creator = users.user_id "
        . "WHERE ins.id = " . $id;
$current_info_query = mysqli_query($link, $current_info_sql);
$old_info_sql = "SELECT FROM_UNIXTIME(ins_log.time) AS time, ins_log.event_type AS etype, ins_log.date_db AS date, "
        . "ins_log.name AS name, ins_log.old_data_id AS oid, ins_log.id AS id, ins_log.comment AS cmt, ins_log.ins_reg AS biobrick, "
        . "users.user_id AS uid, users.first_name AS fname, users.last_name AS lname, ins_type.name AS itype "
        . "FROM ins_log "
        . "LEFT JOIN ins_type ON ins_log.type = ins_type.id "
        . "LEFT JOIN users ON ins_log.creator = users.user_id "
        . "WHERE ins_log.id = " . $id;
$old_info_query = mysqli_query($link, $old_info_sql);

$is_deleted = (mysqli_num_rows($current_info_query) < 1);
$has_history = (mysqli_num_rows($old_info_query) >= 1);

mysqli_close($link) or die("Could not close connection to database");
?>

<p>
<h3>Insert <?php echo $id; ?> info history</h3>
<em>Logged history is automatically removed after 30 days.</em>
</p>

<table class="control-panel-history">
    <col><col><col><col><col><col><col>
    <tr>
        <th class="top" colspan="6">Current data</th>
    </tr>
    <?php
    if ($is_deleted) {
        ?>
        <tr>
            <td colspan="6"><strong>No current data (insert has been removed).</strong></td>
        </tr>
        <?php
    } else {
        $data = mysqli_fetch_assoc($current_info_query);
        ?>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>iGEM registry</th>
            <th>Added by</th>
            <th>Date added</th>
            <th>Comment</th>
        </tr>
        <tr>
            <td><?php echo $data['name']; ?></td>
            <td><?php echo $data['type']; ?></td>
            <td><?php echo $data['biobrick']; ?></td>
            <td><a href="user.php?user_id=<?php echo $data['uid']; ?>"><?php echo $data['fname'] . " " . $data['lname']; ?></a></td>
            <td><?php echo $data['date']; ?></td>
            <td><?php echo $data['cmt']; ?></td>
        </tr>
        <?php
    }
    ?>
    <tr>
        <th class="top" colspan="7">Old data</th>
    </tr>
    <?php
    if ($has_history) {
        ?>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>iGEM registry</th>
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
                <td><?php echo $row['itype']; ?></td>
                <td><?php echo $row['biobrick'] ?></td>
                <td><?php echo $row['cmt']; ?></td>
                <td><?php echo $row['etype']; ?></td>
                <td><?php echo $row['time']; ?></td>
                <td>
                    <form class="control-panel" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
                        <input type="hidden" name="restore_data" value="<?php echo $row['oid']; ?>">
                        <input type="hidden" name="restore_insert" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="header" value="refresh">
                        <button type="submit" class="control-panel-restore" title="Restore" onclick="confirmAction(event, 'Restore insert <?php echo $row['id']; ?> to this record?')"/>
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