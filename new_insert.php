<?php
if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
    session_start();
}

//Set display for the content div
if (isset($_GET['content'])) {
    $current_content = $_GET['content'];
} else {
    $current_content = '';
}

$title = "New entry";
?>

<?php include 'top.php'; ?>

<!-- Main content goes here -->
<main>
    <div class="innertube">

        <?php if ($loggedin) {
            ?>
            <?php
            $reg = $year = $comment = $strain = $backbone = $inst = "";

            if (empty($_POST["registry"])) {
                $reg = "";
            } else {
                $reg = test_input($_POST["registry"]);
            }

            if (empty($_POST["strain"])) {
                $strain = "";
            } else {
                $strain = test_input($_POST["strain"]);
            }

            if (empty($_POST["backbone"])) {
                $backbone = "";
            } else {
                $backbone = test_input($_POST["backbone"]);
            }

            if (empty($_POST["new_insert"])) {
                $inst = "";
            } else {
                $inst = test_input($_POST["new_insert"]);
            }

            if (empty($_POST["year"])) {
                $year = "";
            } else {
                $year = test_input($_POST["year"]);
            }

            if (empty($_POST["comment"])) {
                $comment = "";
            } else {
                $comment = test_input($_POST["comment"]);
            }

            //Functions
            function test_input($data) {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
            }

            function load_ins_type() {
                include 'scripts/db.php';
                $sql_ins_type = mysqli_query($link, "SELECT * FROM ins_type ORDER BY name");
                while ($row = $sql_ins_type->fetch_assoc()) {
                    echo '<option value="' . $row['id'] . '">' . $row['name'] . "</option>";
                }
                mysqli_close($link);
            }

            function load_ins_name() {
                include 'scripts/db.php';
                $sql_ins_name = mysqli_query($link, "SELECT name,id,type FROM ins ORDER BY name");
                while ($row = $sql_ins_name->fetch_assoc()) {
                        echo '<option value="' . $row['id'] . '">' . $row['name'] . "</option>";
                   
                }
                mysqli_close($link);
            }
 

            function load_strain() {
                include 'scripts/db.php';
                $sql_strain = mysqli_query($link, "SELECT name FROM strain ORDER BY name");
                while ($row = $sql_strain->fetch_assoc()) {
                    echo "<option>" . $row['name'] . "</option>";
                }
                mysqli_close($link);
            }

            function load_backbone() {
                include 'scripts/db.php';
                $sql_backbone = mysqli_query($link, "SELECT name FROM backbone ORDER by name");
                while ($row = $sql_backbone->fetch_assoc()) {
                    echo "<option>" . $row['name'] . "</option>";
                }
                mysqli_close($link);
            }
            ?>

            <h2>New Entry</h2>
            <?php
            if (isset($_SESSION['success']) && !empty($_SESSION['success'])) {
                echo $_SESSION['success'];
            } else if (isset($_SESSION['existing']) && !empty($_SESSION['existing'])) {
                echo $_SESSION['existing'];
            } else if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
                echo $_SESSION['error'];
            }
            
            unset($_SESSION['success']);
            unset($_SESSION['existing']);
            unset($_SESSION['error']);
            
            if(isset($error)) {
                echo $error; 
            }
            
            ?>
            <div class="entry_nav">
                <ul>
                    <a href="?content=new_entry">New entry</a>
                    <a href="?content=new_strain">New strain </a>
                    <a href="?content=new_backbone">New backbone</a>
                    <a href="?content=new_insert">New insert</a>
                </ul>
            </div>
            <!-- Desired content is displayed here -->   

            <?php if ($current_content == "new_entry") {
                ?>
                <form class="insert-form" id="new_entry_form" method="post" action="<?php echo htmlspecialchars("insert.php"); ?>" enctype="multipart/form-data">

                    <div class="new_entry">
                        <div id="test"></div>
                        <div class="field-wrap">
                            <label for="Strain">Strain * </label>

                            <select name="strain_name" required>
                                <?php
                                echo load_strain();
                                ?>
                            </select>
                            <br/>
                        </div>

                        <div class="field-wrap">
                            <label for="Backbone">Backbone * </label>

                            <select name="backbone_name" required>
                                <?php
                                echo load_backbone();
                                ?>
                            </select>
                            <br/>
                        </div>

                        <div class="field-wrap">
                            <table id="dynamic">
                                <thead>Insert</thead>


                                <td>
                                    <label for="Ins_Type">Insert type </label>
                                    <select  name="insert_type[]" class="Ins_type" >
                                        <option value="">Select insert type</option>
                                        <?php
                                        echo load_ins_type();
                                        ?>
                                    </select></td>

                                <td>
                                    <label for="Ins">Insert name </label>
                                    <select class="insert" name="ins[]" id ="Ins">
                                        <option value="">Select insert name</option>
                                    </select></td>
                                <td> <button type="button" name="add" id="add_input">+ More inserts</button></td>

                            </table>
                        </div>


                        <div class="field-wrap"> 

                            <label for="Registry">Registry id</label>
                            <input class="insert" type="text" name="registry" id="Registry" value="<?php echo $reg; ?>" placeholder ="BBa_K[X]" pattern="BBa_K\d{4,12}"/> 
                        </div>

                        <div class="field-wrap">
                            <label for="FileToUpload">Sequence </label>
                            <input class="button" type="file" name="my_file" id="FileToUpload">
                        </div>

                        <div class="field-wrap">
                            <label for="Year">Year * </label>
                            <input class="insert" type="text" name = "year" id="Year"  maxlengh= "4" pattern = "[0-9]{4}" 
                                   placeholder="YYYY" value="<?php echo $year; ?>" required/>
                        </div>

                        <div class="field-wrap"> 
                            <label for="Comment">Comment * </label>
                            <textarea class="insert" name="comment" id="Comment" rows ="4" cols="50"
                                      value="<?php echo $comment; ?>" required ="required"> </textarea> 
                        </div>

                        <div class="checkbox">
                            <label for="Private">Make this entry private </label>
                            <input class="checkbox" type="checkbox" name="private" value=1> 
                        </div>

                        <div class="checkbox">
                            <label for="Created">This entry is created </label>
                            <input class="checkbox" type="checkbox" name="created" value=1> 
                        </div>

                        <button id="submit" type="submit" class="button" name="insert" />Submit</button>
                    </div>
                </form>

                <!-- NEW STRAIN -->
                <?php
            } else if ($current_content == "new_strain") {
                ?>

                <form method="post" action="<?php echo htmlspecialchars("add_strain.php"); ?>" enctype="multipart/form-data">

                    <div class="field-wrap">

                        <label for="Strain">Strain * </label>
                        <input class="insert" type="text" name="strain" id="Strain" value="<?php echo $strain; ?>" required/>
                        <br/>
                    </div> 

                    <div class="fieldwrap"> 
                        <label for="Comment">Comment * </label>
                        <textarea name="comment" id="Comment" rows ="4" cols="50"
                                  value="<?php echo $comment; ?>" required ="required"> </textarea> </p>

                    </div>


                    <button id="submit" type="submit" class="button" name="insert" />Submit</button>
                </form>

                <!-- NEW BACKBONE -->
                <?php
            } else if ($current_content == "new_backbone") {
                ?>
                <form method="post" action="<?php echo htmlspecialchars("add_backbone.php"); ?>" enctype="multipart/form-data">
                    <p>
                    <div class="field-wrap">

                        <label for="Backbone">Backbone * </label>
                        <input class="insert" type="text" name ="backbone" id="Backbone" value="<?php echo $backbone; ?>" required/> 
                        <br/>
                    </div>

                    <div class="field-wrap"> 
                        <label for="Registry">Registry id</label>
                        <input class="insert" type="text" name="Bb_registry" id="Registry" value="<?php echo $reg; ?>" placeholder ="BBa_K[X]" pattern="BBa_K\d{4,12}"/> 
                    </div>

                    <div class="field-wrap">
                        <label for="Comment">Comment * </label>
                        <textarea name="comment" id="Comment" rows ="4" cols="50"
                                  value="<?php echo $comment; ?>" required ="required"> </textarea> </p>

                    </div>

                    <div class="checkbox">
                        <label for="Private">Make this entry private </label>
                        <input class="checkbox" type="checkbox" name="private" value=1> 
                    </div>

                    <button id="submit" type="submit" class="button" name="insert" />Submit</button>
                </form>

                <!-- NEW INSERT -->
                <?php
            } else if ($current_content == "new_insert") {
                ?>

                <form method="post" action="<?php echo htmlspecialchars("add_insert.php"); ?>" enctype="multipart/form-data">
                    <div class="field-wrap"> 
                        <table id="dynamic">
                            <td>
                                <label for="Ins_Type">Insert type * </label>
                                <select class="insert" name="new_insert_type" required>
                                    <option value="">Select insert type</option>
                                    <?php
                                    echo load_ins_type();
                                    ?>
                                </select></td>
                            <td>
                                <label for="Ins">Insert name * </label>
                                <input class="insert" type="text" name="new_insert" value="<?php echo $inst; ?>" required/>
                            </td>
                        </table>
                    </div>



                    <div class="field-wrap"> 
                        <label for="Registry">Registry id</label>
                        <input class="insert" type="text" name="Ins_registry" id="Registry" value="<?php echo $reg; ?>" placeholder ="BBa_K[X]" pattern="BBa_K\d{4,12}"/> 
                    </div>

                    <div class="field-wrap">
                        <label for="Comment">Comment * </label>
                        <textarea class="insert" name="comment" id="Comment" rows ="4" cols="50"
                                  value="<?php echo $comment; ?>" required ="required"> </textarea> 
                    </div>

                    <button id="submit" type="submit" class="button" name="insert" />Submit</button>
                </form>
                <?php
            } else {
                echo "";
            }
            ?>

        </div>



        <?php
    } else if (!$active) {
        ?>
        <h3 style="color:red">Access denied (your account is not activated).</h3>
        <?php
    } else {
        ?>
        <h3 style="color:red">Access denied (you are not logged in).</h3>
        <?php
    }
    ?>

