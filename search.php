<?php
if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
    session_start();
}

$title = "Search";

function insertlink($insertnames, $insertids) {

    $namearray = explode(", ", $insertnames);
    $idarray = explode(", ", $insertids);

    foreach ($namearray as $key => $arr) {
        $nameshtmlfriendly .= '<a href="parts.php?ins_id=' . $idarray[$key] . '">' . $arr;
        $nameshtmlfriendly .= '</a>, ';
    }
    return ($nameshtmlfriendly);
}

//Set display for the content div
if (isset($_GET['content'])) {
    $current_content = $_GET['content'];
} else {
    $current_content = '';
}

include 'top.php';
?>

<main>
    <div class="innertube">
        <h2>Search</h2>
        <!-- Nav menu with links to display desired content -->

        <div class="search_nav">

            <ul class="search_nav">
                <li><a <?php
if (isset($_GET['content']) && $_GET['content'] === "search_entries") {
    echo "class=\"active\"";
}
?> href="?content=search_entries">Search for entries</a></li>
                <li><a <?php
                    if (isset($_GET['content']) && $_GET['content'] === "search_users") {
                        echo "class=\"active\"";
                    }
                    ?> href="?content=search_users">Search for users</a></li>
                <li><a <?php
                    if (isset($_GET['content']) && $_GET['content'] === "search_inserts") {
                        echo "class=\"active\"";
                    }
                    ?> href="?content=search_inserts">Search for inserts</a></li>
            </ul>


            <br>
            <br>

            <!-- Checks the current content -->
