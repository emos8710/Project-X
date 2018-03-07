<?php
if (count(get_included_files()) == 1) // Prevent direct access
    exit("Access restricted");

$current_url = "control_panel.php?content=manage_inserts";
?>

<h3>Manage inserts</h3>

<?php
// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete']) && isset($_POST['what'])) {
    include 'scripts/db.php';
    ?>
    <p>
        <?php
        if ($_POST['what'] === "ins_type") {
            $id = mysqli_real_escape_string($link, $_POST['delete']);
            if (!mysqli_query($link, "DELETE FROM ins_type WHERE id = " . $id)): $msg = "<strong style=\"color:red\">Database error: cannot remove insert type (probably used by inserts).</strong>";
            else: $msg = "<strong style=\"color:green\">Insert type successfully removed!</strong>";
            endif;

            echo $msg;
        } else if ($_POST['what'] === "insert") {
            $id = mysqli_real_escape_string($link, $_POST['delete']);
            if (!mysqli_query($link, "DELETE FROM ins WHERE id = " . $id)): $msg = "<strong style=\"color:red\">Database error: Cannot remove insert (probably used by entries).</strong>";
            else: $msg = "<strong style=\"color:green\">Insert successfully removed!</strong>";
            endif;

            echo $msg;
        } else {
            echo "This should never happen";
        }

        mysqli_close($link) or die("Could not close database connection");
        ?>
        <br>
        Reloading in 10 seconds... <a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Reload now</a>
    </p>
    <?php
}

/* Fetch data from database */

include 'scripts/db.php';

// Fetch all inserts
$insertsql = "SELECT ins.id AS id, ins.name AS name, ins.ins_reg AS biobrick, ins.date_db AS date, ins.comment AS comment, ins_type.name AS type, "
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
        <col><col><col><col><col>
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
                            <button class="control-panel-edit" title="Edit insert type" type="submit"/>
                        </form>
                    </td>
                    <td>
                        <form class="control-panel" action="<?php echo $current_url; ?>" method="GET">
                            <input type="hidden" name="content" value="manage_inserts">
                            <input type="hidden" name="history" value="instype">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button class="control-panel-history" title="View insert type history" type="submit"/>
                        </form>
                    </td>
                    <td>
                        <form class="control-panel" action="<?php echo $current_url; ?>" method="POST">
                            <input type="hidden" name="delete" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="what" value="ins_type">
                            <input type="hidden" name="header" value="refresh">
                            <button class="control-panel-delete" title="Delete insert type" type="submit" onclick="confirmAction(event, 'Really delete this insert type?')"/>
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
<br>
<p>
<h4>Inserts</h4>
<?php
if (mysqli_num_rows($insertquery) < 1) {
    ?>
    <strong>No inserts to show.</strong>
    <?php
} else {
    ?>
    <table class="control-panel-inserts">
        <col><col><col><col><col><col><col><col>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Date added</th>
                <th>iGEM Registry</th>
                <th>Added by</th>
                <th>Comment</th>
                <th colspan="3">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($insertquery)) {
                ?>
                <tr>
                    <td><a href="parts.php?ins_id=<?=$row['id'];?>"><?php echo $row['id']; ?></a></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['type']; ?></td>
                    <td><?php echo $row['date']; ?></td>
                    <td><a class="external" href="http://parts.igem.org/Part:<?php echo $row['biobrick']; ?>" target="_blank"><?php echo $row['biobrick']; ?></a></td>
                    <td><a href="user.php?user_id=<?php echo $row['uid']; ?>"><?php echo $row['fname'] . " " . $row['lname']; ?></a></td>
                    <td><?php echo $row['comment']; ?></td>
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
                            <input type="hidden" name="what" value="insert">
                            <input type="hidden" name="header" value="refresh">
                            <button class="control-panel-delete" title="Delete insert" type="submit" onclick="confirmAction(event, 'Really delete this insert?')"/>
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