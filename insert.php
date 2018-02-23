
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
//
// Variables

$year = $_REQUEST['year'];
$comment = $_REQUEST['comment'];
$strain = $_REQUEST['strain'];
$backbone = $_REQUEST['backbone'];
$reg_id = $_REQUEST['registry'];
$current_date = date("Y-m-d");

$creator = 1;  
$creator = $_POST['user_id']; 


$file = "";


// Strain 
$check = "SELECT name FROM strain WHERE name LIKE '$strain'";
$check_query = mysqli_query($link, $check);
if (mysqli_num_rows($check_query) < 1) {
    $sql_strain = "INSERT INTO strain (name) VALUES (?)";
    $stmt_strain = $link->prepare($sql_strain);
    $stmt_strain->bind_param("s", $strain);
    $stmt_strain->execute();
    $stmt_strain->close();
} else {

    $strain_id = "INSERT INTO entry (strain) VALUES (?)";
    $stmt_strainid = $link->prepare($strain_id);
    $stmt_strainid->bind_param("i", $strain_row_id);
    $stmt_strainid->execute();
    $stmt_strainid->close();
}

$strain_s = "SELECT id FROM strain WHERE name LIKE '$strain'";
$strain_s_query = mysqli_query($link, $strain_s);
$strain_row = mysqli_fetch_assoc($strain_s_query);
$strain_row_id = $strain_row["id"];


// Backbone
$check2 = "SELECT name FROM backbone WHERE name LIKE '$backbone'";
$check_query2 = mysqli_query($link, $check2);
if (mysqli_num_rows($check_query2) < 1) {
    $sql_backbone = "INSERT INTO backbone (name, date_db) VALUES (?,?)";
    $stmt_backbone = $link->prepare($sql_backbone);
    $stmt_backbone->bind_param("ss", $backbone, $current_date);
    $stmt_backbone->execute();
    $stmt_backbone->close();
} else {
    $back_id = "INSERT INTO entry (backbone) VALUES (?)";
    $stmt_backid = $link->prepare($back_id);
    $stmt_backid->bind_param("i", $back_row_id);
    $stmt_backid->execute();
    $stmt_backid->close();
}

$back_s = "SELECT id FROM backbone WHERE name LIKE '$backbone'";
$back_s_query = mysqli_query($link, $back_s);
$back_row = mysqli_fetch_assoc($back_s_query);
$back_row_id = $back_row["id"];

// Entry
$sql_entry = "INSERT INTO entry (year_created, comment, date_db, entry_reg, backbone, strain) VALUES (?,?,?,?,?,?)";
$stmt_entry = $link->prepare($sql_entry);
$stmt_entry->bind_param("isssii", $year, $comment, $current_date, $reg_id, $back_row_id, $strain_row_id);
$stmt_entry->execute(); 
$stmt_entry->close();

$entry_s_id = "SELECT * FROM entry ORDER BY id DESC LIMIT 1";
$entry_id_query = mysqli_query($link, $entry_s_id);
$entry_id_row = mysqli_fetch_assoc($entry_id_query); 
$entry_id = $entry_id_row["id"]; 
$up_id = "UU".date("Y").$entry_id; 

// Insert
$ins_name = $_POST["ins"];
$ins_type = $_POST["insert_type"];
$num = count($ins_name);

if ($num > 0) {
    for ($i = 0; $i < $num; $i++) {
            $ins_s = "SELECT id from ins WHERE name LIKE '$ins_name[$i]'";
            $ins_s_query = mysqli_query($link, $ins_s);
            $ins_row = mysqli_fetch_assoc($ins_s_query);
            $ins_id = $ins_row["id"];

            $entry_ins = "INSERT INTO entry_inserts (entry_id, insert_id) VALUES(?,?)";
            $stmt_entry_ins = $link->prepare($entry_ins);
            $stmt_entry_ins->bind_param("ii", $entry_id, $ins_id);
            $stmt_entry_ins->execute();
            $stmt_entry_ins->close();
            
        if (trim($ins_name[$i] != '')) {
            $check3 = "SELECT name FROM ins WHERE name LIKE '$ins_name[$i]'";
            $check_query3 = mysqli_query($link, $check3);
            if (mysqli_num_rows($check_query3) < 1) {
                
                $ins_type_s = "SELECT id FROM ins_type WHERE name LIKE '$ins_type[$i]'"; 
                $ins_type_s_query = mysqli_query($link, $ins_type_s);
                $ins_type_row = mysqli_fetch_assoc($ins_type_s_query);
                $ins_type_id = $ins_type_row["id"];
                //echo $ins_type_id; 
                
                $sql_ins = "INSERT INTO ins (name,date_db,type) VALUES (?,?,?)";
                $stmt_ins = $link->prepare($sql_ins);
                $stmt_ins->bind_param("sss", $ins_name[$i], $current_date, $ins_type_id);
                $stmt_ins->execute();
                $stmt_ins->close();
                
            }   
        }
    }
} else {
}

//Sequence
$org_name_file = $_FILES['file']['name']; 
$sql_file = "INSERT INTO upstrain_file (name_original) VALUES(?)"; 
$stmt_file = $link->prepare($sql_file); 
$stmt_file->bind_param("s", $org_name_file); 
$stmt_file->execute(); 
$stmt_file->close(); 

if (is_uploaded_file($_FILES['file']['tmp_name']) && $_FILES['file']['error'] == 0) {
$path = "files/".up_id;  
      if (!file_exists($path)) {
          if(move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
             echo "The file was uploaded successfully. ";  
          } else {
              echo "The file was not uploaded successfully"; 
          }
      } else {
          echo "File already esists. Please upload another file."; 
      }
} 
else {
    echo "(Error Code: ". $_FILES['file']['error']. ")"; 
} 

//Private


$link->close() or die('Could not close connection to database');



