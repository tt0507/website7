<?php
 // INCLUDE ON EVERY TOP-LEVEL PAGE!
include("includes/init.php");

$current_page = 'edit.php';
const MAX_FILE_SIZE = 1000000;

// Inserting image
if (isset($_POST["submit_upload"]) && is_user_logged_in()) {
    $upload_info = $_FILES["box_file"];
    $upload_title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING);

    if ($upload_info['error'] == UPLOAD_ERR_OK) {
        $upload_name = basename($upload_info["name"]);
        $upload_ext = strtolower(pathinfo($upload_name, PATHINFO_EXTENSION));

        $sql = "INSERT INTO images (image_name, image_ext, title, users_id) VALUES (:image_name,:image_ext, :title, :users_id)";
        $params = array(':image_name' => $upload_name, ':image_ext' => $upload_ext, ':title' => $upload_title, ':users_id' => $current_user['id']);
        $results = exec_sql_query($db, $sql, $params);

        if ($results) {
            $file_id = $db->lastInsertId("id");
            // echo ($file_id);
            $new_path = "uploads/documents/" . $file_id . "." . $upload_ext;
            move_uploaded_file($upload_info["tmp_name"], $new_path);
        }
    }
}

// Deleting the image from database
if (isset($_POST["delete_image"])) {
    $delete_image_id = filter_input(INPUT_POST, "delete_image_id", FILTER_SANITIZE_STRING);

    // Delete image
    $sql = "SELECT * FROM images WHERE id = :delete_image_id";
    $params = array(':delete_image_id' => $delete_image_id);
    $records = exec_sql_query($db, $sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    // Select $records[0] because only one file exists
    $image_ext = $records[0]["image_ext"];
    $path = "uploads/documents/" . "$delete_image_id.$image_ext";
    unlink($path);

    // Delete image from images table
    $sql = "DELETE FROM images WHERE id = :delete_image_id";
    $params = array(':delete_image_id' => $delete_image_id);
    exec_sql_query($db, $sql, $params);

    // Delete image from tag table
    $sql = "DELETE FROM image_tags WHERE image_id = :delete_image_id";
    $params = array(":delete_image_id" => $delete_image_id);
    exec_sql_query($db, $sql, $params);

    add_message("You have deleted the image");
}

$records = exec_sql_query($db, "SELECT * FROM images WHERE users_id = :users_id;", array(':users_id' => $current_user['id']))->fetchAll();

if (isset($_POST['add_tag'])) {
    $id_pic = filter_input(INPUT_POST, 'add_tag', FILTER_VALIDATE_INT);

    $sql = "SELECT * FROM images WHERE id = :id;";
    $params = array(':id' => $id_pic);
    $images = exec_sql_query($db, $sql, $params);

    // If successful, get records from database
    if ($images) {
        $pic = $images->fetchAll();
        // count() checks number of element in array
        if (count($pic) > 0) {
            // Take first element because only one element exists
            $pics = $pic[0];
        }
    }
}

// adding new tag
if (isset($_POST['submit_tag'])) {

    $tag_filter = filter_input(INPUT_POST, "a_tag", FILTER_SANITIZE_STRING);
    $exist_tag = filter_input(INPUT_POST, "exist_tag", FILTER_SANITIZE_NUMBER_INT);
    $tags = exec_sql_query($db, "SELECT * FROM tags", array())->fetchAll();

    // Find id of image wanting to insert the tag into
    $filter_id = filter_input(INPUT_POST, "add_tag", FILTER_SANITIZE_NUMBER_INT);
    $sql_find = "SELECT * FROM images WHERE id = :find_id;";
    $params_find = array(":find_id" => $filter_id);
    $records_find = exec_sql_query($db, $sql_find, $params_find)->fetchAll(PDO::FETCH_ASSOC);

    if ($tag_filter == NULL && $exist_tag == NULL) {
        add_message('Please select tag or add a tag');
    } else {
        if (isset($_POST["exist_tag"])) {
            $sql_insert_e_tag = "INSERT INTO image_tags (images_id, tag_id) VALUES (:records_find, :exist_tag_id)";
            $params_insert_e_tag = array(
                ":records_find" => $records_find[0]["id"],
                ":exist_tag_id" => $exist_tag
            );
            exec_sql_query($db, $sql_insert_e_tag, $params_insert_e_tag);
            if ($tag_filter == NULL) {
                add_message('Added Existing Tag to Image');
            }
        }

        if (isset($_POST["a_tag"])) {
            // Filter tag input element
            $convert_tag_filter = strtolower(trim($tag_filter));

            // Check all tags to check for duplicate
            $all_tags = exec_sql_query($db, "SELECT * FROM tags", array())->fetchAll(PDO::FETCH_ASSOC);
            $check_duplicate = array();
            foreach ($all_tags as $tag) {
                array_push($check_duplicate, strtolower(trim($tag["tag"])));
            }

            // Check if tag is unique
            if (!in_array($convert_tag_filter, $check_duplicate)) {
                // Insert tag into tags table
                $sql = "INSERT INTO tags (tag) VALUES (:new_tag);";
                $params = array(":new_tag" => $tag_filter);
                exec_sql_query($db, $sql, $params);

                // Get id of last tag inserted
                $sql_id = "SELECT * FROM tags WHERE tag = :tags";
                $params_id = array(":tags" => $tag_filter);
                $new_tag_id = exec_sql_query($db, $sql_id, $params_id)->fetchAll(PDO::FETCH_ASSOC);

                // Insert tag and image_id into image_tags table
                $sql_insert = "INSERT INTO image_tags (images_id, tag_id) VALUES (:image_id, :tag_id)";
                $params_insert = array(
                    ":image_id" => $records_find[0]["id"],
                    ":tag_id" => $new_tag_id[0]["id"]
                );
                exec_sql_query($db, $sql_insert, $params_insert);
                if ($exist_tag == NULL) {
                    add_message('Successfully added Tag');
                }
            } else {
                if ($exist_tag == NULL) {
                    add_message('Tag Already exists');
                } else {
                    add_message('Tag Already Exists. Inserted Existing Tag');
                }
            }
        }
    }
}

$tags = exec_sql_query($db, "SELECT * FROM tags", array())->fetchAll();

// Get tags of each picture
$sql_for_tag = "SELECT tags.tag FROM tags LEFT OUTER JOIN image_tags ON tags.id = image_tags.tag_id LEFT OUTER JOIN images ON image_tags.images_id = images.id WHERE images_id = :tag";
$params_for_tag = array(":tag" => $pics['id']);
$tag_element = exec_sql_query($db, $sql_for_tag, $params_for_tag)->fetchAll(PDO::FETCH_ASSOC);

// Deleting tag
if (isset($_POST['delete_tag'])) {
    $image_id = filter_input(INPUT_POST, "delete_tag_id", FILTER_SANITIZE_STRING);
    $delete_tag_id = filter_input(INPUT_POST, "d_tag", FILTER_SANITIZE_STRING);

    $sql_for_tag = "SELECT tags.id FROM tags LEFT OUTER JOIN image_tags ON tags.id = image_tags.tag_id LEFT OUTER JOIN images ON image_tags.images_id = images.id WHERE images_id = :tag";
    $params_for_tag = array(":tag" => $image_id);
    $tag_element = exec_sql_query($db, $sql_for_tag, $params_for_tag)->fetchAll(PDO::FETCH_ASSOC);

    $tag_list = array();
    foreach ($tag_element as $tag) {
        array_push($tag_list, $tag["id"]);
    }

    if ($delete_tag_id == NULL) {
        add_message("Please Select Tag to Delete");
    } else {
        // Check if tag exists
        if (in_array($delete_tag_id, $tag_list)) {
            $sql_find = "SELECT * FROM images WHERE id = :find_id;";
            $params_find = array(":find_id" => $image_id);
            $records_find = exec_sql_query($db, $sql_find, $params_find)->fetchAll(PDO::FETCH_ASSOC);

            // Delete tag from image_tags table
            $sql_delete = "DELETE FROM image_tags WHERE images_id = :selected_image_id AND tag_id = :delete_tag_id;";
            $params_delete = array(
                ":selected_image_id" => $records_find[0]["id"],
                ":delete_tag_id" => $delete_tag_id
            );
            $tag_index = exec_sql_query($db, $sql_delete, $params_delete);

            // If the image_tags table doesn't have the tag delete it from the tag table
            // $sql_delete_unincluded = "DELETE FROM tags WHERE id = :delete_tag_id;";
            // $params_delete_unincluded = array(":delete_tag_id" => $delete_tag_id);
            // exec_sql_query($db, $sql_delete_unincluded, $params_delete_unincluded);
            add_message("Removed Tag");
        } else {
            add_message("Tag is not included in Photo");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include("includes/head.php"); ?>


<body>
    <?php if (is_user_logged_in()) { ?>
    <div class="flex">
        <?php include("includes/sidebar.php") ?>

        <div class="push_left">
            <!--
            #############################################
            Form to add new image
            #############################################
            -->
            <form id="upload_image" action="edit.php" method="post" enctype="multipart/form-data">
                <h3>Upload Image</h3>

                <div>
                    <div>
                        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>" />
                        <label for="box_file">File:</label>
                        <input id="box_file" type="file" name="box_file">
                    </div>
                    <div>
                        <label>Title: </label>
                        <input type="text" name="title">
                    </div>
                </div>

                <button name="submit_upload" type="submit">Upload File</button>

            </form>

            <!--
            #############################################
            Form to delete new image
            #############################################
            -->
            <form id="delete_image" action="edit.php" method="post">
                <h3>Delete Image</h3>
                <select name="delete_image_id">
                    <option value="" selected disabled>Select Image</option>
                    <?php foreach ($records as $record) { ?>
                    <option value="<?php echo $record['id']; ?>"><?php echo $record["title"]; ?></option>
                    <?php

                } ?>
                </select>
                <button id="delete" name="delete_image" type="submit">Delete</button>
            </form>

            <?php if (isset($_POST["delete_image"])) {
                echo "<p>You have deleted the image</p>";
            } ?>


            <!--
            #############################################
            Form to add new tag
            #############################################
             -->
            <form id="new_tag" action="edit.php" method="post">
                <h3>Add tags</h3>
                <div>
                    <div>
                        <select name="add_tag" required>
                            <option value="" selected disabled>Select Image</option>
                            <?php foreach ($records as $record) { ?>
                            <option value="<?php echo $record['id']; ?>"><?php echo $record["title"]; ?></option>
                            <?php

                        } ?>
                        </select>
                    </div>
                    <div>
                        <p>Select Tag: </p>
                        <select name="exist_tag">
                            <option value="" selected disabled>Select Tag</option>
                            <?php
                            foreach ($tags as $tag) {
                                echo "<option value=" . $tag["id"] . ">" . $tag["tag"] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <p>Add new tag:</p>
                        <input type="text" name="a_tag">
                    </div>
                </div>
                <button name="submit_tag" type="submit">Add Tag</button>
                <?php if (isset($_POST['submit_tag'])) { ?>
                <p class='red'><?php show_message() ?></p>
                <?php

            } ?>
            </form>

            <!--
            #############################################
            Form to delete new tag
            #############################################
            -->
            <form id="delete_tag" action="edit.php" method="post">
                <h3>Remove tags</h3>
                <div>
                    <div>
                        <select name="delete_tag_id">
                            <option value="" selected disabled>Select Image</option>
                            <?php foreach ($records as $record) { ?>
                            <option value="<?php echo $record['id']; ?>"><?php echo $record["title"]; ?></option>
                            <?php

                        } ?>
                        </select>
                    </div>
                    <div>
                        <select name="d_tag">
                            <option value="" selected disabled>Select Tag</option>
                            <?php foreach ($tags as $tag) { ?>
                            <option value="<?php echo $tag["id"]; ?>"><?php echo $tag["tag"]; ?></option>
                            <?php

                        } ?>
                        </select>
                    </div>
                </div>
                <button name="delete_tag" type="submit">Delete Tag</button>
                <?php if (isset($_POST['delete_tag'])) { ?>
                <p class='red'><?php show_message() ?></p>
                <?php

            } ?>
            </form>

            <!--
            #############################################
            Form to search image
            #############################################
            -->
            <form action="edit.php" method="get">
                <h3>Search Image</h3>
                <select name="search_for">
                    <option value="" selected disabled>Select Image</option>
                    <?php foreach ($records as $record) { ?>
                    <option value="<?php echo $record['id']; ?>"><?php echo $record["title"]; ?></option>
                    <?php

                } ?>
                </select>
                <button name="search" type="submit">Search</button>
            </form>

            <p><a href="edit.php">Show all pictures</a></p>

            <?php if (!isset($_GET["search"])) { ?>
            <h3>Saved Files</h3>
            <?php
            if (count($records) > 0) {
                foreach ($records as $record) {
                    echo "<Strong>" . $record["title"] . "</Strong>";
                    echo "<p><img src= " . UPLOAD_PATH . $record["id"] . "." . $record["image_ext"] . " alt = '".htmlspecialchars($record["title"])."'/></p>";
                }
            } else {
                echo ("<p>Upload images to see saved files</p>");
            }
        } else {
            echo "<p><img src= " . UPLOAD_PATH . htmlspecialchars($_GET["search_for"]) . "." . $record["image_ext"] . "/></p>";
        }
        ?>
            <h3>All Images Taken by Emily Wu</h3>
            <!-- Source: All Seed picture taken by Emily Wu -->
        </div>
    </div>
    <?php

} else {
    add_message("Invalid Username or Password");
    ?>

    <div class = "center">
        <p><?php show_message() ?></p>
        <a href='login.php'>Go Back to Login Page</a>
    </div>

    <?php
} ?>
</body>

</html>
