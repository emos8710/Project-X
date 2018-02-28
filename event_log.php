<?php
if (count(get_included_files()) == 1)
    exit("Access restricted");

$current_url = "control_panel.php?content=event_log";
?>

<h3>Event log</h3>

<?php
if (mysqli_num_rows($logquery) < 1) {
    ?>
    <strong>No events logged.</strong>
    <?php
} else {
    ?>
    <table class="control-panel-log">
        <col><col><col><col><col>
        <tr>
            <th>Timestamp</th>
            <th>Event type</th>
            <th>Object ID</th>
            <th>Object type</th>
        </tr>

        <?php
        while ($log = mysqli_fetch_assoc($logquery)) {
            ?>
            <tr>
                <td><?php echo $log['time']; ?></td>
                <td><?php echo $log['type']; ?></td>
                <td><?php echo $log['object_id']; ?></td>
                <td><?php echo $log['object']; ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php
}
?>
