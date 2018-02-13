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
        
        include 'populateDB.php';
        
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
        
        <!--<form action="showmovies.php" method="GET"-->
    <!--</form>-->
    
   
    </body>
</html>
