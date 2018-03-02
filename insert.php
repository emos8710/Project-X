
<?php

if (count(get_included_files()) == 1)
    exit("Access restricted");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    include 'scripts/db.php';

    $iserror = FALSE;

//Variables
    $strain = mysqli_real_escape_string($link, $_REQUEST['strain_namw']);
    $backbone = mysqli_real_escape_string($link, $_REQUEST['backbone_name']);
    $year = mysqli_real_escape_string($link, $_REQUEST['year']);
    $reg_id = mysqli_real_escape_string($link, $_REQUEST['registry']);
    $comment = mysqli_real_escape_string($link, $_REQUEST['comment']);
    $current_date = date("Y-m-d");
    $creator = $_SESSION['user_id'];


    $ins_name = $_POST["ins"];
    $ins_type = $_POST["insert_type"];

//Fetch strain id from database
    $strain_s = "SELECT id FROM strain WHERE name LIKE '$strain'";
    $strain_s_query = mysqli_query($link, $strain_s);
    $strain_row = mysqli_fetch_assoc($strain_s_query);
    $strain_row_id = $strain_row["id"];

//Fetch backbone id from database
    $back_s = "SELECT id FROM backbone WHERE name LIKE '$backbone'";
    $back_s_query = mysqli_query($link, $back_s);
    $back_row = mysqli_fetch_assoc($back_s_query);
    $back_row_id = $back_row["id"];

// Insert entry information into database
    $sql_entry = "INSERT INTO entry (year_created, comment, date_db, entry_reg, backbone, strain,creator) VALUES (?,?,?,?,?,?,?)";
    $stmt_entry = $link->prepare($sql_entry);
    $stmt_entry->bind_param("isssiii", $year, $comment, $current_date, $reg_id, $back_row_id, $strain_row_id, $creator);
    $stmt_entry->execute();
    $stmt_entry->close();

//Select upstrainid from the most recent entry 
    $entry_s_id = "SELECT * FROM entry ORDER BY id DESC LIMIT 1";
    $entry_id_query = mysqli_query($link, $entry_s_id);
    $entry_id_row = mysqli_fetch_assoc($entry_id_query);
    $entry_id = $entry_id_row["id"];

    $year_created_s = "SELECT upstrain_id FROM entry_upstrain WHERE entry_id = $entry_id";
    $year_created_query = mysqli_query($link, $year_created_s);
    $year_created_row = mysqli_fetch_assoc($year_created_query);
    $upstrain_id = $year_created_row["upstrain_id"];


//Private

    if (isset($_POST['private']) && $_POST['private'] == 'Private') {
        
    }


//Created 

    if (isset($_POST['created']) && $_POST['created'] == 'Created') {
        
    }



// Insert
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


                    $sql_ins = "INSERT INTO ins (name,date_db,type,creator) VALUES (?,?,?,?)";
                    $stmt_ins = $link->prepare($sql_ins);
                    $stmt_ins->bind_param("sssi", $ins_name[$i], $current_date, $ins_type_id, $creator);
                    $stmt_ins->execute();
                    $stmt_ins->close();
                }
            }
        }
    } else {
        
    }

//Sequence

    if (is_uploaded_file($_FILES['my_file']['tmp_name']) && $_FILES['my_file']['error'] == 0) {
        $path = "files/" . $upstrain_id;
        $lines = file($_FILES['my_file']['tmp_name']);
        $header = $lines[0];
        $firstc = $header[0];
        $num_lines = count($lines);
        $seq = "";
        $msg = "";
        for ($i = 1; $i < $num_lines; $i++) {
            $seq .= $lines[$i];
        }

        if ($firstc == '>' && preg_match("/^[[ATCG]\*\-\s]+$/", $seq)) {
            if (!file_exists($path)) {
                if (move_uploaded_file($_FILES['my_file']['tmp_name'], $path)) {
                    $org_name_file = $_FILES['my_file']['name'];
                    $sql_file = "INSERT INTO upstrain_file (name_original, upstrain_id) VALUES(?,?)";
                    $stmt_file = $link->prepare($sql_file);
                    $stmt_file->bind_param("ss", $org_name_file, $upstrain_id);
                    $stmt_file->execute();
                    $stmt_file->close();
                    $msg = "The file was uploaded successfully";
                } else {
                    $msg = "The file was not uploaded successfully";
                }
            } else {
                $msg = "File already exists. Please upload another file.";
            }
        } else {
            
        }
    } else {
        $msg = "(Error Code: " . $_FILES['my_file']['error'] . ")";
    }
}