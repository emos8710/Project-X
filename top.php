<?php
if (count(get_included_files()) == 1)
    exit("Access restricted."); //prevent direct access

    /* Logs out user if no activity in a certain time */
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true && isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > (7200))) {
    /* If the logout button is pressed the refresh is not made */
    if (basename($_SERVER['PHP_SELF'] != "logout.php")) {
        $_SESSION['logged_in'] = false;
        header("Refresh:0; url=logout.php");
    }
} else {
    $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    $loggedin = TRUE;
} else {
    $loggedin = FALSE;
}

if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) {
    $admin = TRUE;
} else {
    $admin = FALSE;
}

if (isset($_SESSION['active']) && $_SESSION['active'] == 1) {
    $active = TRUE;
} else {
    $active = FALSE;
}

function test_input($string) {
    return htmlspecialchars(strip_tags(stripslashes(trim($string))));
}

/* Making sure that the files directory contains only the correct files */
include 'scripts/db.php';

$all_files = mysqli_query($link, "SELECT name_new FROM upstrain_file");

$scanned_directory = array_diff(scandir('files'), array('..', '.'));
$database_files = [];

while ($file = mysqli_fetch_assoc($all_files)) {
    if (!in_array($file['name_new'], $scanned_directory)) {
        mysqli_query($link, "DELETE FROM upstrain_file WHERE name_new = " . $file['name_new']); // If a database entry has no corresponding file, delete it
    } else {
        array_push($database_files, $file['name_new']);
    }
}

foreach ($scanned_directory as $file) {
    if (!in_array($file, $database_files)) {
        unlink('files/' . $file); // If a file has no corresponding database entry, delete it
    }
}

mysqli_close($link) or die("Could not close database connection");
?>
<!DOCTYPE html>

<html lang="en">

    <!--Scripts, meta tags, stylesheets, favicon, title-->
    <head>
        <script type="text/javascript" src = "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src='https://www.google.com/recaptcha/api.js'></script>
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="css/upstrain.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="css/datatable.css">
        <link rel="apple-touch-icon" sizes="180x180" href="icons/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="icons/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="icons/favicon-16x16.png">
        <link rel="manifest" href="site.webmanifest">
        <link rel="mask-icon" href="icons/safari-pinned-tab.svg" color="#5bbad5">
        <title><?= "UpStrain - " . $title ?></title>
    </head>

    <body>
        <header id="top">
            <!-- Navigation bar and logo -->
            <nav class="navigation">
                <!-- Logo -->
                <div class="logo">
                    <a class="logo" href="index.php">
                        <img id="logo" src="images/uplogo.png" alt="UpStrain logo">
                    </a>
                </div>

                <!-- NAVIGATION BUTTONS -->
                <div class="nav-wrapper">
                    <!-- Home -->
                    <a <?php
                    if (basename($_SERVER['PHP_SELF']) === "index.php") {
                        echo "class=\"active\" ";
                    }
                    ?> href="index.php">Home</a>

                    <!-- Help -->
                    <a <?php
                    if (basename($_SERVER['PHP_SELF']) === "help.php") {
                        echo "class=\"active\" ";
                    }
                    ?> href="help.php">Help</a>

                    <!-- Search -->
                    <a <?php
                    if (basename($_SERVER['PHP_SELF']) === "search.php") {
                        echo "class=\"active\" ";
                    }
                    ?> href="search.php">Search</a>

                    <?php if ($loggedin && $active) {
                        ?>
                        <!--  New Entry -->
                        <a <?php
                        if (basename($_SERVER['PHP_SELF']) === "new_insert.php") {
                            echo "class=\"active\" ";
                        }
                        ?> href="new_insert.php">New Entry</a>
                            <?php
                        }
                        ?>


                    <?php if (isset($_SESSION['active']) && $active && $loggedin && isset($_SESSION['user_id'])) { ?>
                        <!-- Profile -->
                        <a <?php
                        if (basename($_SERVER['PHP_SELF']) === "user.php" && isset($isowner) && $isowner) {
                            echo "class=\"active\" ";
                        }
                        ?> href="user.php?user_id=<?php echo $_SESSION['user_id']; ?>">My Profile</a>
                            <?php
                        }
                        ?> 


                    <?php if ($loggedin && $admin && isset($_SESSION['user_id'])) { ?>
                        <!-- Control Panel (if admin) -->
                        <a <?php
                        if (basename($_SERVER['PHP_SELF']) === "control_panel.php") {
                            echo "class=\"active\"";
                        }
                        ?> href="control_panel.php">Control Panel</a>
                            <?php
                        }
                        ?>

                </div>

                <div class="right-wrapper">
                    <!-- Quick search -->
                    <div class="quicksearch">
                        <form class="quicksearch" action="entry.php">
                            <input class ="quicksearch" type="text" placeholder="Search UpStrain ID" name="upstrain_id" id="quicksearchfield"></input>
                            <button class="quicksearch" type="submit"><img class="quicksearch" src="images/search_button.png" alt="search-icon"></button>
                        </form>
                        <a class="quicksearch" href="search.php">Advanced search</a>
                    </div>

                    <!-- Login -->
                    <?php
                    if (isset($_SESSION['active']) && $active && $loggedin) {
                        ?>
                        <a class="login" href="logout.php">Log out</a>
                        <?php
                    } else {
                        ?>
                        <a class="login 
                           <?php
                           if (basename($_SERVER['PHP_SELF']) === "logsyst.php") {
                               echo " active";
                           }
                           ?>" href="logsyst.php">Log in</a>	
                           <?php
                       }
                       ?>
                </div>
            </nav>
        </header>
        <?php
        if (isset($loggedout_message)) {
            echo $loggedout_message;
        }
        ?>
        <script>
            $("#quicksearchfield").focus(function (e) {
                $("#quicksearchfield").attr("placeholder", "UUYYYYXXX");
            });
            $('#quicksearchfield').blur(function (e) {
                $("#quicksearchfield").attr("placeholder", "Search UpStrain ID");
            });
            $(document).ready(function () {
                $('#eventlog').DataTable({
                    paging: true,
                    select: true,
                    "order": [[0, "desc"]]
                });
            });
            $(document).ready(function () {
                $('#searchtable').DataTable({
                    "searching": false,
                    paging: true,
                    select: true,
                    "order": [[0, "asc"]]
                });
            });
        </script>