<?php

if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    include 'scripts/db.php';

    function test_input($string) {
        return htmlspecialchars(strip_tags(stripslashes(trim($string))));
    }

//Variables
    $strain = mysqli_real_escape_string($link, test_input($_POST['strain_name']));
    $backbone = mysqli_real_escape_string($link, test_input($_POST['backbone_name']));
    $year = mysqli_real_escape_string($link, test_input($_POST['year']));
    $reg_id = mysqli_real_escape_string($link, test_input($_POST['registry']));
    $comment = mysqli_real_escape_string($link, test_input($_POST['comment']));
    $ins = $_POST['ins'];
    $num = count($ins);
    $current_date = date("Y-m-d");
    $creator = $_SESSION['user_id'];
    $private = 0;
    $created = 0;

//Fetch strain id 
    $strain_s = "SELECT id FROM strain WHERE name LIKE '$strain'";
    $strain_s_query = mysqli_query($link, $strain_s);
    $strain_row = mysqli_fetch_assoc($strain_s_query);
    $strain_row_id = $strain_row["id"];

//Fetch backbone id 
    $back_s = "SELECT id FROM backbone WHERE name LIKE '$backbone'";
    $back_s_query = mysqli_query($link, $back_s);
    $back_row = mysqli_fetch_assoc($back_s_query);
    $back_row_id = $back_row["id"];

    if (isset($_POST['private'])) {
        $private = intval(test_input($_POST['private']));
    }
    if (isset($_POST['created'])) {
        $created = intval(test_input($_POST['created']));
    }

    if (isset($strain) && isset($backbone) && isset($year) && isset($comment)) {
// Insert entry information
        $sql_entry = "INSERT INTO entry (year_created, comment, date_db, entry_reg, "
                . "backbone, strain, creator, private, created)"
                . " VALUES (?,?,?,?,?,?,?,?,?)";
        if ($stmt_entry = $link->prepare($sql_entry)) {
            if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                if ($stmt_entry->bind_param("isssiiiii", $year, $comment, $current_date, $reg_id, $back_row_id, $strain_row_id, $creator, $private, $created)) {
                    if ($stmt_entry->execute()) {
// Entry id
                        $entry_s_id = "SELECT * FROM entry ORDER BY id DESC LIMIT 1";
                        $entry_id_query = mysqli_query($link, $entry_s_id);
                        $entry_id_row = mysqli_fetch_assoc($entry_id_query);
                        $entry_id = $entry_id_row["id"];

// Insert insert id and entry id into entry_inserts
                        if ($num > 0) {
                            $position = 0;
                            for ($i = 0; $i < $num; $i++) {
                                if (trim($ins[$i]) != '') {
                                    $position++;
                                    $entry_ins = "INSERT INTO entry_inserts (entry_id, insert_id, position) "
                                            . "VALUES(?,?,?)";
                                    if ($stmt_entry_ins = $link->prepare($entry_ins)) {
                                        if ($stmt_entry_ins->bind_param("iii", $entry_id, $ins[$i], $position)) {
                                            if ($stmt_entry_ins->execute()) {
                                                
                                            } else {
                                                $_SESSION['error'] .= "<div class = 'error'>Execute failed: (" . $stmt_entry_ins->errno . ")" . " " . "Error: " . $stmt_entry_ins->error . "</div>";
                                            } $stmt_entry_ins->close();
                                        } else {
                                            $_SESSION['error'] .= "<div class = 'error'>Binding parameters failed: (" . $stmt_entry_ins->errno . ")" . " " . "Error: " . $stmt_entry_ins->error . "</div>";
                                        }
                                    } else {
                                        $_SESSION['error'] .= "<div class = 'error'>Prepare failed: (" . $link->errno . ")" . " " . "Error: " . $link->error . "</div>";
                                    }
                                }
                            }
                        }

//Select upstrain id from the most recent entry 
                        $year_created_s = "SELECT upstrain_id FROM entry_upstrain WHERE entry_id = $entry_id";
                        $year_created_query = mysqli_query($link, $year_created_s);
                        $year_created_row = mysqli_fetch_assoc($year_created_query);
                        $upstrain_id = $year_created_row["upstrain_id"];


//Check if a sequence filed is uploaded and if it is in nucleotide fasta format
                        if (is_uploaded_file($_FILES['my_file']['tmp_name']) && $_FILES['my_file']['error'] == 0) {
                            $path = "files/" . $upstrain_id . ".fasta";
                            $lines = file($_FILES['my_file']['tmp_name']);
                            $header = $lines[0];
                            $firstc = $header[0];
                            $num_lines = count($lines);
                            $seq = "";
                            for ($i = 1; $i < $num_lines; $i++) {
                                $seq .= $lines[$i];
                            }
                            if ($firstc == '>' && preg_match("/^[ATCGatcg*\-\s]+$/", $seq)) {
                                if (!file_exists($path)) {
                                    if (move_uploaded_file($_FILES['my_file']['tmp_name'], $path)) {
                                        $org_name_file = $_FILES['my_file']['name'];
                                        $sql_file = "INSERT INTO upstrain_file (name_original, upstrain_id) VALUES(?,?)";
                                        $stmt_file = $link->prepare($sql_file);
                                        $stmt_file->bind_param("ss", $org_name_file, $upstrain_id);
                                        $stmt_file->execute();
                                        $stmt_file->close();
                                        $_SESSION['success'] .= "<div class = 'success'>File uploaded successfully</div>";
                                    } else {
                                        $_SESSION['error'] .= "<div class = 'error'>The file was not uploaded successfully</div>";
                                        mysqli_close($link) or die("Could not close database connection");
                                        header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "new_insert.php?content=new_entry");
                                        exit();
                                    }
                                } else {
                                    $_SESSION['error'] .= "<div class = 'error'>File already exists. Please upload another file</div>";
                                    mysqli_close($link) or die("Could not close database connection");
                                    header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "new_insert.php?content=new_entry");
                                    exit();
                                }
                            } else {
                                $_SESSION['error'] .= "<div class = 'error'>The input file has an invalid format. Please only upload nucleotide fasta files</div>";
                                mysqli_close($link) or die("Could not close database connection");
                                header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "new_insert.php?content=new_entry");
                                exit();
                            }
                        }

                        $_SESSION['success'] .= "<div class = 'success'>New entry submitted successfully</div>";
                        mysqli_close($link) or die("Could not close database connection");
                        header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "new_insert.php?content=new_entry");
                        exit();
                    } else {
                        $_SESSION['error_insert'] .= "<div class = 'error'>Execute failed: (" . $stmt_entry->errno . ")" .
                                " " . "Error: " . $stmt_entry->error . "</div>";
                        mysqli_close($link) or die("Could not close database connection");
                        header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "new_insert.php?content=new_entry");
                        exit();
                    } $stmt_entry->close();
                } else {
                    $_SESSION['error'] .= "<div class = 'error'>Binding parameters failed: (" . $stmt_entry->errno .
                            ")" . " " . "Error: " . $stmt_entry->error . "</div>";
                    mysqli_close($link) or die("Could not close database connection");
                    header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "new_insert.php?content=new_entry");
                    exit();
                }
            }
        } else {
            $_SESSION['error'] .= "<div class = 'error'>Prepare failed: (" . $link->errno
                    . ")" . " " . "Error: " . $link->error . "</div>";
            mysqli_close($link) or die("Could not close database connection");
            header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "new_insert.php?content=new_entry");
            exit();
        }
    }
}