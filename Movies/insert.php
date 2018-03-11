<html>


<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Escape user inputs for security

include 'db.php';

$name = mysqli_real_escape_string($link, $_REQUEST['name']);
$year = mysqli_real_escape_string($link, $_REQUEST['year']);
$genre = mysqli_real_escape_string($link, $_REQUEST['genre']);
$rating = mysqli_real_escape_string($link, $_REQUEST['rating']);

$sql = "INSERT INTO movies (name, year, genre, rating) "
        ."SELECT '$name', '$year', genres.id, '$rating' "
        . "FROM genres "
        . "WHERE genres.name = '$genre' "
        . "LIMIT 1";
        

if (mysqli_query($link, $sql)) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($link);
}
?>
<br>
<a href="index.php">Back to the front page</a>
<?php

mysqli_close($link) or die('Could not close connection to database');

?>

</html>