<?php
if (count(get_included_files()) == 1) // Prevent direct access
    exit("Access restricted");

$current_url = "control_panel.php?content=manage_inserts";
?>

<h3>Manage inserts</h3>

<?php
// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['history'])) {
    ?>
    <p>

    </p>
    <?php
}

/* Fetch data from database */

include 'db.php';

// Fetch all inserts
$insertsql = "SELECT ins.id, ins.name, ins.ins_reg AS bioibrick, ins.date_db AS date, ins.comment, ins_type.name AS type, "
        . "users.user_id AS uid, users.first_name AS fname, users.last_name AS lname "
        . "FROM ins "
        . "LEFT JOIN ins_type ON ins.type = ins_type.id "
        . "LEFT JOIN users ON ins.creator = users.user_id "
        . "ORDER BY ins.id ASC";
$insertquery = mysqli_query($link, $insertsql);

//Fetch all insert types
$typesql = "SELECT * FROM ins_type ORDER BY id ASC";
$typequery = mysqli_query($link, $typesql);

mysqli_close($link) or die("Could not close database connection");
?>

<!-- Display insert types -->
<p>
<h4>Insert types</h4>
<?php
if (mysqli_num_rows($typequery) < 1) {
    ?>
    <strong>No insert types</strong>
    <?php
} else {
    ?>
    <table class="control-panel-instypes">
        <col><col><col><col>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th colspan="3">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($typequery)) {
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td>
                        <form class="control-panel" action="#" method="GET">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="edit">
                            <button class="control-panel-edit" title="Edit insert" type="submit"/>
                        </form>
                    </td>
                    <td>
                        <form class="control-panel" action="<?php echo $current_url; ?>" method="GET">
                            <input type="hidden" name="content" value="manage_inserts">
                            <input type="hidden" name="history" value="insert">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button class="control-panel-history" title="View insert history" type="submit"/>
                        </form>
                    </td>
                    <td>
                        <form class="control-panel" action="<?php echo $current_url; ?>" method="POST">
                            <input type="hidden" name="delete" value="<?php echo $row['id']; ?>">
                            <button class="control-panel-delete" title="Delete insert" type="submit" onclick="confirmAction(event, 'Really want to delete this insert?')"/>
                        </form>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
}
?>
</p>