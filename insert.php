
<?php

$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "upstrain_kristina";
$link = new mysqli($hostname, $username, $password, $dbname);

if ($link->connect_error) {
    die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    exit();
}
//
//else{
//echo 'Success... ' . mysqli_get_host_info($link) . "\n";
//}

// Variables

$year = $_REQUEST['year'];
$comment = $_REQUEST['comment'];
$strain = $_REQUEST['strain'];
$backbone = $_REQUEST['backbone'];
$ins = $_REQUEST['ins'];


$reg_link = mysqli_real_escape_string($link, $_REQUEST['registry']);
;

$ins_type = "";
$file = "";
$bb_reg = "";
$creator = "";

$current_date = date("Y-m-d");

// Strain 
$check = "SELECT name FROM strain WHERE name LIKE '$strain'";
$check_query = mysqli_query($link, $check);
if (mysqli_num_rows($check_query) < 1) {
    $sql_strain = "INSERT INTO strain (name) VALUES (?)";
    $stmt_strain = $link->prepare($sql_strain);
    $stmt_strain->bind_param("s", $strain);
    $stmt_strain->execute();
    echo "Strain inserted into strain successfully";
    $stmt_strain->close();

    $strain_s = "SELECT id FROM strain WHERE name LIKE '$strain'";
    $strain_s_query = mysqli_query($link, $strain_s);
    $strain_row = mysqli_fetch_assoc($strain_s_query);
    $strain_row_id = $strain_row["id"];
} else {

    $strain_s = "SELECT id FROM strain WHERE name LIKE '$strain'";
    $strain_s_query = mysqli_query($link, $strain_s);
    $strain_row = mysqli_fetch_assoc($strain_s_query);
    $strain_row_id = $strain_row["id"];

    $strain_id = "INSERT INTO entry (strain) VALUES (?)";
    $stmt_strainid = $link->prepare($strain_id);
    $stmt_strainid->bind_param("s", $strain_row_id);
    $stmt_strainid->execute();
    $stmt_strainid->close();
}


// Backbone
$check2 = "SELECT name FROM backbone WHERE name LIKE '$backbone'";
$check_query2 = mysqli_query($link, $check2);
if (mysqli_num_rows($check_query2) < 1) {
    $sql_backbone = "INSERT INTO backbone (name, date_db) VALUES (?,?)";
    $stmt_backbone = $link->prepare($sql_backbone);
    $stmt_backbone->bind_param("ss", $backbone, $current_date);
    $stmt_backbone->execute();
    echo "Backbone inserted into backbone successfully";
    $stmt_backbone->close();

    $back_s = "SELECT id FROM backbone WHERE name LIKE '$backbone'";
    $back_s_query = mysqli_query($link, $back_s);
    $back_row = mysqli_fetch_assoc($back_s_query);
    $back_row_id = $back_row["id"];
} else {
    $back_s = "SELECT id FROM backbone WHERE name LIKE '$backbone'";
    $back_s_query = mysqli_query($link, $back_s);
    $back_row = mysqli_fetch_assoc($back_s_query);
    $back_row_id = $back_row["id"];

    $back_id = "INSERT INTO entry (backbone) VALUES (?)";
    $stmt_backid = $link->prepare($back_id);
    $stmt_backid->bind_param("s", $back_row_id);
    $stmt_backid->execute();
    $stmt_backid->close();
}

// Entry
$sql_entry = "INSERT INTO entry (year_created, comment, date_db, backbone, strain) VALUES (?,?,?,?,?)";
$stmt_entry = $link->prepare($sql_entry);
$stmt_entry->bind_param("sssss", $year, $comment, $current_date, $back_row_id, $strain_row_id);
$stmt_entry->execute();
echo "Year, comment, date_db inserted into entry successfully";
$stmt_entry->close();

// Insert 
//if(isset($_POST['submit'])) {
   
//if ($_POST['ins']) {
    //foreach ($_POST['ins'] as $key=>$value) {
            //$sql_ins = "INSERT INTO ins (name,date_db) VALUES (?,?)";
            //$stmt_ins = $link->prepare($sql_ins);
            //->bind_param("ss", $value, $current_date);
            //->execute();
            //->close();
       // }
    //echo "Insert inserted into ins successfully"; 
    //}
//} else {
    //echo "No inserts given";
//}

    
//


//Reg link 

//Sequence 


$link->close() or die('Could not close connection to database');



