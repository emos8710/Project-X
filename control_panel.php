<?php
// Start session if session closed
if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set display for history div
$show_history = isset($_GET['history']) && isset($_GET['id']);

// Handle headers
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['header']) && $_POST['header'] === "refresh") {
    if (isset($current_url)): header("Refresh:10, url=" . $current_url);
    else: header("Refresh:10");
    endif;
}

$title = "Control Panel";

include 'top.php';

if (isset($_GET['content'])) {
    $content = test_input($_GET['content']);
}
?>

<?php
if ($loggedin && $active && $admin) {
    ?>
    <main>
        <div class="innertube">
            <!-- Nav menu with links to display desired content -->
            <div class="control_panel_menu">
                <ul>
                    <a <?php
                    if (isset($content) && $content === "manage_users")
                        echo "class=\"active\" ";
                    ?>href="<?php echo $_SERVER['PHP_SELF'] ?>?content=manage_users">
                        Manage users
                    </a>
                    <a <?php
                    if (isset($content) && $content === "manage_entries")
                        echo "class=\"active\" ";
                    ?>href="<?php echo $_SERVER['PHP_SELF'] ?>?content=manage_entries">
                        Manage entries
                    </a>
                    <a <?php
                    if (isset($content) && $content === "manage_inserts")
                        echo "class=\"active\" ";
                    ?>href="<?php echo $_SERVER['PHP_SELF'] ?>?content=manage_inserts">
                        Manage inserts
                    </a>
                    <a <?php
                    if (isset($content) && $content === "manage_strain_backbone")
                        echo "class=\"active\"";
                    ?>href="<?php echo $_SERVER['PHP_SELF'] ?>?content=manage_strain_backbone">
                        Manage strains & backbones
                    </a>
                    <a <?php
                    if (isset($content) && $content === "event_log")
                        echo "class=\"active\" ";
                    ?>href="<?php echo $_SERVER['PHP_SELF'] ?>?content=event_log">
                        Event log
                    </a>
                </ul>
            </div>

            <br>
            <br>

            <?php
            /* Desired content is displayed here */

            if (isset($content)) {
                echo "<div class=\"control-panel-show\">";
                include $content . ".php";
                echo "</div>";
            }

            if ($show_history) {
                ?>
                <div class="panel-history-show">
                    <?php
                    $pages = ["user", "instype", "insert", "backbone", "strain", "entry", "entry_insert"];
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['restore_data'])) {
                        foreach ($pages as $page) {
                            $page = 'restore_' . $page;
                            if (isset($_POST[$page])) {
                                include $page . '.php';
                                break;
                            }
                        }
                    }

                    include test_input($_GET['history']) . '_history.php';
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
?>

<script>
    function confirmAction(e, msg) {
        if (!confirm(msg))
            e.preventDefault();
    }
</script>

<?php include 'bottom.php'; ?>
