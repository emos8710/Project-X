<?php
if (count(get_included_files()) == 1)
    exit("Access rstricted");

/* Database things */
include 'scripts/db.php';

$id = mysqli_real_escape_string($link, $_GET['id']);

$current_info_sql = "SELECT * from ins_type WHERE id = " . $id;
$current_info_query = mysqli_query($link, $current_info_sql);

$old_info_sql = "SELECT old_data_id AS oid, id, name, type, FROM_UNIXTIME(time) AS time "
        . "FROM ins_type_log WHERE id = " . $id;
$old_info_query = mysqli_query($link, $old_info_sql);

$is_deleted = (mysqli_num_rows($current_info_query) < 1);
$has_history = (mysqli_num_rows($old_info_query) >= 1);

mysqli_close($link) or die("Could not close database connection");
?>

<p>
<h3>Insert type <?php echo $id ?> info history</h3>
<em>Logged history is automatically removed after 30 days.</em>
</p>

<table class="control-panel-history">
    <col><col><col><col>
    <tr>
        <th class="top" colspan="4">Current data</th>
    </tr>
    <?php
    if ($is_deleted) {
        ?>
        <tr>
            <td class="top" colspan="4"><strong>No active data (type has been removed).</strong></td>
        </tr>
        <?php
    } else {
        $data = mysqli_fetch_assoc($current_info_query);
        ?>
        <tr>
            <th>ID</th>
            <th>Name</th>
        </tr>
        <tr>
            <td><?php echo $data['id']; ?></td>
            <td><?php echo $data['name']; ?></td>
        </tr>
        <?php
    }
    ?>
    <tr>
        <th class="top" colspan="4">Old data</th>
    </tr>
    <?php
    if ($has_history) {
        ?>
        <tr>
            <th>Name</th>
            <th>Event type</th>
            <th>Time recorded</th>
            <th>Restore</th>
        </tr>
        <?php
        while ($data = mysqli_fetch_assoc($old_info_query)) {
            ?>
            <tr>
                <td><?php echo $data['name']; ?></td>
                <td><?php echo $data['type']; ?></td>
                <td><?php echo $data['time']; ?></td>
                <td>
                    <form class="control-panel" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
                        <input type="hidden" name="restore_data" value="<?php echo $data['oid']; ?>">
                        <input type="hidden" name="restore_instype" value="<?php echo $data['id']; ?>">
                        <input type="hidden" name="header" value="refresh">
                        <button type="submit" class="control-panel-restore" title="Restore" onclick="confirmAction(event, 'Restore insert type <?php echo $data['id']; ?> to this record?')"/>
                    </form>
                </td>
            </tr>
            <?php
        }
    } else {
        ?>
        <tr>
            <td colspan="4"><strong>No old data recorded.</strong></td>
        </tr>
        <?php
    }
    ?>
</table>
