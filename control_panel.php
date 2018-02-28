<?php
if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set display for history div
$show_history = isset($_GET['history']);
if ($show_history)
    $history_content = $_GET['history'];
?>

<script>
    function confirmAction(e, msg) {
        if (!confirm(msg))
            e.preventDefault();
    }
</script>

<?php
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
                <br>

                <!-- Nav menu with links to display desired content -->
                <div class="control_panel_menu">
                    <h3>Navigation</h3>
                    <ul>
                        <a href="?content=manage_users">Manage users</a>
                        <a href="?content=manage_entries">Manage entries</a>
                        <a href="?content=event_log">Event log</a></li>
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
                ?>

                <div class="panel-history-show">
                    <?php
                    if ($show_history && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['history'])) {
                        if (isset($_POST['restore_data']) && isset($_POST['restore_user'])) {
                            include 'scripts/db.php';
                            $restore_id = mysqli_real_escape_string($link, $_POST['restore_data']);
                            $user_id = mysqli_real_escape_string($link, $_POST['restore_user']);

                            $check_exists = mysqli_query($link, "SELECT * from users WHERE user_id = " . $user_id);
                            $deleted = (mysqli_num_rows($check_exists) < 1);

                            if ($deleted) {
                                $restore_sql = "INSERT INTO users(active, admin, email, first_name, hash, last_name, password, phone, username, user_id) "
                                        . "SELECT active, admin, email, first_name, hash, last_name, password, phone, username, user_id FROM users_log "
                                        . "WHERE old_data_id = " . $restore_id . ";";
                            } else {
                                $old_data_sql = "SELECT email, first_name, last_name, phone, username "
                                        . "FROM users_log WHERE old_data_id = " . $restore_id . ";";

                                $old_data = mysqli_fetch_assoc(mysqli_query($link, $old_data_sql));
                                $restore_sql = "UPDATE users SET email = '" . $old_data['email'] . "', first_name = '" . $old_data['first_name'] . "', last_name = '" . $old_data['last_name']
                                        . "', phone = '" . $old_data['phone'] . "', username = '" . $old_data['username'] . "' WHERE user_id = " . $user_id;
                            }

                            $restore_query = mysqli_query($link, $restore_sql);
                            ?>
                            <p>
                                <?php
                                if (!$restore_query) {
                                    ?>
                                    <strong style="color:red">Error: <?php echo mysqli_error($link); ?></strong>
                                    <?php
                                } else {
                                    ?>
                                    <strong style="color:green">User info successfully restored!</strong>
                                    <?php
                                }
                                ?>
                                <br>
                                Reloading in 10 seconds... <a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Reload now</a>
                                <?php
                                header("Refresh: 10; url=" . $_SERVER['REQUEST_URI']);
                            }

                            if ($history_content == "user") {
                                include 'scripts/db.php';

                                $id = mysqli_real_escape_string($link, $_POST['history']);

                                $current_info_sql = "SELECT username, first_name, last_name, email, phone, admin FROM users WHERE user_id = " . $id;
                                $current_info_query = mysqli_query($link, $current_info_sql);
                                $old_info_sql = "SELECT old_data_id AS id, user_id AS uid, username, first_name, last_name, email, phone, admin, type, FROM_UNIXTIME(time) AS time FROM users_log WHERE user_id = " . $id . " ORDER BY time DESC";
                                $old_info_query = mysqli_query($link, $old_info_sql);

                                $is_deleted = (mysqli_num_rows($current_info_query) < 1);
                                $has_history = (mysqli_num_rows($old_info_query) >= 1);

                                mysqli_close($link) or die("Could not close connection to database");
                                ?>

                            <h3>User <?php echo $id; ?> info history</h3>
                            <em>Logged history is automatically removed after 30 days.</em>

                            <table class="control-panel-history">
                                <col><col><col><col><col><col><col><col>
                                <tr>
                                    <th class="top" colspan="7">Current data</th>
                                </tr>
                                <tr>
                                    <?php
                                    if ($is_deleted) {
                                        ?>
                                        <td colspan="7"><strong>No active data (user has been removed).</strong></td>
                                        <?php
                                    } else {
                                        $data = mysqli_fetch_assoc($current_info_query);
                                        ?>
                                        <th>Username</th>
                                        <th>Name</th>
                                        <th>E-mail address</th>
                                        <th>Phone number</th>
                                        <th>Admin</th>
                                    </tr>
                                    <tr>
                                        <td><?php echo $data['username']; ?></td>
                                        <td><?php echo $data['first_name'] . " " . $data['last_name']; ?></td>
                                        <td><?php echo $data['email']; ?></td>
                                        <td><?php echo $data['phone']; ?></td>
                                        <td><?php
                                            if ($data['admin'] == '1'): echo "Yes";
                                            else: echo "No";
                                            endif;
                                            ?></td>
                                        <?php
                                    }
                                    ?>
                                </tr>
                                <tr>
                                    <th class="top" colspan="7">Old data</th>
                                </tr>
                                <?php
                                if ($has_history) {
                                    ?>
                                    <tr>
                                        <th>Event type</th>
                                        <th>Username</th>
                                        <th>Name</th>
                                        <th>E-mail address</th>
                                        <th>Phone number</th>
                                        <th>Time recorded</th>
                                        <th>Restore</th>
                                    </tr>
                                    <?php
                                    while ($data = mysqli_fetch_assoc($old_info_query)) {
                                        ?>
                                        <tr>
                                            <td><?php echo $data['type']; ?></td>
                                            <td><?php echo $data['username']; ?></td>
                                            <td><?php echo $data['first_name'] . " " . $data['last_name']; ?></td>
                                            <td><?php echo $data['email']; ?></td>
                                            <td><?php echo $data['phone']; ?></td>
                                            <td><?php echo $data['time']; ?></td>
                                            <td>
                                                <form class="control-panel" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
                                                    <input type="hidden" name="restore_data" value="<?php echo $data['id']; ?>">
                                                    <input type="hidden" name="restore_user" value="<?php echo $data['uid']; ?>">
                                                    <input type="hidden" name="history" value="<?php echo $id; ?>"> 
                                                    <button type="submit" class="control-panel-restore" title="Restore" onclick="confirmAction(event, 'Restore user <?php echo $data['uid']; ?> to this record?')"/>
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
                            <?php
                        } else if ($history_content == "entry") {
                            include 'scripts/db.php';

                            $id = mysqli_real_escape_string($link, $_POST['history']);

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

                            <?php
                        } else {
                            echo "This should never happen";
                        }
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

</body>
</html>