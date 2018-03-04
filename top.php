<?php
if (count(get_included_files()) == 1)
    exit("Access restricted."); //prevent direct access

    /* Logs out user if no activity in a certain time */
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true && isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > (100000))) {
    /* If the logout button is pressed the refresh is not made */
    if (basename($_SERVER['PHP_SELF'] != "logout.php")) {
        $_SESSION['logged_in'] = false;
        header("Refresh:0; url=logout.php");
        session_unset();     // unset $_SESSION variable for the run-time 
        session_destroy();   // destroy session data in storage
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
?>
<!DOCTYPE html>

<html lang="en">

    <!--Scripts, meta tags, stylesheets, favicon, title-->
    <head>
        <script src = "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.js"></script>
        <script src='https://www.google.com/recaptcha/api.js'></script>
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/upstrain.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.css">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
        <title><?php echo $title; ?></title>
    </head>

    <body>
        <header>
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
                            <input class ="quicksearch" type="text" placeholder="Search UpStrain ID" name="upstrain_id"></input>
                            <button class="quicksearch" type="submit"><img class="quicksearch" src="images/search_button.png"></img></button>
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