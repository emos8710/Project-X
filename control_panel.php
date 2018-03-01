<?php
// Start session if session closed
if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set display for history div
$show_history = isset($_GET['history']);
if ($show_history)
    $history_content = $_GET['history'];

// Handle headers
if (isset($_POST['header']) && $_POST['header'] === "refresh") {
    if (isset($current_url)): header("Refresh:10, url=" . $current_url);
    else: header("Refresh:10");
    endif;
}

// Database stuff

include 'scripts/db.php';

// Fetch all users (admins first)
$usersql = "SELECT user_id, username, first_name, last_name, email, phone, admin FROM users ORDER BY admin DESC, user_id ASC";
$userquery = mysqli_query($link, $usersql) or die("MySQL error: " . mysqli_error($link));

// Fetch all entries
$entrysql = "SELECT entry.id AS eid, entry.comment AS cmt, entry.year_created AS year, entry.date_db AS date, "
        . "entry.entry_reg AS biobrick, entry_upstrain.upstrain_id AS uid, backbone.name AS bname, "
        . "strain.name AS sname, ins.name AS iname, users.user_id AS usid, users.username AS usname, users.first_name AS fname, users.last_name AS lname FROM entry "
        . "LEFT JOIN entry_upstrain ON entry_upstrain.entry_id = entry.id "
        . "LEFT JOIN backbone ON entry.backbone = backbone.id "
        . "LEFT JOIN strain ON entry.strain = strain.id "
        . "LEFT JOIN entry_inserts ON entry_inserts.entry_id = entry.id "
        . "LEFT JOIN ins ON entry_inserts.insert_id = ins.id AND entry_inserts.entry_id = entry.id "
        . "LEFT JOIN users ON entry.creator = users.user_id "
        . "ORDER BY eid";
$entryquery = mysqli_query($link, $entrysql) or die("MySQL error: " . mysqli_error($link));

// Fetch event log
$logsql = "SELECT * from event_log ORDER by time DESC";
$logquery = mysqli_query($link, $logsql) or die("MySQL error: " . mysqli_error($link));

mysqli_close($link) or die("Could not close connection to database");

$title = "Control Panel";
?>

<!DOCTYPE html>

<?php include 'top.php'; ?>

<body>

    <?php
    if ($loggedin && $active && $admin) {
        ?>

        <main>
            <div class="innertube">
                <h2>Control Panel</h2>

                <!-- Nav menu with links to display desired content -->
                <div class="control_panel_menu">
                    <ul>
                        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?content=manage_users">Manage users</a>
                        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?content=manage_entries">Manage entries</a>
                        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?content=manage_inserts">Manage inserts</a>
                        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?content=event_log">Event log</a>
                    </ul>
                </div>

                <br>
                <br>


                <?php
                /* Desired content is displayed here */

                if (isset($_GET['content'])) {
                    echo "<div class=\"control-panel-show\">";
                    include $_GET['content'] . ".php";
                    echo "</div>";
                }

                if ($show_history) {
                    ?>
                    <div class="panel-history-show">
                        <?php
                        if (isset($_POST['restore_data']) && isset($_POST['restore_user'])) {
                            include 'restore_user.php';
                        }

                        if ($history_content == "user" && isset($_GET['id'])) {
                            include 'user_history.php';
                        } else if ($history_content == "entry" && isset($_GET['id'])) {
                            include 'entry_history.php';
                        } else {
                            echo "This should never happen";
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>
            </div>

        </div>
    </main>

    <?php
} else {
    ?>
    <h3 style="color:red">Error: Access denied.</h3>
    <br>
    <a href="index.php">Go home</a>
    <?php
}

include 'bottom.php';
?>

<script>
    function confirmAction(e, msg) {
        if (!confirm(msg))
            e.preventDefault();
    }
</script>
</body>
</html>