<?php
 // INCLUDE ON EVERY TOP-LEVEL PAGE!
include("includes/init.php");

add_message("You have been logged out");
$current_page = "logout.php";
?>
<!DOCTYPE html>
<html lang="en">

<?php include("includes/head.php"); ?>

<body>
    <div>
        <!-- Return all image -->
        <h1 class="logout">Logged Out</h1>
        <p class="center"><?php show_message()?></p>
        <p class="center">Click <a href="index.php">here</a> to return to Home page</p>
    </div>





</body>

</html>
