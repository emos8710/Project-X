<?php
if (count(get_included_files()) == 1)
    exit("Access restricted");

$current_url = "control_panel.php?content=manage_strain_backbone";
?>

<h3 class="strainbackbone" style="text-align: left; font-style: normal; font-weight: 300; color: #001F3F;">Manage backbones & strains</h3>

<?php
// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete']) && isset($_POST['what'])) {
    
}

/* Fetch data from database */

include 'scripts/db.php';

//Fetch all strains
$strainsql = "SELECT strain.id AS sid, strain.name AS name, strain.comment AS cmt, strain.date_db AS date, "
        . "strain.private AS private, users.user_id AS uid, users.first_name AS fname, users.last_name AS lname "
        . "FROM strain "
        . "LEFT JOIN users ON strain.creator = users.user_id "
        . "ORDER BY strain.id ASC";
$strainquery = mysqli_query($link, $strainsql);

//Fetch all backbones
$backbonesql = "SELECT backbone.id AS bid, backbone.name AS name, backbone.comment AS cmt, "
        . "backbone.date_db AS date, backbone.private AS private, backbone. Bb_reg AS biobrick, "
        . "users.user_id AS uid, users.first_name AS fname, users.last_name AS lname "
        . "FROM backbone "
        . "LEFT JOIN users ON backbone.creator = users.user_id "
        . "ORDER by backbone.id ASC";
$backbonequery = mysqli_query($link, $backbonesql);

mysqli_close($link) or die("Could not close database connection");
?>

<!-- Display strains -->
<p>
<h4 class="manage_strain_backbone" style="text-align: left; font-weight: 200; font-style: italic; font-size: 18px;">Strains</h4>
<?php
if (mysqli_num_rows($strainquery) < 1) {
    ?>
    <strong>No strains to show</strong>
    <?php
} else {
    ?>
    <table class="control-panel-strains">
        <col><col><col><col><col><col><col><col><col>
        <thead>
            <tr>
                <th>ID</th>
                <th style="min-width: 80px;">Name</th>
                <th style="min-width: 80px;">Date added</th>
                <th style="min-width: 100px;">Added by</th>
                <th style="min-width: 130px;">Comment</th>
                <th style="min-width: 80px;">Private</th>
                <th colspan="3" style="min-width: 80px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($strainquery)) {
                ?>
                <tr>
                    <td><a href="parts.php?strain_id=<?= $row['sid'] ?>"><?php echo $row['sid']; ?></a></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['date']; ?></td>
                    <td><a href="user.php?user_id=<?php echo $row['uid']; ?>"><?php echo $row['fname'] . " " . $row['lname']; ?></a></td>
                    <td><?php echo $row['cmt']; ?></td>
                    <td><?php
                        if ($row['private'] == 1): echo "Yes";
                        else: echo "No";
                        endif;
                        ?></td>
                    <td>
                        <form class="control-panel" action="parts.php" method="GET">
                            <input type="hidden" name="strain_id" value="<?php echo $row['sid']; ?>">
                            <input type="hidden" name="edit">
                            <button class="control-panel-edit" title="Edit strain" type="submit"/>
                        </form>
                    </td>
                    <td>
                        <form class="control-panel" action="<?php echo $current_url; ?>" method="GET">
                            <input type="hidden" name="content" value="manage_strain_backbone">
                            <input type="hidden" name="history" value="strain">
                            <input type="hidden" name="id" value="<?php echo $row['sid']; ?>">
                            <button class="control-panel-history" title="View insert type history" type="submit"/>
                        </form>
                    </td>
                    <td>
                        <form class="control-panel" action="<?php echo $current_url; ?>" method="POST">
                            <input type="hidden" name="delete" value="<?php echo $row['sid']; ?>">
                            <input type="hidden" name="what" value="strain">
                            <input type="hidden" name="header" value="refresh">
                            <button class="control-panel-delete" title="Delete strain" type="submit" onclick="confirmAction(event, 'Really delete this insert type?')"/>
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
<h4 class="manage_strain_backbone" style="text-align: left; font-weight: 200; font-style: italic; font-size: 18px;">Backbones</h4>
<?php
if (mysqli_num_rows($backbonequery) < 1) {
    ?>
    <strong>No backbones to show</strong>
    <?php
} else {
    ?>
    <table class="control-panel-backbones">

        <thead>
            <tr>
                <th>ID</th>
                <th style="min-width: 80px;">Name</th>
                <th style="min-width: 80px;">iGEM Registry</th>
                <th style="min-width: 80px;">Date added</th>
                <th style="min-width: 100px;">Added by</th>
                <th style="min-width: 130px;">Comment</th>
                <th style="min-width: 80px;">Private</th>
                <th colspan="3" style="min-width: 80px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($backbonequery)) {
                ?>
                <tr>
                    <td><a href="parts.php?backbone_id=<?= $row['bid'] ?>"><?php echo $row['bid']; ?></a></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['biobrick']; ?></td>
                    <td><?php echo $row['date']; ?></td>
                    <td><a href="user.php?user_id=<?php echo $row['uid']; ?>"><?php echo $row['fname'] . " " . $row['lname']; ?></a></td>
                    <td><?php echo $row['cmt']; ?></td>
                    <td><?php
                        if ($row['private'] == 1): echo "Yes";
                        else: echo "No";
                        endif;
                        ?></td>
                    <td>
                        <form class="control-panel" action="parts.php" method="GET">
                            <input type="hidden" name="backbone_id" value="<?php echo $row['bid']; ?>">
                            <input type="hidden" name="edit">
                            <button class="control-panel-edit" title="Edit backbone" type="submit"/>
                        </form>
                    </td>
                    <td>
                        <form class="control-panel" action="<?php echo $current_url; ?>" method="GET">
                            <input type="hidden" name="content" value="manage_strain_backbone">
                            <input type="hidden" name="history" value="backbone">
                            <input type="hidden" name="id" value="<?php echo $row['bid']; ?>">
                            <button class="control-panel-history" title="View insert type history" type="submit"/>
                        </form>
                    </td>
                    <td>
                        <form class="control-panel" action="<?php echo $current_url; ?>" method="POST">
                            <input type="hidden" name="delete" value="<?php echo $row['bid']; ?>">
                            <input type="hidden" name="what" value="backbone">
                            <input type="hidden" name="header" value="refresh">
                            <button class="control-panel-delete" title="Delete backbone" type="submit" onclick="confirmAction(event, 'Really delete this insert type?')"/>
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
