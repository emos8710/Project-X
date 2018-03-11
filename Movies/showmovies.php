<?php
include 'db.php';
$con=mysqli_connect("localhost","root","","movie");
echo "<table border='1'>
<tr>
<th>Movie ID</th>
<th>Movie Name</th>
<th>Year</th>
<th>Genre</th>
<th>Rating</th>
</tr>";

$result = mysqli_query($con,"select * from movies") or die("Could not issue MySQL query");

while($movieinfo = mysqli_fetch_array($result))
{
echo "<tr>";
echo "<td>" . $movieinfo['id'] . "</td>";
echo "<td>" . $movieinfo['name'] . "</td>";
echo "<td>" . $movieinfo['year'] . "</td>";
echo "<td>" . $movieinfo['genre'] . "</td>";
echo "<td>" . $movieinfo['rating'] . "</td>";
echo "</tr>";
}
echo "</table>";
?>
<br>
<a href="index.php">Back to the front page</a>
