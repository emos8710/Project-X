<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        include 'db.php';
        ?>

        <form action ="insert.php" method="POST">

            <p> Movie name: 
                <input type="text" name="name"><br>
            </p>

            <p> Year: 
                <input type="text" name="year"><br>
            </p>

            <p> Movie genre: </p>
            <p> 
                <select name="genre">
                    <option value="Action/Adventure">Action/Adventure</option>
                    <option value="Comedy">Comedy</option>
                    <option value="Drama">Drama</option>
                    <option value="Fantasy/Sci-Fi">Fantasy/Sci-Fi</option>
                </select>
            </p>

            <p> Select rating: </p>
            <p>
                <select name="rating">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>   

            <p id="submit">
                <input type="submit" value="SUBMIT" />

            </p>    
        </form>

        <form action="index.php" method="post">
            <label>Search for a movie: </label>
            <input type="text" name="name"/>
            <p>If you make an empty search, you'll see all the movies in the database </p>
            <button type="submit" class="button" name="search" />Search</button>
    </form>

</body>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = mysqli_real_escape_string($link, $_POST['name']);
    if (!empty($name)) {
        $sql = "SELECT * FROM movies WHERE name LIKE '$name'";
        $res = mysqli_query($link, $sql);
        $num_res_rows = mysqli_num_rows($res);
        if ($num_res_rows > 0) {
            echo "<table>";
            echo "<tr><th>Id</th><th>Name</th><th>Year</th>"
            . "<th>Genre</th><th>Rating</th></tr>";
            while ($row = $res->fetch_assoc()) {
                echo "<tr><td>" . $row['id'] . "</td>" . "<td>" . $row['name'] . "</td>" . "<td>"
                . $row['year'] . "</td>" . "<td>" . $row['genre'] . "</td>" . "<td>" . $row['rating'] . "</td></tr>";
            }
            echo "</table>";
            mysqli_close($link); 
        }
    } else {
        $sql = "SELECT * FROM movies";
        $res = mysqli_query($link, $sql);
        $num_res_rows = mysqli_num_rows($res);
        if ($num_res_rows > 0) {
            echo "<table>";
            echo "<tr><th>Id</th><th>Name</th><th>Year</th>"
            . "<th>Genre</th><th>Rating</th></tr>";
            while ($row = $res->fetch_assoc()) {
                echo "<tr><td>" . $row['id'] . "</td>" . "<td>" . $row['name'] . "</td>" . "<td>"
                . $row['year'] . "</td>" . "<td>" . $row['genre'] . "</td>" . "<td>" . $row['rating'] . "</td></tr>";
            }
            echo "</table>";
            mysqli_close($link); 
        }
    }
}
?>
</html>