</main>

<?php include 'bottom.php'; ?>

<script src = "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        var i = 1;
        var max = 5;

        $("#add_input").click(function () {
            if (i <= max) {
                $("#dynamic").append('<tr id="row' + i + '">\n\
                <td><select name="insert_type[]" class="Ins_type" ><option value="">Select insert type</option>\n\
<?php echo load_ins_type(); ?></select><td>\n\
                <select class="insert" name="ins[]" id ="Ins"><option value="">\n\
                Select insert name</option><?php echo load_ins_name(); ?></select></td>\n\
                <td><button type="button" name="remove" id="' + i + '" class="btn_remove">Remove insert</button></td></tr>');
                i++;
            }
        });
        
        $(document).on('click', '.btn_remove', function () {
            var button_id = $(this).attr("id");
            $("#row" + button_id + '').remove();
            i--;

        });

        $('#submit').click(function () {
            $.ajax({
                url: "insert.php",
                method: "POST",
                data: $('#add_me').serialize(),
                success: function (data)
                {
                    $('#add_me')[0].reset();
                }
            });
        });
    });

</script>


<script>
    $(document).ready(function () {
        $(".Ins_type").change(function () {
            var type_id = $(this).val();
            $.ajax({
                url: 'dropdown.php',
                method: "POST",
                data: {inst: type_id},
                dataType: "text",
                success: function (data) {
                    $("#Ins").html(data);
                }
            });

        });
    });
</script>

<?php /*
<script>
    
    $(document).ready(function () {
            var type_values = new Array(); 
        $("#dynamic").change(function () {
            $('.Ins_type option:selected').each(function(i,selected) {
                type_values[i] = $(selected).val(); 
            });
            
            $(document).on('click', '.btn_remove', function () {
            type_values.removeAttr("selected");   
            }); 
            
            $.ajax({
                url: "ajax.php",
                method: "POST",
                data: {type_val: type_values},
                success: function (data) {
                    //$("#test").html(data); 
                    
                    $("#Ins_name").html(data);
                }
            });
        });
        
      
    });

</script>
 * 
 */
