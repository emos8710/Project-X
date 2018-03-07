<?php
if (count(get_included_files()) == 1)
    exit("Access restricted");

if (isset($_POST['what'])) {
    include 'scripts/db.php';

    $restore_id = mysqli_real_escape_string($link, $_POST['restore_data']);
    $entry_id = mysqli_real_escape_string($link, $_POST['restore_entry']);
    $what = $_POST['what'];

    if ($what === "entry") {
        $check_exists = mysqli_query($link, "SELECT * FROM entry WHERE id = '$id'");

        if (mysqli_num_rows($check_exists) < 1) {
            $restore_msg = "<strong style=\"color:red\">This entry is permanently deleted and cannot be restored. Please create a new entry.</strong>";
            $no_query = TRUE;
        } else {
            $old_sql = "SELECT entry_log.comment AS cmt, entry_log.private AS private, entry_log.created AS created, "
                    . "entry_log.entry_reg AS biobrick, backbone.id AS bid, strain.id AS sid "
                    . "LEFT JOIN backbone ON entry_log.backbone = backbone.id "
                    . "LEFT JOIN strain ON entry_log.strain = strain.id "
                    . "WHERE entry_log.old_data_id = '$restore_id'";
            $old_data = mysqli_fetch_assoc(mysqli_query($link, $old_sql));

            $restore_sql = "UPDATE entry "
                    . "SET comment = '" . $old_data['cmt'] . "', private = '" . $old_data['private'] . "', "
                    . "entry_reg = '" . $old_data['biobrick'] . "', backbone = '" . $old_data['bid'] . "', "
                    . "strain = '" . $old_data['sid'] . "' WHERE id = '$entry_id'";
        }
    } else if ($what === "entry_insert") {
        $old_sql = "SELECT insert_id, position FROM entry_inserts_log WHERE old_data_id = '$restore_id'";
        $old_data = mysqli_fetch_assoc(mysqli_query($link, $old_sql));

        $move_sql = "UPDATE entry_inserts SET position = position-+1 WHERE position >= " . $old_data['position'] . " AND entry_id = '$entry_id'";
        $add_sql = "INSERT INTO entry_inserts(entry_id, insert_id, position) VALUES('$entry_id', " . $old_data['insert_id'] . ", " . $old_data['position'] . ")";
        $restore_sql = $move_sql . "; " . $add_sql;
    } else {
        echo "This should never happen";
    }

    mysqli_close($link) or die("Could not close database connection");

    if (!$no_query)
        $restore_query = mysqli_query($link, $restore_sql);

    if ($what === "entry" && !$no_query) {
        if (!$restore_query) {
            $restore_msg = "<strong style=\"color:red\">Database error: Failed to restore entry.</strong>";
        } else {
            $restore_msg = "<strong style=\"green\">Entry succesfully restored!</strong>";
        }
    } else if ($what === "entry_insert") {
        if (!$restore_query) {
            $restore_msg = "<strong style=\"color:red\">Database error: Failed to restore insert to entry.</strong>";
        } else {
            $restore_msg = "<strong style=\"green\">Insert succesfully restored to entry!</strong>";
        }
    }
    echo $restore_msg;
    ?>
    <br>
    Reloading in 10 seconds... <a href="<?= $_SERVER['REQUEST_UTI'] ?>">Reload now</a>
    <?php
}