<?php if ($current_content == "search_entries") {
    ?>
                <!-- Form specification -->
                <form class="search-form" action="search.php?content=search_entries" method="post" id="searchform">
                    <div>
                        <div class="top-row">
                            <div class="field-wrap">
                                <label>Upstrain ID</label>
                                <input class="all" type="text" name="id_criteria" placeholder="UUYYYYXXX" pattern = "UU\d{7,10}" title="Upstrain ID must match pattern UUYYYYXXX."/>
                            </div>

                            <div class="field-wrap">
                                <label>Strain</label>
                                <input class="all" type="text" name="strain_criteria"/>
                            </div>
                        </div>

                        <div class="top-row">
                            <div class="field-wrap">
                                <label>Insert</label>
                                <input class="all" type="text" name="insert_criteria"/>
                            </div>

                            <div class="field-wrap">
                                <label>Year created</label>
                                <input class="all" type="text" name="creation_year_criteria" minlength= "4" maxlengh= "4" pattern = "(?:19|20)[0-9]{2}" 
                                       placeholder="YYYY" title ="Must contain four digits for year."/>
                            </div>
                        </div>

                        <div class="top-row">
                            <div class="field-wrap">
                                <label>Creator</label>
                                <input class="all" type="text" name="creator_criteria" placeholder=""/>
                            </div>

                            <div class="field-wrap">
                                <label>Biobrick registry ID</label>
                                <input class="all" type="text" name="bb_id_criteria" placeholder="BBa_K[X]" pattern="BBa_K\d{4,12}" title ="Biobrick ID must match pattern BBa_KXXXXX."/>
                            </div>
                        </div>

                        <div class="top-row">
                            <div class="field-wrap">
                                <label>Backbone</label>
                                <input class="all" type="text" name="backbone_criteria"/>
                            </div>

                            <div class="field-wrap">
                                <label>Insert Type</label>
                                <select class="all" name="insert_type">
                                    <option value=""></option>    
                                    <option value="promotor">Promotor</option>
                                    <option value="coding">Coding</option>
                                    <option value="RBS">RBS</option>
                                </select>    
                            </div>
                        </div>

                        <div class="field-wrap">
                            <label>Date inserted</label>
                            <input class="all" type="date" name="inserted_date_criteria" pattern = "((?:19|20)[0-9]{2})-([0-9]{2})-([0-9]{2})" 
                                   placeholder="YYYY-MM-DD" title="Must match date pattern YYYY-MM-DD"/>
                        </div>

                        <div class="field-wrap">
                            <label>Comment</label>
                            <input class="all" type="text" name="comment_criteria" rows ="4" cols="50"/>
                        </div>

                            <!-- <input name="submit-form" value="Search" type="submit"> -->
                        <button type="submit" class="button" name="search" />Search</button>
                    </div>

                </form>
            </div>

    <?php
    include 'scripts/db.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $id_criteria = mysqli_real_escape_string($link, $_REQUEST['id_criteria']);
        $strain_criteria = mysqli_real_escape_string($link, $_REQUEST['strain_criteria']);
        $backbone_criteria = mysqli_real_escape_string($link, $_REQUEST['backbone_criteria']);
        $insert_criteria = mysqli_real_escape_string($link, $_REQUEST['insert_criteria']);
        $bb_id_criteria = mysqli_real_escape_string($link, $_REQUEST['bb_id_criteria']);
        $comment_criteria = mysqli_real_escape_string($link, $_REQUEST['comment_criteria']);
        $creation_year_criteria = mysqli_real_escape_string($link, $_REQUEST['creation_year_criteria']);
        $inserted_date_criteria = mysqli_real_escape_string($link, $_REQUEST['inserted_date_criteria']);
        $creator_criteria = mysqli_real_escape_string($link, $_REQUEST['creator_criteria']);
        $insert_type_criteria = mysqli_real_escape_string($link, $_REQUEST['insert_type']);


        $ConditionArray = [];
        $ischarvalid = TRUE;

        if (!empty($id_criteria)) {
            if (!preg_match('/[^A-Za-z0-9]/', $id_criteria)) {
                $ConditionArray[] = "t9.upstrain_id = '$id_criteria'";
            } else {
                $ischarvalid = FALSE;
                echo nl2br("\n \n Error: Non-valid character usage for 'ID'.");
            }
        }

        if (!empty($strain_criteria)) {
            $ConditionArray[] = "t4.name like '$strain_criteria'";
        }

        if (!empty($backbone_criteria)) {
            if (!preg_match('/[^A-Za-z0-9]/', $backbone_criteria)) {
                $ConditionArray[] = "t3.name = '$backbone_criteria'";
            } else {
                $ischarvalid = FALSE;
                echo nl2br("\n \n Error: Non-valid character usage for 'Backbone'.");
            }
        }

        if (!empty($insert_criteria)) {
            $ConditionArray[] = "(t9.entry_id IN (SELECT entry_inserts.entry_id FROM "
                    . "entry_inserts WHERE entry_inserts.insert_id IN (SELECT ins.id FROM "
                    . "ins WHERE (LOCATE('$insert_criteria', ins.name) > 0))))";
        }

        if (!empty($bb_id_criteria)) {
            if (!preg_match('/[^A-Za-z0-9_]/', $bb_id_criteria)) {
                $ConditionArray[] = "t1.entry_reg = '$bb_id_criteria'";
            } else {
                $ischarvalid = FALSE;
                echo nl2br("\n \n Error: Non-valid character usage for 'Biobrick registry ID'.");
            }
        }

        if (!empty($comment_criteria)) {
            $ConditionArray[] = "LOCATE('$comment_criteria', t1.comment) > 0";
        }

        if (!empty($creation_year_criteria)) {
            if (is_numeric($creation_year_criteria)) {
                $ConditionArray[] = "t1.year_created = $creation_year_criteria";
            } else {
                $ischarvalid = FALSE;
                echo nl2br("\n \n Error: Non-valid character usage for 'Year created'.");
            }
        }

        if (!empty($inserted_date_criteria)) {
            if (!preg_match('/^(?:19|20)[0-9]{2})-([0-9]{2})-([0-9]{2})/', $inserted_date_criteria)) {
                $ConditionArray[] = "t1.date_db = '$inserted_date_criteria'";
            } else {
                $ischarvalid = FALSE;
                echo nl2br("\n \n Error: Non-valid character usage for 'Date inserted'.");
            }
        }

        if (!empty($creator_criteria)) {
            $ConditionArray[] = "(t2.first_name = '$creator_criteria' OR "
                    . "t2.last_name = '$creator_criteria' OR "
                    . "t2.username = '$creator_criteria' OR "
                    . "(CONCAT(t2.first_name,' ', t2.last_name) = '$creator_criteria'))";
        }

        if (!empty($insert_type_criteria)) {
            $ConditionArray[] = "(t9.entry_id IN (SELECT entry_inserts.entry_id FROM "
                    . "entry_inserts WHERE entry_inserts.insert_id IN "
                    . "(SELECT ins.id FROM ins WHERE ins.type IN "
                    . "(SELECT ins_type.id FROM ins_type WHERE "
                    . "(ins_type.name = '$insert_type_criteria')))))";
        }


        $entrysql = "SELECT DISTINCT t1.comment AS cmt, t1.year_created AS year, "
                . "t1.date_db AS date, t1.entry_reg AS biobrick, t1.private AS private, "
                . "t4.name AS strain, t4.id AS strain_id, "
                . "GROUP_CONCAT(DISTINCT t6.name SEPARATOR ', ') AS insname, GROUP_CONCAT(DISTINCT t6.id SEPARATOR ', ') AS ins_id, "
                . "t3.name AS backbone, t3.id AS backbone_id, "
                . "t2.user_id AS user_id, t2.username AS uname, "
                . "GROUP_CONCAT(DISTINCT t7.name SEPARATOR ', ') AS instype, "
                . "t9.upstrain_id AS up_id "
                . "FROM (entry AS t1) "
                . "LEFT JOIN entry_inserts AS t5 ON t5.entry_id = t1.id "
                . "LEFT JOIN ins AS t6 ON t6.id = t5.insert_id "
                . "LEFT JOIN ins_type AS t7 ON t7.id = t6.type "
                . "LEFT JOIN users AS t2 ON t1.creator = t2.user_id "
                . "LEFT JOIN backbone AS t3 ON t1.backbone = t3.id "
                . "LEFT JOIN strain AS t4 ON t1.strain = t4.id "
                . "LEFT JOIN entry_upstrain AS t9 ON t1.id = t9.entry_id "
                . "WHERE ";

        $sql = "";
        $result = "";
        $iserror = FALSE;

        $num_result_rows = 0;

        // If there are results, show them
        if (count($ConditionArray) > 0) {
            $sql = $entrysql . implode(' AND ', $ConditionArray) . " GROUP BY up_id";
            $result = mysqli_query($link, $sql);
            if (!$result) {
                echo mysqli_error($link);
            }
            $num_result_rows = mysqli_num_rows($result);
        } else if ($ischarvalid && count($ConditionArray) == 0) {
            echo nl2br("\n Error: Please enter search query");
        }
        if ($num_result_rows > 0) {
            echo "<table>";
            echo "<tr><th>UpStrain ID</th><th>Strain</th><th>Backbone</th>"
            . "<th>Insert</th><th>Insert Type</th><th>Year</th><th>iGEM Registry</th>"
            . "<th>Creator</th><th>Added date</th><th>Comment</th></tr>";

            // output data of each row
            while ($row = $result->fetch_assoc()) {
                $biobrick = "";
                $insert_links = insertlink($row["insname"], $row["ins_id"]);
                if ($row["biobrick"] === null || $row["biobrick"] == '') {
                    $biobrick = "N/A";
                } else {
                    $biobrick = "<a class=\"external\" href=\"http://parts.igem.org/Part:" . $row["biobrick"] . "\" target=\"_blank\">" . $row["biobrick"] . "</a>";
                }
                if (!$loggedin && $row["private"] == 1) {
                    
                } else {
                    echo "<tr><td><a href=\"entry.php?upstrain_id=" . $row["up_id"] . "\">" . $row["up_id"] . "</a>" .
                    "</td><td><a href=\"parts.php?strain_id=" . $row["strain_id"] . "\">" . $row["strain"] . "</a>" .
                    "</td><td><a href=\"parts.php?backbone_id=" . $row["backbone_id"] . "\">" . $row["backbone"] . "</a>" .
                    "</td><td>" . $insert_links .
                    //"</td><td><a href=\"parts.php?ins_id=" . $row["ins_id"] . "\">" . $row["insname"] . "</a>" .
                    "</td><td>" . $row["instype"] .
                    "</td><td>" . $row["year"] .
                    "</td><td>" . $biobrick .
                    "</td><td>" . "<a href=\"user.php?user_id=" . $row["user_id"] . "\">" .
                    $row["uname"] . "</td><td>" . $row["date"] .
                    "</td><td class=\"comment\">" . $row["cmt"] . "</td></tr>";
                }
            }
            echo "</table>";
        }
        // If there are no rows, create error
        else {
            $iserror = TRUE;
            $error = "No matching results, try another search.";
        }
        // Show errors
        if ($iserror && !empty($ConditionArray)) {
            echo "<h6> Error: " . $error . "</h6>";
        }

        mysqli_close($link) or die("Could not close database connection");
    }
} else if ($current_content == "search_users") {
    ?>
            <form class="search-form" action="search.php?content=search_users" method="post" id="searchform">
                <div>
                    <div class="top-row">
                        <div class="field-wrap">
                            <label>Username</label>
                            <input class="all" type="text" name="uname_criteria"/>
                        </div>

                        <div class="field-wrap">
                            <label>First name</label>
                            <input class="all" type="text" name="fname_criteria"/>
                        </div>
                    </div>
                    <div class="top-row">
                        <div class="field-wrap">
                            <label>Last name</label>
                            <input class="all" type="text" name="lname_criteria"/>
                        </div>

                        <div class="field-wrap">
                            <label>User ID</label>
                            <input class="all" type="text" name="id_criteria"  pattern = "[0-9]{1,12}" 
                                   title ="Must contain only digits."/>
                        </div>
                    </div>
            <!-- <input name="submit-form" value="Search" type="submit"> -->
                    <button type="submit" class="button" name="search" />Search</button>
                </div>

            </form>

    <?php
    include 'scripts/db.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $uname_criteria = mysqli_real_escape_string($link, $_REQUEST['uname_criteria']);
        $fname_criteria = mysqli_real_escape_string($link, $_REQUEST['fname_criteria']);
        $lname_criteria = mysqli_real_escape_string($link, $_REQUEST['lname_criteria']);
        $id_criteria = mysqli_real_escape_string($link, $_REQUEST['id_criteria']);



        $ConditionArray = [];
        $ischarvalid = TRUE;

        if (!empty($uname_criteria)) {
            if (!preg_match('/[^A-Za-z0-9]/', $uname_criteria)) {
                $ConditionArray[] = "t1.username like '$uname_criteria'";
            } else {
                $ischarvalid = FALSE;
                echo nl2br("\n \n Error: Non-valid character usage for 'Username'.");
            }
        }


        if (!empty($fname_criteria)) {
            if (!preg_match('/[^A-Za-z]/', $fname_criteria)) {
                $ConditionArray[] = "t1.first_name = '$fname_criteria'";
            } else {
                $ischarvalid = FALSE;
                echo nl2br("\n \n Error: Non-valid character usage for 'First Name'.");
            }
        }


        if (!empty($lname_criteria)) {
            if (!preg_match('/[^A-Za-z]/', $lname_criteria)) {
                $ConditionArray[] = "t1.last_name = '$lname_criteria'";
            } else {
                $ischarvalid = FALSE;
                echo nl2br("\n \n Error: Non-valid character usage for 'Last Name'.");
            }
        }


        if (!empty($id_criteria)) {
            if (is_numeric($id_criteria)) {
                $ConditionArray[] = "t1.user_id = $id_criteria";
            } else {
                $ischarvalid = FALSE;
                echo nl2br("\n \nError: Non-valid character usage for 'User ID'.");
            }
        }



        $usersql = "SELECT DISTINCT t1.username AS uname, t1.first_name AS fname, "
                . "t1.last_name AS lname, t1.user_id AS user_id, t1.phone AS phone, "
                . "t1.email AS email FROM (users AS t1) "
                . "WHERE ";

        $sql = "";
        $result = "";
        $iserror = FALSE;

        $num_result_rows = 0;

        // If there are results, show them
        if (count($ConditionArray) > 0) {
            $sql = $usersql . implode(' AND ', $ConditionArray) . " GROUP BY user_id";
            $result = mysqli_query($link, $sql);
            if (!$result) {
                echo mysqli_error($link);
            }
            $num_result_rows = mysqli_num_rows($result);
        } else if ($ischarvalid && count($ConditionArray) == 0) {
            echo nl2br("\n Error: Please enter search query");
        }
        if ($num_result_rows > 0) {
            echo "<table>";
            echo "<tr><th>User ID</th><th>Username</th><th>First Name</th>"
            . "<th>Last Name</th><th>Phone number</th><th>Email address</th></tr>";

            // output data of each row
            while ($row = $result->fetch_assoc()) {

                echo "<tr><td>" . $row["user_id"] .
                "</td><td><a href=\"user.php?user_id=" . $row["user_id"] . "\">" . $row["uname"] . "</a>" .
                "</td><td>" . $row["fname"] .
                "</td><td>" . $row["lname"] .
                "</td><td>" . $row["phone"] .
                "</td><td>" . $row["email"] .
                "</td></tr>";
            }

            echo "</table>";
        }
        // If there are no rows, create error
        else {
            $iserror = TRUE;
            $error = "No matching results, try another search.";
        }
        // Show errors
        if ($iserror && !empty($ConditionArray)) {
            echo "<h4> Error: " . $error . "</h4>";
        }

        mysqli_close($link) or die("Could not close database connection");
    }
} else if ($current_content == "search_inserts") {
    ?>
            <form class="search-form" action="search.php?content=search_inserts" method="post" id="searchform">
                <div>
                    <div class="top-row">
                        <div class="field-wrap">
                            <label>Insert ID</label>
                            <input class="all" type="text" name="id_criteria" pattern = "[0-9]{1,12}" 
                                   title="Insert ID must only contain digits."/>
                        </div>

                        <div class="field-wrap">
                            <label>Insert name</label>
                            <input class="all" type="text" name="name_criteria"/>
                        </div>
                    </div>

                    <div class="top-row">				
                        <div class="field-wrap">
                            <label>Creator</label>
                            <input class="all" type="text" name="creator_criteria"/>
                        </div>

                        <div class="field-wrap">
                            <label>Biobrick registry ID</label>
                            <input class="all" type="text" name="bb_id_criteria" placeholder="BBa_K[X]" 
                                   pattern="BBa_K\d{4,12}" title ="Biobrick ID must match pattern BBa_KXXXXX."/>
                        </div>
                    </div>

                    <div class="top-row">				
                        <div class="field-wrap">
                            <label>Insert Type</label>
                            <select class="all" name="insert_type_criteria">
                                <option value=""></option>    
                                <option value="promotor">Promotor</option>
                                <option value="coding">Coding</option>
                            </select>    
                        </div>

                        <div class="field-wrap">
                            <label>Date inserted</label>
                            <input class="all" type="date" name="inserted_date_criteria" pattern = "((?:19|20)[0-9]{2})-([0-9]{2})-([0-9]{2})" 
                                   placeholder="YYYY-MM-DD" title="Must match date pattern YYYY-MM-DD"/>
                        </div>
                    </div>

                    <div class="field-wrap">
                        <label>Comment</label>
                        <input class="all" type="text" name="comment_criteria" rows ="4" cols="50"/>
                    </div>

                            <!-- <input name="submit-form" value="Search" type="submit"> -->
                    <button type="submit" class="button" name="search" />Search</button>
                </div>

            </form>

    <?php
    include 'scripts/db.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $id_criteria = mysqli_real_escape_string($link, $_REQUEST['id_criteria']);
        $name_criteria = mysqli_real_escape_string($link, $_REQUEST['name_criteria']);
        $bb_id_criteria = mysqli_real_escape_string($link, $_REQUEST['bb_id_criteria']);
        $comment_criteria = mysqli_real_escape_string($link, $_REQUEST['comment_criteria']);
        $inserted_date_criteria = mysqli_real_escape_string($link, $_REQUEST['inserted_date_criteria']);
        $creator_criteria = mysqli_real_escape_string($link, $_REQUEST['creator_criteria']);
        $insert_type_criteria = mysqli_real_escape_string($link, $_REQUEST['insert_type_criteria']);



        $ConditionArray = [];
        $ischarvalid = TRUE;


        if (!empty($id_criteria)) {
            if (!preg_match('/[^0-9]/', $id_criteria)) {
                $ConditionArray[] = "t1.id = '$id_criteria'";
            } else {
                $ischarvalid = FALSE;
                echo nl2br("\n \n Error: Non-valid character usage for 'ID'.");
            }
        }

        if (!empty($name_criteria)) {
            $ConditionArray[] = "LOCATE('$name_criteria', t1.name) > 0";
        }


        if (!empty($bb_id_criteria)) {
            if (!preg_match('/[^A-Za-z0-9_]/', $bb_id_criteria)) {
                $ConditionArray[] = "t1.ins_reg = '$bb_id_criteria'";
            } else {
                $ischarvalid = FALSE;
                echo nl2br("\n \n Error: Non-valid character usage for 'Biobrick registry ID'.");
            }
        }

        if (!empty($comment_criteria)) {
            $ConditionArray[] = "LOCATE('$comment_criteria', t1.comment) > 0";
        }


        if (!empty($inserted_date_criteria)) {
            if (!preg_match('/^(?:19|20)[0-9]{2})-([0-9]{2})-([0-9]{2})/', $inserted_date_criteria)) {
                $ConditionArray[] = "t1.date_db = '$inserted_date_criteria'";
            } else {
                $ischarvalid = FALSE;
                echo nl2br("\n \n Error: Non-valid character usage for 'Date inserted'.");
            }
        }

        if (!empty($creator_criteria)) {
            $ConditionArray[] = "(t2.first_name = '$creator_criteria' OR "
                    . "t2.last_name = '$creator_criteria' OR "
                    . "t2.username = '$creator_criteria' OR "
                    . "(CONCAT(t2.first_name,' ', t2.last_name) = '$creator_criteria'))";
        }

        if (!empty($insert_type_criteria)) {
            $ConditionArray[] = "(t1.type IN (SELECT ins_type.id FROM "
                    . "ins_type WHERE ins_type.name = '$insert_type_criteria'))";
        }



        $insertsql = "SELECT DISTINCT t1.id AS ins_id, t1.name AS insname, "
                . "t1.ins_reg AS ins_reg, t1.date_db AS date_db, t1.private AS private, "
                . "t2.username AS creator_name, t2.user_id AS user_id, "
                . "t3.name AS ins_type FROM (ins AS t1) "
                . "LEFT JOIN users AS t2 ON t2.user_id = t1.creator "
                . "LEFT JOIN ins_type AS t3 ON t3.id = t1.type "
                . "WHERE ";

        $sql = "";
        $result = "";
        $iserror = FALSE;

        $num_result_rows = 0;

        // If there are results, show them
        if (count($ConditionArray) > 0) {
            $sql = $insertsql . implode(' AND ', $ConditionArray) . " GROUP BY ins_id";
            $result = mysqli_query($link, $sql);
            if (!$result) {
                echo mysqli_error($link);
            }
            $num_result_rows = mysqli_num_rows($result);
        } else if ($ischarvalid && count($ConditionArray) == 0) {
            echo nl2br("\n Error: Please enter search query");
        }
        if ($num_result_rows > 0) {
            echo "<table>";
            echo "<tr><th>Insert ID</th><th>Insert Name</th><th>Insert Type</th>"
            . "<th>Biobrick registry ID</th><th>Date added</th>"
            . "<th>Creator</th><th>Comment</th></tr>";

            // output data of each row
            while ($row = $result->fetch_assoc()) {
                if (!$loggedin && $row["private"] == 1) {
                    
                } else {
                    echo "<tr><td>" . $row["ins_id"] .
                    "</td><td><a href=\"parts.php?ins_id=" . $row["ins_id"] . "\">" . $row["insname"] . "</a>" .
                    "</td><td>" . $row["ins_type"] .
                    "</td><td>" . $row["ins_reg"] .
                    "</td><td>" . $row["date_db"] .
                    "</td><td><a href=\"user.php?user_id=" . $row["user_id"] . "\">" . $row["creator_name"] . "</a>" .
                    "</td><td>" . "" .
                    "</td></tr>";
                }
            }

            echo "</table>";
        }
        // If there are no rows, create error
        else {
            $iserror = TRUE;
            $error = "No matching results, try another search.";
        }
        // Show errors
        if ($iserror && !empty($ConditionArray)) {
            echo "<h3> Error: " . $error . "</h3>";
        }

        mysqli_close($link) or die("Could not close database connection");
    }
}
?>

    </div>    
</main>

<?php include 'bottom.php'; ?>

