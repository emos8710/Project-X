<?php
if (count(get_included_files()) == 1)
    exit("Access restricted");

include 'scripts/db.php';

$restore_id = mysqli_real_escape_string($link, $_POST['restore_data']);
$entry_id = mysqli_real_escape_string($link, $_POST['restore_entry_insert']);

$old_sql = "SELECT insert_id, position FROM entry_inserts_log WHERE old_data_id = '$restore_id'";
$old_data = mysqli_fetch_assoc(mysqli_query($link, $old_sql));

$check_entry_exists = mysqli_query($link, "SELECT * FROM entry WHERE entry.id = " . $entry_id);
$check_insert_exists = mysqli_query($link, "SELECT * FROM ins WHERE id = " . $old_data['insert_id']);
?>
<p>
    <?php
    if (mysqli_num_rows($check_entry_exists) < 1) {
        ?>
        <strong style="color:red">Error: Cannot restore insert (the entry doesn't exist anymore).</strong>
        <?php
    } else if (mysqli_num_rows($check_insert_exists) < 1) {
        ?>
        <strong style="color:red">Error: Cannot restore insert (it doesn't exist anymore).</strong>
        <?php
    } else {
        $move_sql = "UPDATE entry_inserts SET position = position+1 WHERE position >= " . $old_data['position'] . " AND entry_id = '$entry_id'";
        $add_sql = "INSERT INTO entry_inserts(entry_id, insert_id, position) VALUES(" . $entry_id . ", " . $old_data['insert_id'] . ", " . $old_data['position'] . ")";
        $move_query = mysqli_query($link, $move_sql);
        $add_query = mysqli_query($link, $add_sql);
        ?>
        <?php
        if (!$move_query || !$add_query) {
            ?>
            <strong style="color:red">Error: <?= mysqli_error($link) ?></strong>
            <?php
        } else {
            ?>
            <strong style="color:green">Insert successfully restored to entry <?= $entry_id ?>!</strong>
            <?php
        }
    }
    mysqli_close($link) or die("Could not close database connection");
    ?>
    <br>
    Reloading in 10 seconds... <a href="<?= $_SERVER['REQUEST_URI'] ?>">Reload now</a>
</p>
