<?php
 // INCLUDE ON EVERY TOP-LEVEL PAGE!
include("includes/init.php");

$current_page = 'index.php';

$sql = "SELECT * FROM images";
$params = array();
$records = exec_sql_query($db, $sql, $params)->fetchAll(PDO::FETCH_ASSOC);

$tags = exec_sql_query($db, "SELECT * FROM tags", array())->fetchAll();

/**
 * Changes photo in a string query
 */
function print_photo_gallery($search)
{
  echo '<a href="single_image.php?' . http_build_query(array('id' => $search['id'])) . '"><img src="uploads/documents/' . $search['id'] . "." . $search["image_ext"] . '" alt= "' . htmlspecialchars($search['title']) . '"/></a>' . PHP_EOL;
}

$search = FALSE;

if (isset($_GET['search'])) {
  $search_tag = filter_input(INPUT_GET, "search_image", FILTER_SANITIZE_STRING);

  if ($search_tag != NULL) {
    $search = TRUE;

    $sql_select = "SELECT images.id, images.image_ext, images.title FROM images LEFT OUTER JOIN image_tags ON images.id = image_tags.images_id LEFT OUTER JOIN tags ON image_tags.tag_id = tags.id WHERE tags.id = :tag_id";
    $params_select = array(":tag_id" => $search_tag);
    $tag_selected = exec_sql_query($db, $sql_select, $params_select);

    if ($tag_selected) {
      $fetch_image = $tag_selected->fetchAll(PDO::FETCH_ASSOC);
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
            <form action="index.php" method="get">
                <label>Tag:</label>
                <select name="search_image">
                    <option value="" selected disabled>Search by Tag</option>
                    <?php
                    foreach ($tags as $tag) {
                      echo "<option value=" . $tag["id"] . ">" . $tag["tag"] . "</option>";
                    }
                    ?>
                </select>
                <button id="delete" name="search" type="submit">Search</button>
            </form>

            <?php
            if ($search == FALSE) {
              if (isset($records)) {
                foreach ($records as $record) {
                  echo "<h3> " . $record['title'] . "</h3>";
                  print_photo_gallery($record);
                }
              }
            } else {
              foreach ($fetch_image as $record) {
                echo "<h3> " . $record['title'] . "</h3>";
                print_photo_gallery($record);
              }
            }
            ?>

            <div>
                <a href="index.php">Show All Pictures</a>
            </div>

            <h3 class="move_down">Source: All pictures taken by Emily Wu</h3>
            <!-- Source: All seed images taken by Emily Wu -->

        </div>
    </div>

    </body>

    </html>
