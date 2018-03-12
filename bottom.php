<?php if (count(get_included_files()) == 1) exit("Access restricted."); ?>

<!-- Site footer -->
<footer>
    <div class="innertube">
        <img src="images/whitewheels.png" alt="UpStrain logo" height="45"></img>
        <div>
            <h4>External links</h4>
            <a class="external" href="http://igemuppsala.se/" target="_blank">iGEM Uppsala</a>
            <br><a class="external" href="http://igem.org/Main_Page" target="_blank">iGEM Main Page</a>
            <br><a class="external" href="http://parts.igem.org/Main_Page" target="_blank">iGEM Registry</a>
        </div>
        <div>
            <h4>Navigation</h4>
            <a class="bottom" href="index.php">Home</a>
            <br><a class="bottom" href="help.php">Help</a>
            <br><a class="bottom" href="search.php">Search</a>
            <?php if ($loggedin && $active) {
                ?>
                <br><a href="new_insert.php">New entry</a>
                <br><a href="user.php?user_id=<?php echo $_SESSION['user_id']; ?>">My profile</a>
                <?php if ($admin) {
                    ?>
                    <br><a href="control_panel.php">Control panel</a>
                    <?php
                }
                ?>
                <?php
            }
            ?>
        </div>
    </div>
</footer>
</body>
</html>