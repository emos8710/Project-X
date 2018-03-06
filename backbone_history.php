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
$old_info_sql = "SELECT old_data_id AS id, FROM_UNIXTIME(time) AS time, type, "
        . "id, name, comment AS cmt, date_db AS date, private, Bb_reg AS biobrick "
        . "FROM backbone_log "
        . "WHERE id = " . $id
        . " ORDER by time DESC";
$old_info_query = mysqli_query($link, $old_info_sql);

$is_deleted = (mysqli_num_rows($current_info_query) < 1);
$has_history = (mysqli_num_rows($old_info_query) >= 1);

mysqli_close($link) or die("Could not close connection to database");
?>

<h3>Backbone <?php echo $id; ?> info hsitory</h3>
<em>Logged history is automatically removed after 30 days.</em>

<table class="control-panel-history">

    <tr>
        <th class="top" colspan="">Current data</th>
    </tr>
    <?php
    if ($is_deleted) {
        ?>
        <tr>
            <td colspan="">No active data (backbone has been removed).</td>
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
            <td><?php echo $data['biobrick']; ?></td>
            <td><a href="user.php?user_id=<?php echo $data['uid']; ?>"><?php echo $data['fname'] . " " . $data['lname'] ?></a></td>
            <td><?php echo $data['cmt']; ?></td>
            <td><?php echo $data['private']; ?></td>
        </tr>
        <?php
    }
    ?>
    <tr>
        <th class="top" colspan="">Old data</th>
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
    } else {
        ?>
        <td colspan=""><strong>No old data recorded.</strong></td>
        <?php
    }
    ?>
</table>