            <!-- Edit inserts -->
            <tr class="edit_entry"> 
                <th class="title"> Inserts: </th>
                <td class="info"> 
                    <div class="mini-table">
                        <?php while ($insert_row = mysqli_fetch_assoc($insert_result)) { ?>
                            <table class="mini-table">
                                <tr class="mini-table">
                                    <th class="mini-table" style="text-align: left; width:50px; border: none; border-bottom: none;"> 
                                        Insert: 
                                    </th> 
                                    <td class="mini-table" style="width: 200px">
                                        <?php echo $insert_row['name'] ?>
                                    </td>
                                    <th class="mini-table" style="text-align: left; width: 50px; border: none; border-bottom: none;"> 
                                        Type:
                                    </th> 
                                    <td class="mini-table" style="width: 200px">
                                        <?php echo $insert_row['type']; ?>
                                    </td>
                                    <?php
                                    if ($current_content != "insert" . $insert_row['position']) {
                                        ?>
                                        <td class="mini-table" style="verticle-align: center;">
                                            <a href="?upstrain_id=<?php echo $id; ?>&edit&content=insert<?php echo $insert_row['position'] ?>">Edit</a>
                                        </td>
                                        <td class="mini-table">
                                            <form action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                                                <input name="remove_insert" type="hidden">
                                                <input name="position" type="hidden" value="<?php echo $insert_row['position'] ?>">
                                                <input class="edit_entry_button" type="submit" value="Remove" style="height: 20px; padding: 2px; verticle-align: center; margin-top: 3px;">
                                            </form>
                                        </td>
                                    <?php } ?>
                                </tr>
                            </table>
                            <?php if ($current_content == "insert" . $insert_row['position']) { ?>
                                <div class="field-wrap">
                                    <form action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                                        <label>Insert type</label>
                                        <select name="insert_type" id="Ins_type" required>
                                            <option value="">Select insert type</option>
                                            <?php
                                            include 'scripts/db.php';
                                            $sql_ins_type = mysqli_query($link, "SELECT * FROM ins_type");
                                            while ($row = $sql_ins_type->fetch_assoc()) {
                                                echo '<option value="' . $row['id'] . '">' . $row['name'] . "</option>";
                                            }
                                            ?>
                                        </select>

                                        <label class="edit_entry">Insert name</label>
                                        <select name="insert" id ="Ins" required>
                                            <option value="">Select insert name</option>
                                        </select>

                                        <!-- Send insert position -->
                                        <input name="position" type="hidden" value="<?php echo $insert_row['position'] ?>">
                                        <input class="edit_entry_button" type="submit" value="Submit" >
                                        <div class="clear"></div>
                                        <a style="float:right; margin-left: 2px;" href="?upstrain_id=<?php echo $id; ?>&edit">Cancel</a>
                                    </form>
                                </div>
                            <?php } ?>

                        <?php } ?>
                    </div>
                </td>
                <td class="edit">
                    <a href="?upstrain_id=<?php echo $id; ?>&edit&content=add_insert">Add insert</a>
                    <!-- Add new insert -->
                    <?php if ($current_content == "add_insert") { ?>
                        <table class="mini-table" style="margin-top: 0px;">
                            <tr class="mini-table" style="margin-top: 0px;">
                            <form class="edit_entry" action="entry.php?upstrain_id=<?php echo $id; ?>&edit" method="POST">
                                <td class="mini-table" style="margin-top: 0px;">
                                    <div class="field-wrap" style="float: left; margin-right: 5px; margin-top: 0px;">
                                        <label class="edit_entry" style="font-size: 14px; font-style: normal; padding: 0px;"> Insert type </label>
                                        <br><select class="edit_entry" name="insert_type" id="Ins_type" required style="border: 1px solid #001F3F; border-radius: 5px">
                                            <option value="">Select insert type</option>
                                            <?php
                                            include 'scripts/db.php';
                                            $sql_ins_type = mysqli_query($link, "SELECT * FROM ins_type");
                                            while ($row = $sql_ins_type->fetch_assoc()) {
                                                echo '<option value="' . $row['id'] . '">' . $row['name'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </td>

                                <td class="mini-table">
                                    <div class="field-wrap">
                                        <label class="edit_entry" style="font-size: 14px; font-style: normal; padding: 0px;">Insert name</label>
                                        <br><select class="edit_entry" name="new_insert" id ="Ins" required style="border: 1px solid #001F3F; border-radius: 5px">
                                            <option value="">Select insert name</option>
                                        </select>
                                    </div>
                                </td>

                                <?php
                                include 'scripts/db.php';
                                $pos_query = "SELECT MAX(position) FROM entry_inserts WHERE entry_id = '$entry_id'";
                                $pos_result = mysqli_query($link, $pos_query);
                                $position = mysqli_fetch_array($pos_result)[0] + 1;
                                ?>

                                <td class="mini-table" style="margin-top: 0px;">
                                    <input name="position" type="hidden" value="<?php echo $position ?>" style="border: 1px solid #001F3F; border-radius: 5px">
                                </td>
                                <td class="mini-table" style="margin-top: 0px;">
                                    <br>
                                    <input class="edit_entry_button" type="submit" value="Submit" style="height: 20px; padding: 2px; verticle-align: center; margin-top: 3px;">
                                </td>
                                <td class="mini-table" style="margin-top: 0px;">
                                    <br>
                                    <a style="float:right; margin-left: 2px;" href="?upstrain_id=<?php echo $id; ?>&edit">Cancel</a>
                                </td>
                            </form>
                </tr>
            </table>
        <?php } ?>
    </td>
    </tr>