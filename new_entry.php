<!DOCTYPE html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Entry</title>
    <link href="css/upstrain.css" rel="stylesheet">


</head>

<body>

    <?php include 'top.php'; ?>

    <!-- Main content goes here -->
    <main>
        <div class="innertube">
            <h2>New Entry</h2>

            <?php
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

            <form method="post" action="<?php echo htmlspecialchars("insert.php"); ?>" >

                <?php
                session_start();
                ?>

                <p>
                    <label for="Strain">Strain </label>
                    <input type="text" name="strain" id="Strain" value="<?php echo $strain; ?>" />
                    <span class="error">* <?php echo $strainErr; ?></span>
                    <br/></p>

                <p>
                    <label for="Backbone">Backbone </label>
                    <input type="text" name ="backbone" id="Backbone" value="<?php echo $backbone; ?>" /> 
                    <span class="error">* <?php echo $backboneErr; ?></span>
                    <br/></p>

                <p> <div id="cont_ins">
                    <label for="Ins">Insert </label>
                    <input type='text' name="ins" value="<?php echo $inst; ?>" id ="Ins" class="auto">
                    <a href="#" id="add">+ Add insert</a>
                </div>
                </p>

                </p>


                <p>
                    <select name="insert_type">
                        <option value="promotor">Promotor</option>
                        <option value="coding_seq">Coding sequence</option>
                        <option value="RBS">RBS</option>
                        <option value="other">Other</option>
                    </select></p>

                <p> 
                    <label for="Registry">Registry link</label>
                    <input type="text" name="registry" id="Registry" value="<?php echo $reg; ?>" /> 
                </p>

                <p> 
                    <label for="FileToUpload">Sequence </label>
                    <input type="file" name="fileToUpload" id="FileToUpload">

                </p>
                <p>
                    <label for="Year">Year </label>
                    <input type="text" name = "year" id="Year" minlength= "4" maxlengh= "4" pattern = "(?:19|20)[0-9]{2})" 
                           placeholder="YYYY" value="<?php echo $year; ?>" />
                    <span class="error">* <?php echo $yearErr; ?></span>
                    <br/></p>
                <p> 
                    <label for="Comment">Comment </label>
                    <textarea name="comment" id="Comment" rows ="4" cols="50"
                              value="<?php echo $comment; ?>" > </textarea> </p>

                <p>
                    <label for="Private">Make this entry private </label>
                    <input type="checkbox" name="private" value="Private" </p>

                <p id="submit">
                    <input type="submit" value="Submit" />

                </p>

            </form>



        </div>
        <script type = "text/javascript" src = "http://code.jquery.com/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="http://code.jquery.com/ui/1.10.1/jquery-ui.min.js"></script>    
        <script type="text/javascript">
            $(function () {

                //autocomplete
                $(".auto").autocomplete({
                    source: "autocomplete_ins",
                    minLength: 1
                });
            });
        </script>


        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script type="text/javascript" >
            $(document).ready(function(e){
            var html = "<p /><div>Insert <input type='text' name="ins" id ="childIns" class="auto"><a href="#" id="remove">x </a></div>";
            var max = 5;
            var current = 1;
            $("#add").click(function (e) {
                if (x <= max) {
                    $("#cont_ins").append(html);
                    x++;
                }
            });
            $("#cont_ins").on("click", "#remove", function (e) {
                $(this).parent("div").remove();
                x--;
                        

            });
        }); 
        </script>




    </main>

    <?php include 'bottom.php'; ?>

</body>
</html>

