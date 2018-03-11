<?php
if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) { // restrict direct access
    session_start();
}

// URL variable parsing functions
function check_id($input) {
    return preg_match('/^\d+$/', $input) == 1;
}

$is_upstrain_error = FALSE;
$is_mysql_error = FALSE;

// Fetch the insert id from URL
if (isset($_GET["ins_id"])) {
    $part_id = htmlspecialchars(strip_tags(stripslashes(trim($_GET["ins_id"]))));
    $part = "ins.id";
    $name = "ins.name";
    $table = "ins";
    if (!check_id($part_id)) {
        $is_upstrain_error = TRUE;
        $upstrain_error = "Invalid insert ID.";
    }
} else if (isset($_GET["backbone_id"])) {
    $part_id = htmlspecialchars(strip_tags(stripslashes(trim($_GET["backbone_id"]))));
    $part = "backbone.id";
    $name = "backbone.name";
    $table = "backbone";
    if (!check_id($part_id)) {
        $is_upstrain_error = TRUE;
        $upstrain_error = "Invalid backbone ID.";
    }
} else if (isset($_GET["strain_id"])) {
    $part_id = htmlspecialchars(strip_tags(stripslashes(trim($_GET["strain_id"]))));
    $part = "strain.id";
    $name = "strain.name";
    $table = "strain";
    if (!check_id($part_id)) {
        $is_upstrain_error = TRUE;
        $upstrain_error = "Invalid strain ID.";
    }
} else {
    $is_upstrain_error = TRUE;
    $upstrain_error = "No ID specified";
}

if (!$is_upstrain_error) {
    include 'scripts/db.php';

    $id = mysqli_real_escape_string($link, $part_id);
    $sql = "SELECT " . $part . ", " . $name . " AS name FROM " . $table . " WHERE " . $part . " LIKE '$id'";


    $result = mysqli_query($link, $sql);
    $info = mysqli_fetch_assoc($result);
    if (!$result) {
        $is_mysql_error = TRUE;
        $mysql_error = mysqli_error();
    } elseif (mysqli_num_rows($result) < 1) {
        $is_mysql_error = TRUE;
        $mysql_error = "No such insert";
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
} else if ($edit && isset($_GET['ins_id'])) {
    $title = "Edit insert: " . $info['name'];
} else if (!$edit && isset($_GET['ins_id'])) {
    $title = "Insert: " . $info['name'];
} else if ($edit && isset($_GET['backbone_id'])) {
    $title = "Edit backbone: " . $info['name'];
} else if (!$edit && isset($_GET['backbone_id'])) {
    $title = "Backbone: " . $info['name'];
} else if ($edit && isset($_GET['strain_id'])) {
    $title = "Edit strain: " . $info['name'];
} else if (!$edit && isset($_GET['strain_id'])) {
    $title = "Strain: " . $info['name'];
}

include 'top.php';
?>
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
                include 'parts_edit.php';
            } else {
                include 'parts_show.php';
            }
        }
        ?>
    </div>
</main>

<?php include 'bottom.php'; ?>

