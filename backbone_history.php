<?php
if (count(get_included_files()) == 1)
    exit("Access restricted");

include 'db.php';

$id = mysqli_real_escape_string($link, $_GET['id']);

$current_info_sql = "SELECT id, name, comment AS cmt, "
        . "date_db AS date, private, Bb_reg AS biobrick, "
        . "users.user_id AS uid, users.first_name AS fname, "
        . "users.last_name AS lname "
        . "FROM backbone "
        . "LEFT JOIN users ON backbone.creator = users.user_id "
        . "WHERE id = " . $id;
$current_info_query = mysqli_query($link, $current_info_sql);
$old_info_sql = "SELECT old_data_id AS oid, FROM_UNIXTIME(time) AS time, type, "
        . "id, name, comment AS cmt, date_db AS date, private, Bb_reg AS biobrick "
        . "FROM backbone_log "
        . "WHERE id = " . $id
        . " ORDER by time DESC";
$old_info_query = mysqli_query($link, $old_info_sql);

$is_deleted = (mysqli_num_rows($current_info_query) < 1);
$has_history = (mysqli_num_rows($old_info_query) >= 1);

mysqli_close($link) or die("Could not close connection to database");
?>

<h3>Backbone <?php echo $id; ?> info history</h3>
<em>Logged history is automatically removed after 30 days.</em>

<table class="control-panel-history">

    <tr>
        <th class="top" colspan="5">Current data</th>
    </tr>
    <?php
    if ($is_deleted) {
        ?>
        <tr>
            <td colspan="5">No active data (backbone has been removed).</td>
        </tr>
        <?php
    } else {
        $data = mysqli_fetch_assoc($current_info_query);
        ?>
        <tr>
            <th>Name</th>
            <th>iGEM registry</th>
            <th>Added by</th>
            <th>Comment</th>
            <th>Private</th>
        </tr>
        <tr>
            <td><?php echo $data['name']; ?></td>
            <td><a class="external" href="http://parts.igem.org/Part:<?php echo $data['biobrick']; ?>" target="_blank"><?php echo $data['biobrick']; ?></a></td>
            <td><a href="user.php?user_id=<?php echo $data['uid']; ?>"><?php echo $data['fname'] . " " . $data['lname'] ?></a></td>
            <td><?php echo $data['cmt']; ?></td>
            <td><?php echo $data['private']; ?></td>
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
            <th>Event type</th>
            <th>Name</th>
            <th>iGEM registry</th>
            <th>Comment</th>
            <th>Private</th>
            <th>Time recorded</th>
            <th>Restore</th>
        </tr>
        <?php
        while ($data = mysqli_fetch_assoc($old_info_query)) {
            ?>
            <tr>
                <td><?php echo $data['type']; ?></td>
                <td><?php echo $data['name']; ?></td>
                <td><?php echo $data['biobrick']; ?></td>
                <td><?php echo $data['cmt']; ?></td>
                <td><?php echo $data['private']; ?></td>
                <td><?php echo $data['time']; ?></td>
                <td>
                    <form class="control-panel" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
                        <input type="hidden" name="restore_data" value="<?php echo $data['oid']; ?>">
                        <input type="hidden" name="restore_backbone" value="<?php echo $data['id']; ?>">
                        <input type="hidden" name="header" value="refresh">
                        <button class="control-panel-restore" type="submit" title="Restore" onclick="confirmAction(event, 'Restore backbone <?php echo $data['id']; ?> to this record?')"/>
                    </form>
                </td>
            </tr>
            <?php
        }
    } else {
        ?>
        <td colspan="7"><strong>No old data recorded.</strong></td>
        <?php
    }
    ?>
</table>