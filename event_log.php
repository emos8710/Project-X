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

<h3 class="event-log" style="font-style: normal; font-weight: 300; color: #001F3F;">Event log</h3>

<?php
if (mysqli_num_rows($logquery) < 1) {
    ?>
    <strong>No events logged.</strong>
    <?php
} else {
    ?>
    <table class="display" id="eventlog">
        <thead>
            <tr>
                <th>Timestamp</th>
                <th>Event type</th>
                <th>Object ID</th>
                <th>Object type</th>
                <th>History</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($log = mysqli_fetch_assoc($logquery)) {
                if ($log['object'] === "Entry" || $log['object'] === "Entry-insert link") {
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
                        <a href="control_panel.php?content=event_log&history=<?php
                        if ($log['object'] != "Entry-insert link"): echo strtolower($log['object']);
                        else: echo "entry";
                        endif;
                        ?>&id=<?=$id?>">View detailed history</a>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
        <tfoot></tfoot>
    </table>
    <?php
}
?>