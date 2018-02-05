<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'db.php';

$query1 = "CREATE TABLE IF NOT EXISTS genres (id INT(2) NOT NULL AUTO_INCREMENT, name VARCHAR(30) NOT NULL UNIQUE, PRIMARY KEY(id));"; 

$query2 = "CREATE TABLE IF NOT EXISTS movies (id INT(6) NOT NULL AUTO_INCREMENT,  "
        . "name VARCHAR(30) NOT NULL, year VARCHAR(4) NOT NULL, genre INT(2) NOT NULL, "
        . "rating INT(1), PRIMARY KEY(id), FOREIGN KEY(genre) REFERENCES genres (id));";
        
if(!mysqli_query($link, $query1)) {
    echo "Error creating table: " . mysqli_error($link); 
}



if(!mysqli_query($link, $query2)) {
    echo "Error creating table: " . mysqli_error($link); 
}

//$sql = "INSERT INTO genres (name) SELECT * FROM (SELECT 'Action/Adventure') AS tmp WHERE NOT EXISTS (SELECT name FROM genres WHERE name = 'Action/Adventure') LIMIT 1;"
//."INSERT INTO genres (name) SELECT * FROM (SELECT 'Comedy') AS tmp WHERE NOT EXISTS (SELECT name from genres WHERE name = 'Comedy') LIMIT 1;"
//."INSERT INTO genres (name) SELECT * FROM (SELECT 'Drama') AS tmp WHERE NOT EXISTS (SELECT name from genres WHERE name = 'Drama') LIMIT 1;"
//."INSERT INTO genres (name) SELECT * FROM (SELECT 'Fantasy/Sci-Fi') AS tmp WHERE NOT EXISTS (SELECT name from genres WHERE name = 'Fantasy/Sci-Fi') LIMIT 1;";

$sql = "INSERT INTO genres (name) VALUES ('Action/Adventure') ON DUPLICATE KEY UPDATE name=name;"
        ."INSERT INTO genres (name) VALUES ('Comedy') ON DUPLICATE KEY UPDATE name=name;"
        ."INSERT INTO genres (name) VALUES ('Drama') ON DUPLICATE KEY UPDATE name=name;"
        ."INSERT INTO genres (name) VALUES ('Fantasy/Sci-Fi') ON DUPLICATE KEY UPDATE name=name;";

if(!mysqli_multi_query($link, $sql)) {
    echo "Error inserting data into table: " . mysqli_error($link); 
}

mysqli_close($link);
        
        
        


