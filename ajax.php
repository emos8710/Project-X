<?php

if (isset($_POST['type_val'])) {
    include 'scripts/db.php';
    $vals = $_POST['type_val'];
    $num = count($vals);
    $values = '';
    foreach ($vals as $one) {
        $values[] .= $one;
    }

    $out = '';
    for ($i = 1; $i < $num; $i++) {
        $sql_ins_name = mysqli_query($link, "SELECT name,id,type FROM ins ORDER BY name");
        $out = '<option value="">Select insert name</option>';
        while ($row = $sql_ins_name->fetch_assoc()) {
            if ($values[$i] == $row['type']) {
                $out .= '<option value="' . $row['id'] . '">' . $row['name'] . "</option>";
            }
        }
    }echo $out;
        mysqli_close($link);
}



