<?php
if (count(get_included_files()) == 1) // Prevent direct access
    exit("Access restricted");

$current_url = "control_panel.php?content=manage_inserts";
?>

<h3 class="manage_inserts" style="text-align: left; font-style: normal; font-weight: 300; color: #001F3F;">Manage inserts</h3>

<?php
// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete']) && isset($_POST['what'])) {
        include 'scripts/db.php';
        ?>
        <?php
        if ($_POST['what'] === "ins_type") {
            $id = mysqli_real_escape_string($link, $_POST['delete']);
            if (!mysqli_query($link, "DELETE FROM ins_type WHERE id = " . $id)): $msg = "<strong style=\"color:red\">Database error: cannot remove insert type (probably used by inserts).</strong>";
            else: $msg = "<strong style=\"color:green\">Insert type successfully removed!</strong>";
            endif;
        } else if ($_POST['what'] === "insert") {
            $id = mysqli_real_escape_string($link, $_POST['delete']);
            if (!mysqli_query($link, "DELETE FROM ins WHERE id = " . $id)): $msg = "<strong style=\"color:red\">Database error: Cannot remove insert (probably used by entries).</strong>";
            else: $msg = "<strong style=\"color:green\">Insert successfully removed!</strong>";
            endif;
        } else {
            $msg = "This should never happen";
        }
        mysqli_close($link) or die("Could not close database connection");
        ?>
        <?php
    } else if (isset($_POST['add_instype'])) {
        include 'scripts/db.php';
        $new_type = mysqli_real_escape_string($link, test_input($_POST['add_instype']));
        $check_exists = mysqli_query($link, "SELECT * FROM ins_type WHERE name = '$new_type'");
        if (mysqli_num_rows($check_exists) >= 1) {
            $msg = "<strong style=\"color:red\">Insert type already exists!</strong>";
        } else {
            if (!mysqli_query($link, "INSERT INTO ins_type (name) VALUES ('$new_type')")) {
                $msg = "<strong style=\"color:red\">Database error: Could not add insert type.</strong>";
            } else {
                $msg = "<strong style=\"color:green\">Insert type successfully added!</strong>";
            }
        }
        mysqli_close($link) or die("Could not close database connection");
    }
    ?>
    <p>
        <?= $msg; ?>
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
<h4 class="manage_inserts"style="text-align: left; font-weight: 200; font-style: italic; font-size: 18px;">Insert types</h4>
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
                <th colspan="2">Actions</th>
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
            <tr>
                <th colspan="3">
                    Add insert type
                </th>
            </tr>
            <tr>
                <td colspan="3">
                    <form class="control-panel" action="<?= $current_url ?>" method="POST">
                        <input type="text" name="add_instype" style="border: 1px solid #001F3F; border-radius: 5px; display: inline-block; padding: 3px;">
                        <input type="hidden" name="header" value="refresh">
                        <input class="edit_entry_button" type="submit" value="Submit" style="width: 100px; font-size: 14px; font-style: normal; text-align: left; display: inline-block; margin-left: 5px;">
                    </form>
                </td>
            </tr>
        </tbody>
    </table>
    <?php
}
?>
</p>
<br>
<p>
<h4 class="manage_inserts"style="text-align: left; font-weight: 200; font-style: italic; font-size: 18px;">Inserts</h4>
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
                <th style="min-width: 80px;">Name</th>
                <th style="min-width: 80px;">Type</th>
                <th style="min-width: 90px;">Date added</th>
                <th style="min-width: 90px;">iGEM Registry</th>
                <th style="min-width: 80px;">Added by</th>
                <th style="min-width: 130px;">Comment</th>
                <th style="min-width: 80px;" colspan="3">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($insertquery)) {
                ?>
                <tr>
                    <td><a href="parts.php?ins_id=<?= $row['id']; ?>"><?php echo $row['id']; ?></a></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['type']; ?></td>
                    <td><?php echo $row['date']; ?></td>
                    <td><a class="external" href="http://parts.igem.org/Part:<?php echo $row['biobrick']; ?>" target="_blank"><?php echo $row['biobrick']; ?></a></td>
                    <td><a href="user.php?user_id=<?php echo $row['uid']; ?>"><?php echo $row['fname'] . " " . $row['lname']; ?></a></td>
                    <td><?php echo $row['comment']; ?></td>
                    <td>
                        <form class="control-panel" action="parts.php" method="GET">
                            <input type="hidden" name="ins_id" value="<?php echo $row['id']; ?>">
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