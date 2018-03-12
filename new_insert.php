<?php
if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
    session_start();
}

$title = "New entry";

include 'top.php';

//Set display for the content div
if (isset($_GET['content'])) {
    $current_content = test_input($_GET['content']);
} else {
    $current_content = '';
}
?>

<!-- Main content goes here -->
<main>
    <div class="innertube">

        <?php if ($loggedin) {
            ?>
            <?php

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
                $sql_ins_name = mysqli_query($link, "SELECT name,id FROM ins ORDER BY name");
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

            <!-- <h2 class="search_etc">New Entry</h2> -->
    <?php
    if (isset($_SESSION['success']) && !empty($_SESSION['success'])) {
        echo $_SESSION['success'];
    } else if (isset($_SESSION['existing']) && !empty($_SESSION['existing'])) {
        echo $_SESSION['existing'];
    } else if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
        echo $_SESSION['error'];
    }

    unset($_SESSION['error']);
    unset($_SESSION['success']);
    unset($_SESSION['existing']);
    ?>
            <div class="entry_nav">
                <ul>
                    <li>
                        <a <?php
        if (isset($_GET['content']) && test_input($_GET['content']) === "new_entry") {
            echo "class=\"active\"";
        }
        ?> href="?content=new_entry">New entry</a>
                    </li>
                    <li>
                        <a <?php
                            if (isset($_GET['content']) && test_input($_GET['content']) === "new_strain") {
                                echo "class=\"active\"";
                            }
                            ?> href="?content=new_strain">New strain </a>
                    </li>
                    <li>
                        <a <?php
                            if (isset($_GET['content']) && test_input($_GET['content']) === "new_backbone") {
                                echo "class=\"active\"";
                            }
                            ?> href="?content=new_backbone">New backbone</a>
                    </li>
                    <li>
                        <a <?php
                            if (isset($_GET['content']) && test_input($_GET['content']) === "new_insert") {
                                echo "class=\"active\"";
                            }
                            ?> class="last" href="?content=new_insert">New insert</a>
                    </li>
                </ul>
            </div> 
            <!-- Desired content is displayed here -->   

    <?php if ($current_content == "new_entry") {
        ?>
                <form class="insert-form" id="new_entry_form" method="post" action="<?php echo htmlspecialchars("insert.php"); ?>" enctype="multipart/form-data">

                    <div class="new_entry">
                        <div class="field-wrap">
                            <label>Strain * </label>
                            <select class="insert" name="strain_name" required>
                                <option value="">Select strain</option>
        <?php
        echo load_strain();
        ?>
                            </select>
                            <br/>
                        </div> 

                        <div class="field-wrap">
                            <label>Backbone * </label>
                            <select class="insert" name="backbone_name" required>
                                <option value="">Select backbone</option>
        <?php
        echo load_backbone();
        ?>
                            </select>
                            <br/>
                        </div>

                        <div class="field-wrap">
                            <table class="insert" id="dynamic">
                                <!-- <tr> <th> Insert </th> </tr> -->

                                <tr> 
                                    <td class="type">
                                        <label class="type">Insert type </label> <br>
                                        <select class="insert" name="insert_type[]" id="Ins_type" >
                                            <option value="">Select insert type</option>
        <?php
        echo load_ins_type();
        ?>
                                        </select></td>


                                    <td class="name">
                                        <label>Insert name </label> <br>
                                        <select class="insert" name="ins[]" id ="Ins">
                                            <option value="">Select insert name</option>
                                        </select></td>
                                    <td class="more-button"> <br> <button class="insert-mini" type="button" name="add" id="add_input">+ More inserts</button></td> </tr>

                            </table>
                        </div>


                        <div class="field-wrap"> 

                            <label>Registry id</label>
                            <input class="insert" type="text" name="registry" id="Registry" placeholder ="BBa_K[X]" pattern="BBa_K\d{4,12}"/> 
                        </div>

                        <div class="field-wrap">
                            <label class="sequence">Sequence </label>
                            <input class="sequence" type="file" name="my_file" id="FileToUpload"></input>
                        </div>

                        <div class="field-wrap">
                            <label>Year * </label>
                            <input class="insert" type="text" name="year" minlength= "4" maxlength= "4" pattern = "(?:19|20)[0-9]{2}" 
                                   placeholder="YYYY" title ="Must contain four digits for year." required/>
                        </div>

                        <div class="field-wrap"> 
                            <label>Comment * </label>
                            <textarea class="insert" name="comment" id="Comment" rows ="4" cols="50"
                                      required></textarea> 
                        </div>

                        <div class="checkbox">
                            <label>Make this entry private </label>
                            <input class="checkbox" type="checkbox" name="private" value=1> 
                        </div>

                        <div class="checkbox">
                            <label>This entry is created </label>
                            <input class="checkbox" type="checkbox" name="created" value=1> 
                        </div>

                        <button id="submit" type="submit" class="button" name="insert">Submit</button>
                    </div>
                </form>

                <!-- NEW STRAIN -->
        <?php
    } else if ($current_content == "new_strain") {
        ?>

                <form class="insert-form" method="post" action="<?php echo htmlspecialchars("add_strain.php"); ?>">
                    <div class="new_strain">
                        <div class="field-wrap-strain">
                            <label for="Strain">Strain * </label>
                            <input class="insert" type="text" name="strain" id="Strain" required/></input>
                            <br/>
                        </div> 

                        <div class="field-wrap-strain"> 
                            <label for="Comment">Comment * </label>
                            <textarea class="insert" name="comment" id="Comment" rows ="4" cols="50"
                                      required></textarea>
                        </div>
                    </div>

                    <button id="submit" type="submit" class="button" name="insert" />Submit</button>
                </form>

                <!-- NEW BACKBONE -->
        <?php
    } else if ($current_content == "new_backbone") {
        ?>
                <form class="insert-form" method="post" action="<?php echo htmlspecialchars("add_backbone.php"); ?>">
                    <div class="new_backbone">
                        <div class="field-wrap-backbone">
                            <label>Backbone * </label>
                            <input class="insert" type="text" name ="backbone" id="Backbone" placeholder="pSB#X#" pattern="pSB\d{1}[A-Z]{1}\d{1}" required/></input>
                            <br/>
                        </div>

                        <div class="field-wrap-backbone"> 
                            <label >Registry id</label>
                            <input class="insert" type="text" name="Bb_registry" id="Registry" placeholder ="BBa_K[X]" pattern="BBa_K\d{4,12}"/> 
                        </div>

                        <div class="field-wrap-backbone">
                            <label>Comment * </label>
                            <textarea class="insert" name="comment" id="Comment" rows ="4" cols="50"
                                      required></textarea> </p>
                        </div>

                        <div class="checkbox">
                            <label>Make this entry private </label>
                            <input class="checkbox" type="checkbox" name="private" value=1> 
                        </div>
                    </div>

                    <button id="submit" type="submit" class="button" name="insert" />Submit</button>
                </form>

                <!-- NEW INSERT -->
        <?php
    } else if ($current_content == "new_insert") {
        ?>

                <form class="insert-form" method="post" action="<?php echo htmlspecialchars("add_insert.php"); ?>">
                    <div class="new_insert">
                        <div class="field-wrap-insert"> 
                            <label>Insert type * </label>
                            <select class="insert" name="new_insert_type" required>
                                <option value="">Select insert type</option>
        <?php
        echo load_ins_type();
        ?>
                            </select>
                        </div>
                        <div class="field-wrap-insert">
                            <label>Insert name * </label>
                            <input class="insert" type="text" name="new_insert"  required/></input>
                        </div>

                        <div class="field-wrap-insert"> 
                            <label>Registry id</label>
                            <input class="insert" type="text" name="Ins_registry" id="Registry"  placeholder ="BBa_K[X]" pattern="BBa_K\d{4,12}"/> 
                        </div>

                        <div class="field-wrap-insert">
                            <label>Comment * </label>
                            <textarea class="insert" name="comment" id="Comment" rows ="4" cols="50"
                                      required></textarea> 
                        </div>

                        <div class="checkbox">
                            <label>Make this entry private </label>
                            <input class="checkbox" type="checkbox" name="private" value=1> 
                        </div>
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
} else if (!$loggedin) {
    ?>
        <h3 style="color:red">Access denied (you are not logged in).</h3>
        <?php
    } else {
        ?>
        <h3 style="color:red">Access denied (your account is not activated).</h3>
        <?php
    }
    ?>

</main>

<script>
    $(document).ready(function () {
        var i = 1;
        var max = 5;

        $("#add_input").click(function () {
            if (i <= max) {
                $("#dynamic").append('<tr id="row' + i + '">\n\
                <td class="type"><select class="insert" name="insert_type[]" id="Ins_type"><option value="">Select insert type</option>\n\
<?php echo load_ins_type(); ?></select></td>\n\
                <td class="name"><select class="insert" name="ins[]" id ="Ins_name"><option value="">\n\
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
        $("#Ins_type").change(function () {
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
<?php
/*
  <script>
  $(document).ready(function () {
  var type_values = new Array();
  $("#Ins_type").change(function () {
  $('#Ins_type option:selected').each(function(i,selected) {
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
?>

<?php include 'bottom.php'; ?>