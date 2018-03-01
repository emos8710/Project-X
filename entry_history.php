<?php
if (count(get_included_files()) == 1)
    exit("Access restricted");

include 'scripts/db.php';

$id = mysqli_real_escape_string($link, $_GET['id']);

$current_info_sql = "SELECT entry.id AS eid, entry.comment AS cmt, entry.year_created AS year, entry.date_db AS date, "
        . "entry.entry_reg AS biobrick, entry_upstrain.upstrain_id AS uid, backbone.name AS bname, "
        . "strain.name AS sname, users.user_id AS usid, users.username AS usname, users.first_name AS fname, users.last_name AS lname FROM entry "
        . "LEFT JOIN entry_upstrain ON entry_upstrain.entry_id = entry.id "
        . "LEFT JOIN backbone ON entry.backbone = backbone.id "
        . "LEFT JOIN strain ON entry.strain = strain.id "
        . "LEFT JOIN users ON entry.creator = users.user_id "
        . "WHERE entry.id = " . $id;
$current_info_query = mysqli_query($link, $current_info_sql);

$old_info_sql = "SELECT FROM_UNIXTIME(entry_log.time) AS time, entry_log.type, entry_log.id AS eid, entry_log.comment AS cmt, entry_log.year_created AS year, entry_log.date_db AS date, "
        . "entry_log.entry_reg AS biobrick, backbone.name AS bname, "
        . "strain.name AS sname, users.first_name AS fname, users.last_name AS lname FROM entry_log "
        . "LEFT JOIN backbone ON entry_log.backbone = backbone.id "
        . "LEFT JOIN strain ON entry_log.strain = strain.id "
        . "LEFT JOIN users ON entry_log.creator = users.user_id "
        . "WHERE entry_log.id = " . $id;
$old_info_query = mysqli_query($link, $old_info_sql);

$is_deleted = (mysqli_num_rows($current_info_query) < 1);
$has_history = (mysqli_num_rows($old_info_query) >= 1);

mysqli_close($link) or die("Could not close connection to database");
?>

<h3>Entry <?php echo $id ?> info history</h3>
<em>Logged history is automatically removed after 30 days.</em>

<table class="control-panel-history">
    <col><col><col><col><col><col><col><col>
    <tr>
        <th class="top" colspan="8">Current data</th>
    </tr>
    <?php
    if ($is_deleted) {
        ?>
        <tr>
            <td class="top" colspan="8"><strong>No active data (entry has been removed).</strong></td>
        </tr>
        <?php
    } else {
        $data = mysqli_fetch_assoc($current_info_query);
        ?>
        <tr>
            <th>Entry ID</th>
            <th>Comment</th>
            <th>Year created</th>
            <th>Date added</th>
            <th>iGEM Registry ID</th>
            <th>Backbone</th>
            <th>Strain</th>
            <th>Created by</th>
        </tr>
        <tr>
            <td><?php echo $data['eid']; ?></td>
            <td><?php echo $data['cmt']; ?></td>
            <td><?php echo $data['year']; ?></td>
            <td><?php echo $data['date']; ?></td>
            <td><?php echo $data['biobrick']; ?></td>
            <td><?php echo $data['bname']; ?></td>
            <td><?php echo $data['sname']; ?></td>
            <td><?php echo $data['fname'] . " " . $data['lname']; ?></td>
        </tr>
        <?php
    }
    ?>
    <tr>
        <th class="top" colspan="8">Old data</th>
    </tr>
    <?php
    if ($has_history) {
        ?>
        <tr>
            <th>Time recorded</th>
            <th>Event type</th>
            <th>Username</th>
            <th>Name</th>
            <th>E-mail address</th>
            <th>Phone number</th>
            <th>Admin</th>
        </tr>
        <?php
        while ($data = mysqli_fetch_assoc($old_info_query)) {
            ?>
            <tr>
                <td><?php echo $data['time']; ?></td>
                <td><?php echo $data['type']; ?></td>
                <td><?php echo $data['year']; ?></td>
                <td><?php echo $data['date']; ?></td>
                <td><?php echo $data['biobrick']; ?></td>
                <td><?php echo $data['bname']; ?></td>
                <td><?php echo $data['sname']; ?></td>
                <td><?php echo $data['fname'] . " " . $data['lname']; ?></td>
            </tr>
            <?php
        }
    } else {
        ?>
        <tr>
            <td colspan="8"><strong>No old data recorded.</strong></td>
        </tr>
        <?php
    }
    ?>
</table>