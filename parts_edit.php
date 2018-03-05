<?php
if (count(get_included_files()) == 1)
    exit("Access restricted.");

if ($loggedin && $active && $admin) {
    include 'scripts/db.php';

    //do some database shit

    mysqli_close($link);
    ?>

    <!-- do some edit form shit -->
    <p>
        In the future, there will be an ok form for editing parts here.
        <br>
        It will probably not meet your expectations...
    </p>

    <?php
    //handle some form return shit
    ?>

    <br>
    <p>
        Down here, there will be some text, probably a total mess. 
        <br>
        It will be <strong><u>sad.</u></strong>
    </p>

    <?php
} else {
    if (isset($_GET["ins_id"])) {
        if (!$loggedin) {
            ?>
            <h3 style="color:red">Access denied (you are not logged in).</h3>
            <br>
            <a href="parts.php?ins_id=<?php echo "$ins_id" ?> ">Go back to insert page</a>
            <?php
        } else if (!$active) {
            ?>
            <h3 style="color:red">Access denied (your account is not activated).</h3>
            <br>
            <a href="parts.php?ins_id=<?php echo "$ins_id" ?> ">Go back to insert page</a>
            <?php
        } else {
            ?>
            <h3 style="color:red">You are not allowed to edit inserts (you are not an admin).</h3>
            <br>
            <a href="parts.php?ins_id=<?php echo "$ins_id" ?> ">Go back to insert page</a>
            <?php
        }
    } else if (isset($_GET["backbone_id"])) {
        if (!$loggedin) {
            ?>
            <h3 style="color:red">Access denied (you are not logged in).</h3>
            <br>
            <a href="parts.php?backbone_id=<?php echo "$backbone_id" ?> ">Go back to backbone page</a>
            <?php
        } else if (!$active) {
            ?>
            <h3 style="color:red">Access denied (your account is not activated).</h3>
            <br>
            <a href="parts.php?backbone_id=<?php echo "$backbone_id" ?> ">Go back to backbone page</a>
            <?php
        } else {
            ?>
            <h3 style="color:red">You are not allowed to edit backbones (you are not an admin).</h3>
            <br>
            <a href="parts.php?backbone_id=<?php echo "$backbone_id" ?> ">Go back to backbone page</a>
            <?php
        }
    } else if (isset($_GET["strain_id"])) {
        if (!$loggedin) {
            ?>
            <h3 style="color:red">Access denied (you are not logged in).</h3>
            <br>
            <a href="parts.php?strain_id=<?php echo "$strain_id" ?> ">Go back to strain page</a>
            <?php
        } else if (!$active) {
            ?>
            <h3 style="color:red">Access denied (your account is not activated).</h3>
            <br>
            <a href="parts.php?strain_id=<?php echo "$strain_id" ?> ">Go back to strain page</a>
            <?php
        } else {
            ?>
            <h3 style="color:red">You are not allowed to edit strains (you are not an admin).</h3>
            <br>
            <a href="parts.php?strain_id=<?php echo "$strain_id" ?> ">Go back to strain page</a>
            <?php
        }
    }
}
