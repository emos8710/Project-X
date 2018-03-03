<?php
if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) { // restrict direct access
    session_start();
}

// URL variable parsing function
function check_upstrain_id($input) {
    if (!is_string($input))
        return FALSE;
    if (preg_match('/^(UU||uu)[1-2][0-9]{6}$/', $input) == 1): return TRUE;
    else: return FALSE;
    endif;
}

$is_upstrain_error = FALSE;
$is_mysql_error = FALSE;

// Fetch the upstrain id from URL
if (isset($_GET["upstrain_id"])) {
    $upstrain_id = $_GET["upstrain_id"];
    if (!check_upstrain_id($upstrain_id)) {
        $is_upstrain_error = TRUE;
        $upstrain_error = "Invalid entry ID.";
    }
} else {
    $is_upstrain_error = TRUE;
    $upstrain_error = "No entry ID specified";
}

if (!$is_upstrain_error) {
    include 'scripts/db.php';

    $id = mysqli_real_escape_string($link, $upstrain_id);
    $sql = "SELECT upstrain_id FROM entry_upstrain WHERE upstrain_id LIKE '$id'";

    $result = mysqli_query($link, $sql);
    if (!$result) {
        $is_mysql_error = TRUE;
        $mysql_error = mysqli_error();
    } elseif (mysqli_num_rows($result) < 1) {
        $is_mysql_error = TRUE;
        $mysql_error = "No such entry";
    }

    mysqli_close($link) or die("Could not close database connection");
}

// check if edit or show
$edit = isset($_GET["edit"]);


// set page title
if ($is_upstrain_error) {
    $title = "ID error";
} else if ($is_mysql_error) {
    $title = "Database Error";
} else if ($edit) {
    $title = "Edit entry " . strtoupper($upstrain_id);
} else {
    $title = "Entry " . strtoupper($upstrain_id);
}
?>

<!DOCTYPE html>

<?php include 'top.php'; ?>

<body>
    <!-- Body content of page -->

    <main>
        <div class="innertube">
            <?php
            // print errors...
            if ($is_upstrain_error || $is_mysql_error) {
                if ($is_upstrain_error)
                    echo "<h3>Error: " . $upstrain_error . "</h3><br>";
                if ($is_mysql_error)
                    echo "<h3>Error: " . $mysql_error . "</h3>";
                echo "<br>" .
                "<a href=\"javascript:history.go(-1)\">Go back</a>";
                //...or show correct content
            }else {
                if ($edit) {
                    include 'entry_edit.php';
                } else {
                    include 'entry_show.php';
                }
            }
            ?>
        </div>
    </main>

    <?php include 'bottom.php'; ?>

</body>
</html>