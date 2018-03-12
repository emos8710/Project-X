<?php
if (count(get_included_files()) == 1)
    exit("Access restricted");

$current_url = "control_panel.php?content=manage_entries";

// Connect to database
include 'scripts/db.php';

// Fetch all entries
$entrysql = "SELECT entry.id AS eid, entry.comment AS cmt, entry.year_created AS year, entry.date_db AS date, "
        . "entry.entry_reg AS biobrick, entry.created AS created, entry.private AS private, entry.backbone AS bid, entry.strain AS sid, entry_upstrain.upstrain_id AS uid, backbone.name AS bname, "
        . "strain.name AS sname, ins.id AS iid, ins.name AS iname, users.user_id AS usid, users.username AS usname, users.first_name AS fname, users.last_name AS lname FROM entry "
        . "LEFT JOIN entry_upstrain ON entry_upstrain.entry_id = entry.id "
        . "LEFT JOIN backbone ON entry.backbone = backbone.id "
        . "LEFT JOIN strain ON entry.strain = strain.id "
        . "LEFT JOIN entry_inserts ON entry_inserts.entry_id = entry.id "
        . "LEFT JOIN ins ON entry_inserts.insert_id = ins.id AND entry_inserts.entry_id = entry.id "
        . "LEFT JOIN users ON entry.creator = users.user_id "
        . "ORDER BY eid";
$entryquery = mysqli_query($link, $entrysql) or die("MySQL error: " . mysqli_error($link));

mysqli_close($link) or die("Could not close database connection");
?>

