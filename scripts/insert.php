
<?php

include db.php; 


if (mysqli_query($link, $sql)) {
    echo "New entry created successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($link);
}

$strain= mysqli_real_escape_string($link,$_REQUEST['strain']);
$backbone= mysqli_real_escape_string($link,$_REQUEST['backbone']);
$ins = mysqli_real_escape_string($link,$_REQUEST['ins']);
$ins_type = ""; 
$reg_link = mysqli_real_escape_string($link,$_REQUEST['registry']);; 
$year = mysqli_real_escape_string($link,$_REQUEST['year']);
$file = ""; 
$comment = mysqli_real_escape_string($link,$_REQUEST['comment']);

$sql_strain = "INSERT INTO strain (name) VALUES (?)"; 
$stmt_strain = mysqli_prepare($sql_strain); 
$stmt_strain->bind_param("s",$strain); 
$stmt_strain->execute(); 

$sql_backbone = "INSERT INTO backbone (name) VALUES(?)"; 
$stmt_backbone = mysqli_prepare($sql_backbone); 
$stmt_backbone->bind_param("s",$backbone); 
$stmt_backbone->execute();

$sql_entry = "INSERT INTO entry (year_created, comment) 
       VALUES (?,?)"; 
$stmt_entry = mysqli_prepare($sql_entry); 
$stmt_entry->bind_param("ss",$year, $comment); 
$stmt_entry->execute();
$stmt_entry->close(); 

mysqli_close($link) or die('Could not close connection to database');

?>



