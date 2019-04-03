<?php
 // INCLUDE ON EVERY TOP-LEVEL PAGE!
include("includes/init.php");
?>
<!DOCTYPE html>
<html lang="en">

<?php include("includes/head.php"); ?>

<body>

    <h1 class="center">Foods at Ithaca</h1>
    <div class="login">
        <h1 class="shift_right">Login</h1>

        <?php show_message();?>

        <form id="login" method="post" action="edit.php">
            <ul>
                <Li>
                    <label>Username: </label>
                    <input type="text" name="username" required></Li>
                <li>
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </li>
            </ul>

            <button id="button" type="submit" name="login">Log In</button>
        </form>
    </div>
    <p class="move">Click <a href="index.php">here</a> to return to Home page</p>


</body>

</html>
