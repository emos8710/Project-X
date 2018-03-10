<?php
if (count(get_included_files()) == 1)
    exit("Access restricted");

include 'scripts/db.php';
$restore_id = mysqli_real_escape_string($link, test_input($_POST['restore_data']));
$entry_id = mysqli_real_escape_string($link, test_input($_POST['restore_entry']));

$check_exists = mysqli_query($link, "SELECT * from entry WHERE  id = " . $entry_id);
?>
<p>
    <?php
    if (mysqli_num_rows($check_exists) == 1) { // Check if the entry still exists
        $old_sql = "SELECT entry_log.comment AS cmt, entry_log.private AS private, entry_log.created AS created, "
                . "entry_log.creator AS uid, entry_log.entry_reg AS biobrick, entry_log.backbone AS bid, entry_log.strain AS sid, "
                . "FROM entry_log "
                . "WHERE entry_log.old_data_id = '$restore_id'";
        $old_data = mysqli_fetch_assoc(mysqli_query($link, $old_sql));

        $check_backbone_exists = mysqli_query($link, "SELECT * FROM backbone WHERE id = " . $old_data['bid']);
        $check_strain_exists = mysqli_query($link, "SELECT * FROM strain WHERE id = " . $old_data['sid']);
        $check_user_exists = mysqli_query($link, "SELECT * FROM users WHERE user_id = " . $old_data['uid']);

        if (mysqli_num_rows($check_backbone_exists) < 1) {
            ?>
            <strong style="color:red">Cannot restore entry: its backbone has been removed.</strong>
            <?php
        } else if (mysqli_num_rows($check_strain_exists) < 1) {
            ?>
            <strong style="color:red">Cannot restore entry: its strain has been removed.</strong>
            <?php
        } else if (mysqli_num_rows($check_user_exists) < 1) {
            ?>
            <strong style="color:red">Cannot restore entry: its creator has been removed.</strong>
            <?php
        } else {
            $restore_sql = "UPDATE entry "
                    . "SET comment = '" . $old_data['cmt'] . "', private = '" . $old_data['private'] . "', "
                    . "entry_reg = '" . $old_data['biobrick'] . "', backbone = '" . $old_data['bid'] . "', "
                    . "strain = '" . $old_data['sid'] . "' WHERE id = '$entry_id'";

            $restore_query = mysqli_query($link, $restore_sql);

            if (!$restore_query) {
                ?>
                <strong style="color:red">Error: <?php echo mysqli_error($link); ?></strong>
                <?php
            } else {
                ?>
                <strong style="color:green">Entry info successfully restored!</strong>
                <?php
            }
        }
    } else {
        ?>
        <strong style="color:red">This entry has been permanently deleted and cannot be restored. Please create a new entry.</strong>
        <?php
    }
    mysqli_close($link) or die("Could not close database connection");
    ?>
    <br>
    Reloading in 10 seconds... <a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Reload now</a>
</p>