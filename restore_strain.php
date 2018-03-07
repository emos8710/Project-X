<?php
if (count(get_included_files()) == 1)
    exit("Access restricted");


include 'scripts/db.php';
$restore_id = mysqli_real_escape_string($link, $_POST['restore_data']);
$strain_id = mysqli_real_escape_string($link, $_POST['restore_strain']);

$check_exists = mysqli_query($link, "SELECT * from strain WHERE id = " . $strain_id);

$old_sql = "SELECT name, comment, private, creator FROM strain_log WHERE old_data_id = " . $restore_id;
$old_data = mysqli_fetch_assoc(mysqli_query($link, $old_sql));

$check_user_exists = mysqli_query($link, "SELECT * FROM users WHERE user_id = " . $old_data['creator']);
?>
<p>
    <?php
    if (mysqli_num_rows($check_user_exists) < 1) {
        ?>
        <strong style="color:red">Cannot restore strain (its user has been removed).</strong>
        <?php
    } else {
        if (mysqli_num_rows($check_exists) < 1) {
            $restore_sql = "INSERT INTO strain(id, name, comment, private, creator, date_db) "
                    . "SELECT id, name, comment, private, creator, date_db FROM strain_log WHERE old_data_id = " . $restore_id . ";";
        } else {
            $restore_sql = "UPDATE strain SET name = '" . $old_data['name'] . "', comment = '" . $old_data['comment'] . "', "
                    . "private = '" . $old_data['private'] . "' WHERE id = " . $strain_id;
        }

        $restore_query = mysqli_query($link, $restore_sql);
        ?>
        <?php
        if (!$restore_query) {
            ?>
            <strong style="color:red">Error: <?php echo mysqli_error($link); ?></strong>
            <?php
        } else {
            ?>
            <strong style="color:green">Strain info successfully restored!</strong>
            <?php
        }
    }
    mysqli_close($link) or die("Could not close database connection");
    ?>
    <br>
    Reloading in 10 seconds... <a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Reload now</a>
</p>
