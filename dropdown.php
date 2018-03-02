<?php

include 'scripts/db.php'; 

$out = '';
$sql = "SELECT id,name from ins WHERE type='" . $_POST['inst'] . "' ORDER BY name";
$res = mysqli_query($link, $sql);
$out = '<option value="">Select insert name</option>';
while ($row = mysqli_fetch_assoc($res)) {
    $out .= '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
}
echo $out;
