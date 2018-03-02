<?php
if (count(get_included_files()) == 1)
    exit("Access restricted");

include 'scripts/db.php';

$id = mysqli_real_escape_string($link, $_GET['id']);

$current_info_sql = "SELECT ins.name, ins.ins_reg AS biobrick, ins.comment AS cmt, "
        . "ins_type.name AS type, users.first_name AS fname, users.last_name AS lname "
        . "FROM ins "
        . "LEFT JOIN ins_type ON ins.type = ins_type.id "
        . "LEFT JOIN users ON ins.creator = users.user_id "
        . "WHERE ins.id = " . $id;
$current_info_query = mysqli_query($link, $current_info_sql);
$old_info_sql = "SELECT FROM_UNIXTIME(ins_log.time) AS time, ins_log.event_type AS etype, "
        . "ins_log.name AS name, ins_log.old_data_id AS oid, ins_log.id AS id, ins_log.comment AS cmt, "
        . "users.first_name AS fname, users.last_name AS lname, ins_type.name AS itype "
        . "FROM ins_log "
        . "LEFT JOIN ins_type ON ins_log.type = ins_type.id "
        . "LEFT JOIN users ON ins_log.creator = users.user_id "
        . "WHERE ins_log.id = " . $id;
$old_info_query = mysqli_query($link, $old_info_sql);

$is_deleted = (mysqli_num_rows($current_info_query) < 1);
$has_history = (mysqli_num_rows($old_info_query) >= 1);

mysqli_close($link) or die("Could not close connection to database");

?>

<h3>Insert <?php echo $id; ?> info history</h3>
<em>Logged history is automatically removed after 30 days.</em>