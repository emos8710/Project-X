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
        $strainErr = $backboneErr = $yearErr = $regErr = ""; 
        $strain = $backbone = $comment = $year = $private = $reg = ""; 
        
        if($_SERVER["REQUEST_METHDO"] == "POST") {
           
            if (empty($_POST["strain"])) {
                 $strainErr = "A strain is required"; 
            } else {
                 $strain = test_input($_POST["strain"]); 
            }
            
            if(empty($_POST["backbone"])) {
                $backboneErr = "A backbone is required"; 
            } else {
                $backbone = test_input($_POST["backbone"]); 
            }           
            
            if(empty($_POST["comment"])) {
                $comment = ""; 
            } else {
                $comment = test_input($_POST["comment"]); 
            }
            
            if(empty($_POST["year"])) {
                $yearErr = "A year is required";  
            } elseif(!is_numeric($year)) {
                $yearErr = "The input year is not valid";  
            } else {
                $year = test_input($_POST["year"]); 
            }
            
            $reg = test_input($_POST["reg_link"]); 
        
        }
        
        
        function test_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
             return $data; 
            
        }
        ?>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["insert.php"]);?>" >
           
            <?php
             session_start(); 
             ?>
             
             <p>
                <label for="strain">Strain </label>
                <input type="text" name="strain" id="strain" value="<?php echo $strain;?>" />
                <span class="error">* <?php echo $strainErr; ?></span>
                <br/></p>
            
             <p>
                <label for="backbone">Backbone </label>
                <input type="text" name ="backbone" id="backbone" value="<?php echo $backbone;?>" /> 
                <span class="error">* <?php echo $backboneErr; ?></span>
                <br/></p>
            
            <p> <label for="insert">Insert </label>
                <input type='text' name="insert" value="" class="auto"></p>
            </p>
        
             <p>
               <select name="insert_type">
                <option value="promotor">Promotor</option>
                <option value="coding_seq">Coding sequence</option>
                <option value="RBS">RBS</option>
                <option value="other">Other</option>
            </select></p>
            
            <p> 
                <label for="reg_link">Registry link</label>
                <input type="text" name="registry" id="reg" value="<?php echo $reg;?>" /> 
            </p>
            
            <p> 
            <label for="seq">Sequence </label>
            <input type="file" name="fileToUpload" id="fileToUpload">
            
            </p>
            <p>
                <label for="year">Year </label>
                <input type="text" name = "year" id="year" minlength= "4" maxlengh= "4" pattern = "(?:19|20)[0-9]{2})" 
                placeholder="YYYY" value="<?php echo $year; ?>" /> </p>
            
           <p> 
                <label for="comment">Comment </label>
                <input type="textarea" name="comment" id="comment" rows ="4" cols="50"
                value="<?php echo $comment;?>" /> </p>
            
           <p>
               <label for="private">Make this entry private </label>
               <input type="checkbox" name="private" value="private" </p>
           
           <p id="submit">
               <input type="submit" value="Submit" />
        
             </p>
            
        </form>
		
			</div>
        
        
<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.1/jquery-ui.min.js"></script>    
<script type="text/javascript">
$(function() {
    
    //autocomplete
    $(".auto").autocomplete({
        source: "search.php",
        minLength: 1
    });                
});
</script>
        
	</main>
	
	<?php include 'bottom.php'; ?>
	
</body>
</html>