<h3 class="entries" style="font-style: normal; font-weight: 300; color: #001F3F;">Manage entries</h3>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    ?>
    <p>
        <?php
        include 'scripts/db.php';

        $entry_id = $_POST['delete'];
        $id = mysqli_real_escape_string($link, $entry_id);
        $check_file = mysqli_query($link, "SELECT * from upstrain_file WHERE upstrain_id = (SELECT upstrain_id FROM entry_upstrain WHERE entry_id = '$id');");
        $deletesql = "DELETE FROM entry WHERE id = " . $entry_id;
        $filename = mysqli_fetch_assoc($check_file)['name_new'];
        if (mysqli_num_rows($check_file) >= 1 && file_exists("files/" . $filename)) {
            $file_delete = unlink("files/" . $filename);
        }
        if (isset($file_delete) && !$file_delete): $delete_msg = "<strong style=\"color:red\">Could not delete this entry's sequence file</strong>";
        elseif (!mysqli_query($link, $deletesql)): $delete_msg = "<strong style=\"color:red\">Database error: " . mysqli_error($link) . "</strong>";
        else: $delete_msg = "<strong style=\"color:green\">Entry successfully deleted!</strong>";
        endif;
        echo $delete_msg;
        ?>
        <br>
        Reloading in 10 seconds... <a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Reload now</a>
    </p>
    <?php
    mysqli_close($link);
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
        $entry_insert_ids = array();
        $rows = [];
        while ($row) { // will return FALSE when no more rows
            array_push($rows, $row);
            $current_id = $row['eid']; // fetch current entry ID
            $inserts = []; // empty array for storing inserts
            $insert_ids = []; // and one for storing their ID's
            while ($current_id == $row['eid']) { // loop over all rows with the same entry ID
                array_push($inserts, $row['iname']); // add this row's insert to the array
                array_push($insert_ids, $row['iid']);
                $row = mysqli_fetch_assoc($entryquery); // next row
                if (!$row)
                    break; //break if no more rows
            }
            $entry_inserts[$current_id] = $inserts; // associate entry ID with its inserts in the array.
            $entry_insert_ids[$current_id] = $insert_ids;
        }
        mysqli_data_seek($entryquery, 0); // reset result to beginning
        ?>
    <table class="control-panel-entries">
        <col><col><col><col><col><col><col><col><col><col><col><col>
        <tr>
            <th class="manage_entries">Upstrain ID</th>
            <th class="manage_entries" style="min-width: 80px;">Date added</th>
            <th class="manage_entries" style="min-width: 80px;">Strain</th>
            <th class="manage_entries" style="min-width: 80px;">Backbone</th>
            <th class="manage_entries" style="min-width: 80px;">Inserts</th>
            <th class="manage_entries" style="min-width: 80px;">Year created</th>
            <th class="manage_entries" style="min-width: 80px;">iGEM Registry</th>
            <th class="manage_entries" style="min-width: 80px;">Comment</th>
            <th class="manage_entries" style="min-width: 80px;">Added by</th>
            <th class="manage_entries" style="min-width: 80px;">Private</th>
            <th class="manage_entries" style="min-width: 80px;">Created physically</th>
            <th colspan="3" class="manage_entries" style="min-width: 80px;">Actions</th>
        </tr>
        <?php
        foreach ($rows as $row) {
            ?>
            <tr>
                <td><a href="entry.php?upstrain_id=<?php echo $row['uid']; ?>"><?php echo $row['uid']; ?></a></td>
                <td><?php echo $row['date']; ?></td>
                <td><a href="parts.php?strain_id=<?= $row['sid'] ?>"><?php echo $row['sname']; ?></a></td>
                <td><a href="parts.php?backbone_id=<?= $row['bid'] ?>"><?php echo $row['bname']; ?></a></td>
                <?php
                $current_entry = $row['eid'];
                $current_inserts = $entry_inserts[$current_entry];
                $current_ids = $entry_insert_ids[$current_entry];
                $ins_string = "";
                for ($i = 0; $i < count($current_inserts); $i++) {
                    $link_string = "<a href=\"parts.php?ins_id=" . $current_ids[$i] . "\">" . $current_inserts[$i] . "</a>";
                    if ($i == count($current_inserts) - 1): $ins_string = $ins_string . $link_string;
                    else: $ins_string = $ins_string . $link_string . ", ";
                    endif;
                }
                ?>
                <td><?php echo $ins_string; ?></td>
                <td><?php echo $row['year']; ?></td>
                <td><?php echo $row['biobrick']; ?></td>
                <td><?php echo $row['cmt']; ?> </td>
                <td><a href="user.php?user_id=<?php echo $row['usid']; ?>"><?php echo $row['fname'] . " " . $row['lname']; ?></a></td>
                <td><?php
                    if ($row['private'] == 1): echo "Yes";
                    else: echo "No";
                    endif;
                    ?></td>
                <td><?php
                    if ($row['created'] == 1): echo "Yes";
                    else: echo "No";
                    endif;
                    ?></td>
                <td>
                    <form class="control-panel" action="entry.php" method="GET">
                        <input type="hidden" name="upstrain_id" value="<?php echo "" . $row['uid'] . ""; ?>">
                        <input type="hidden" name="edit">
                        <button class="control-panel-edit" title="Edit entry"/>
                    </form>
                </td>
                <td>
                    <form class="control-panel" action="<?php echo $current_url; ?>&history=entry" method="GET">
                        <input type="hidden" name="content" value="manage_entries">
                        <input type="hidden" name="history" value="entry">
                        <input type="hidden" name="id" value="<?php echo $row['eid']; ?>">
                        <button type="submit" class="control-panel-history" title="View entry history"/>
                    </form>
                </td>
                <td>
                    <form class="control-panel" action="<?php echo $current_url; ?>" method="POST">
                        <input type="hidden" name="delete" value="<?php echo $row['eid']; ?>">
                        <input type="hidden" name="header" value="refresh">
                        <button type="submit" class="control-panel-delete" title="Delete entry" onclick="confirmAction(event, 'Delete entry <?= $row['eid'] ?>? THIS IS PERMANENT. To resotre deleted entry, you need to creata new one manually.')"/>
                    </form>
                </td>
            </tr>
            <?php
        }
    }
    ?>
</table>
</p>