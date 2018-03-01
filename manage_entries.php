<?php
if (count(get_included_files()) == 1)
    exit("Access restricted");

$current_url = "control_panel.php?content=manage_entries";
?>

<h3>Manage entries</h3>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['history'])) {
    ?>
    <p>
        <?php
        include 'scripts/db.php';

        $delete_entry = FALSE;
        if (isset($_POST['delete'])) {
            $entry_id = $_POST['delete'];
            $id = mysqli_real_escape_string($link, $entry_id);
            $delete_entry = TRUE;
        } else {
            echo "This should never happen.";
        }

        if ($delete_entry) {
            $deletesql = "DELETE FROM entry WHERE id = " . $entry_id;
            if (!$deletequery = mysqli_query($link, $deletesql)): $delete_msg = "<strong style=\"color:red\">Database error: " . mysqli_error($link) . "</strong>";
            else: $delete_msg = "<strong style=\"color:green\">Entry successfully deleted!</strong>";
            endif;
            echo $delete_msg;
        }
        ?>
        <br>
        Reloading in 10 seconds... <a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Reload now</a>
    </p>
    <?php
}
?>

<p>
    <?php
    if (mysqli_num_rows($entryquery) < 1) {
        ?>
        <strong>No entries to show</strong>
        <?php
    } else {
        $row = mysqli_fetch_assoc($entryquery);
        $entry_inserts = array(); // create empty array for linking each entry with its inserts
        $rows = [];
        while ($row) { // will return FALSE when no more rows
            array_push($rows, $row);
            $current_id = $row['eid']; // fetch current entry ID
            $inserts = []; // empty array for storing inserts
            while ($current_id == $row['eid']) { // loop over all rows with the same entry ID
                array_push($inserts, $row['iname']); // add this row's insert to the array
                $row = mysqli_fetch_assoc($entryquery); // next row
                if (!$row)
                    break; //break if no more rows
            }
            $entry_inserts[$current_id] = $inserts; // associate entry ID with its inserts in the array.
        }
        mysqli_data_seek($entryquery, 0); // reset result to beginning
        ?>
    <table class="control-panel-entries">
        <col><col><col><col><col><col><col><col><col><col>
        <tr>
            <th>Upstrain ID</th>
            <th>Date added</th>
            <th>Strain</th>
            <th>Backbone</th>
            <th>Inserts</th>
            <th>Year created</th>
            <th>iGEM Registry</th>
            <th>Comment</th>
            <th>Created by</th>
            <th colspan="3">Actions</th>
        </tr>
        <?php
        foreach ($rows as $row) {
            ?>
            <tr>
                <td><a href="entry.php?upstrain_id=<?php echo $row['uid']; ?>"><?php echo $row['uid']; ?></a></td>
                <td><?php echo $row['date']; ?></td>
                <td><?php echo $row['sname']; ?></td>
                <td><?php echo $row['bname']; ?></td>
                <?php
                $current_entry = $row['eid'];
                $current_inserts = $entry_inserts[$current_entry];
                $ins_string = "";
                for ($i = 0; $i < count($current_inserts); $i++) {
                    if ($i == count($current_inserts) - 1): $ins_string = $ins_string . $current_inserts[$i];
                    else: $ins_string = $ins_string . $current_inserts[$i] . ", ";
                    endif;
                }
                ?>
                <td><?php echo $ins_string; ?></td>
                <td><?php echo $row['year']; ?></td>
                <td><?php echo $row['biobrick']; ?></td>
                <td><?php echo $row['cmt']; ?> </td>
                <td><a href="user.php?user_id=<?php echo $row['usid']; ?>"><?php echo $row['fname'] . " " . $row['lname']; ?></a></td>
                <td>
                    <form class="control-panel" action="entry.php" method="GET">
                        <input type="hidden" name="upstrain_id" value="<?php echo "" . $row['uid'] . ""; ?>">
                        <input type="hidden" name="edit">
                        <button class="control-panel-edit" title="Edit entry"/>
                    </form>
                </td>
                <td>
                    <form class="control-panel" action="<?php echo $current_url; ?>&history=entry" method="POST">
                        <input type="hidden" name="history" value="<?php echo $row['eid']; ?>">
                        <button type="submit" class="control-panel-history" title="View entry history"/>
                    </form>
                </td>
                <td>
                    <form class="control-panel" action="<?php echo $current_url; ?>" method="POST">
                        <input type="hidden" name="delete" value="<?php echo $row['eid']; ?>">
                        <input type="hidden" name="header" value="refresh">
                        <button type="submit" class="control-panel-delete" title="Delete entry" onclick="confirmAction(event, 'Really want to delete this entry?')"/>
                    </form>
                </td>
            </tr>
            <?php
        }
    }
    ?>
</table>
</p>