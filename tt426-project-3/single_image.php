<?php
 // INCLUDE ON EVERY TOP-LEVEL PAGE!
include("includes/init.php");

$db = open_or_init_sqlite_db("secure/site.sqlite", "secure/init.sql");

$current_page = 'single_image.php';

// Find photo ID
if (isset($_GET['id'])) {
    $id_pic = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

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


?>
<!DOCTYPE html>
<html lang="en">

<?php include("includes/head.php"); ?>

<body>
    <div class="flex">
        <?php include("includes/sidebar.php") ?>

        <div class="gallery">
            <?php if (isset($pics)) { ?>

            <h3><?php echo htmlspecialchars($pics["title"]) ?></h3>

            <?php

            echo '<img src="uploads/documents/' . $pics['id'] . "." . $pics["image_ext"] . '" alt= "' . htmlspecialchars($search['title']) . '"/>';

            // Fetch tag from database corresponding to photo
            $sql_for_tag = "SELECT tags.tag FROM tags LEFT OUTER JOIN image_tags ON tags.id = image_tags.tag_id LEFT OUTER JOIN images ON image_tags.images_id = images.id WHERE images_id = :tag";
            $params_for_tag = array(":tag" => $pics["id"]);
            $tag_element = exec_sql_query($db, $sql_for_tag, $params_for_tag)->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <h3>Tags:</h3>
            <?php
            foreach ($tag_element as $tag) {
                echo "<li class = 'bullet_point'>" . $tag["tag"] . "</li>";
            }
            ?>

            <p><a href="index.php">Show all pictures</a></p>

            <h3>Picture Taken by Emily Wu</h3>
            <!-- Source: All seed picture taken by Emily Wu -->
            <?php

        } else { ?>

            <p>The Food does not exist.</p>
            <?php
        } ?>

        </div>
    </div>

</body>

</html>
