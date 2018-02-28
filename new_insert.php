<?php
if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
    session_start();
}

$title = "New Entry";
?>

<!DOCTYPE html>

<?php include 'top.php'; ?>

<body>
    <!-- Main content goes here -->
    <main>
        <div class="innertube">
		<form class="insert-form" method="post" action="<?php echo htmlspecialchars("insert.php"); ?>" >

		<h2>New Entry</h2>
            <?php if ($loggedin) {
                $strainErr = $backboneErr = $yearErr = "";
                $strain = $backbone = $comment = $year = $reg = $inst = "";

                if ($_POST) {

                    if (empty($_POST["strain"])) {
                        $strainErr = "A strain is required";
                    } else {
                        $strain = test_input($_POST["strain"]);
                    }

                    if (empty($_POST["backbone"])) {
                        $backboneErr = "A backbone is required";
                    } else {
                        $backbone = test_input($_POST["backbone"]);
                    }

                    if (empty($_POST["year"])) {
                        $yearErr = "A year is required";
                    } elseif (!is_numeric($year)) {
                        $yearErr = "The input year is not valid";
                    } else {
                        $year = test_input($_POST["year"]);
                    }

                    if (empty($_POST["comment"])) {
                        $comment = "";
                    } else {
                        $comment = test_input($_POST["comment"]);
                    }

                    if (empty($_POST["registry"])) {
                        $reg = "";
                    } else {
                        $reg = test_input($_POST["registry"]);
                    }

                    if (empty($_POST["ins"])) {
                        $inst = "";
                    } else {
                        $inst = test_input($_POST["ins"]);
                    }
                }

                function test_input($data) {
                    $data = trim($data);
                    $data = stripslashes($data);
                    $data = htmlspecialchars($data);
                    return $data;
                }
                ?>

                <p><span class="error">* required field.</span></p>

                    <div>
						<div class="field-wrap"> 
							<label for="Strain">Strain </label>
							<input class="insert" type="text" name="strain" id="Strain" value="<?php echo $strain; ?>" required/>
							<span class="error">* <?php echo $strainErr; ?></span>
                        <br/>
						</div>

						<div class="field-wrap">
							<label for="Backbone">Backbone </label>
							<input class="insert" type="text" name ="backbone" id="Backbone" value="<?php echo $backbone; ?>" required/> 
							<span class="error">* <?php echo $backboneErr; ?></span>
							<br/>
						</div>

						<div class="field-wrap"> 
							<table id="dynamic">
							<label for="Ins">Insert </label>
							<input class="insert" type="text" name="ins[]" value="<?php echo $inst; ?>" id ="Ins" class="typeahead"/>
							<button class="insert-mini" type="button" name="add" id="add_input">+ More inserts</button>
							</table>
						</div>

						<div class="field-wrap">
							<label for="Ins_Type">Insert type </label>
							<select class="insert" name="insert_type[]">
                            <option value="Promotor">Promotor</option>
                            <option value="Coding sequence">Coding sequence</option>
                            <option value="RBS">RBS</option>
                            <option value="Other">Other</option>
							</select>
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
							<label for="Year">Year </label>
							<input class="insert" type="text" name = "year" id="Year"  maxlengh= "4" pattern = "[0-9]{4}" 
                               placeholder="YYYY" value="<?php echo $year; ?>" required/>
							<span class="error">* <?php echo $yearErr; ?></span>
							<br/>
						</div>
						
						<div class="field-wrap"> 
							<label for="Comment">Comment </label>
							<textarea class="insert" name="comment" id="Comment" rows ="4" cols="50"
                                  value="<?php echo $comment; ?>" > </textarea> 
						</div>

						<div class="checkbox">
							<label for="Private">Make this entry private </label>
							<input class="checkbox" type="checkbox" name="private" value="Private"> 
						</div>

                    <!-- <p id="submit">
                        <input type="submit" name="submit" value="Submit" />

                    </p> -->
					<button id="submit" type="submit" class="button" name="insert" />Submit</button>
				</div>
                </form>
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
<script type = "text/javascript" src = "http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.1/jquery-ui.min.js"></script>
<script src="//code.jquery.com/jquery-2.1.4.min.js"></script>

</body>
</html>

<script>
    $(document).ready(function () {
        var i = 1;
        var max = 5;
        $("#add_input").click(function () {
            if (i <= max) {
                $("#dynamic").append('<tr id="row' + i + '"><td><input class="insert-mini" type="text" name="ins[]" id ="Ins" /></td><td><select class="insert-mini" name="insert_type[]"><option value="Promotor">Promotor</option><option value="Coding sequence">Coding sequence</option><option value="RBS">RBS</option><option value="Other">Other</option></select></td><td><button type="button" name="remove" id="' + i + '" class="btn_remove">Remove insert</button></td></tr>');
                i++;
            } else {

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
                    alert(data);
                    $('#add_me')[0].reset();
                }
            });
        });
    });

</script>

<script>
    $(document).ready(function () {
        $('#Ins').typeahead({
            source: function (query, result) {
                $.ajax({
                    url: "autocomplete_ins.php",
		    data: 'query=' + query,            
                    dataType: "json",
                    type: "POST",
                    success: function (data) {
			result($.map(data, function (item) {
			    return item;
                        }));
                    }
                });
            }
        });
    });
</script>
<?php

/*
<script type="text/javascript">
    $(function () {

        //autocomplete
        $(".auto").autocomplete({
            source: "autocomplete_ins.php",
            minLength: 1
        });
    });
</script>


?>
*/
?>