<?php
if (count(get_included_files()) == 1)
    exit("Access restricted");

include 'scripts/db.php';
$restore_id = mysqli_real_escape_string($link, $_POST['restore_data']);
$instype_id = mysqli_real_escape_string($link, $_POST['restore_instype']);

$check_exists = mysqli_query($link, "SELECT * from ins_type WHERE id = " . $instype_id);
$deleted = (mysqli_num_rows($check_exists) < 1);

if ($deleted) {
    $restore_sql = "INSERT INTO ins_type(id, name) "
            . "SELECT id, name FROM ins_type_log WHERE old_data_id = " . $restore_id . ";";
} else {
    $old_data_sql = "SELECT name FROM ins_type_log WHERE old_data_id = " . $restore_id . ";";

    $old_data = mysqli_fetch_assoc(mysqli_query($link, $old_data_sql));
    $restore_sql = "UPDATE ins_type SET name = '" . $old_data['name'] . "' WHERE id = " . $instype_id . ";";
}

$restore_query = mysqli_query($link, $restore_sql);
?>
<p>
    <?php
    if (!$restore_query) {
        ?>
        <strong style="color:red">Error: <?php echo mysqli_error($link); ?></strong>
        <?php
    } else {
        ?>
        <strong style="color:green">Insert type info successfully restored!</strong>
        <?php
    }
    mysqli_close($link) or die("Could not close database connection");
    ?>
    <br>
    Reloading in 10 seconds... <a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Reload now</a>
</p>
