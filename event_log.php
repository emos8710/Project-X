<?php
if (count(get_included_files()) == 1)
    exit("Access restricted");

$current_url = "control_panel.php?content=event_log";

// Connect to database
include 'scripts/db.php';

// Fetch event log
$logsql = "SELECT * from event_log ORDER by time DESC";
$logquery = mysqli_query($link, $logsql) or die("MySQL error: " . mysqli_error($link));

mysqli_close($link) or die("Could not close database connection");
?>

<h3 class="event-log">Event log</h3>

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
            if ($log['object'] === "Entry") {
                $id = $log['object_id'];
            } else {
                $id = ltrim($log['object_id'], '0');
            }
            ?>
            <tr>
                <td><?php echo $log['time']; ?></td>
                <td><?php echo $log['type']; ?></td>
                <td><?php echo $id; ?></td>
                <td><?php echo $log['object']; ?></td>
                <td>
                    <form class="control-panel" action="<?php echo $current_url; ?>" method="GET">
                        <input type="hidden" name="content" value="event_log">
                        <input type="hidden" name="history" value="<?php echo strtolower($log['object']); ?>">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <button class="control-panel-history" title="View detailed history" type="submit"/>
                    </form>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php
}
?>
