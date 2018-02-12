
<?php

include db.php; 

$strain= mysqli_real_escape_string($link,$_REQUEST['strain']);
$backbone= mysqli_real_escape_string($link,$_REQUEST['backbone']);
$ins = mysqli_real_escape_string($link,$_REQUEST['strain']);
$ins_type = ""; 
$reg_link = ""; 
$year = mysqli_real_escape_string($link,$_REQUEST['strain']);
$file = ""; 
$comment = mysqli_real_escape_string($link,$_REQUEST['strain']);

$sql = "INSERT INTO entry (strain, backbone, year_created, comment) 
       VALUES (?,?,?,?)"; 
$stmt = mysqli_prepare($sql); 
$stmt->bind_param("ssss",$strain, $backbone, $year, $comment); 
$stmt->execute();
$stmt->close(); 

if (mysqli_query($link, $sql)) {
    echo "New entry created successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($link);
}

mysqli_close($link) or die('Could not close connection to database');

?>


