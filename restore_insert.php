<?php
if (count(get_included_files()) == 1)
    exit("Access restricted");

include 'scripts/db.php';
$restore_id = mysqli_real_escape_string($link, $_POST['restore_data']);
$insert_id = mysqli_real_escape_string($link, $_POST['restore_insert']);

$check_exists = mysqli_query($link, "SELECT * from ins WHERE id = " . $user_id);
$deleted = (mysqli_num_rows($check_exists) < 1);

