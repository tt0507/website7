<?php
 // Use this file to hash passwords for your database.
// You should not use it for your live site.
if (isset($_GET['password']) && trim($_GET['password']) != '') {
    $password = trim($_GET["password"]);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Hash Password</title>
</head>

<body>
    <p>
        Use this form to hash passwords for your seed data.
    </p>
    <p>
        <strong>You should not use this file for a live site.</strong>
    </p>

    <?php
    if (isset($password) && isset($hashed_password)) { ?>
    <h2>Hashed Password</h2>

    <?php
    echo '<p>' . htmlspecialchars($password) . ' → ' . htmlspecialchars($hashed_password) . '</p>';
} ?>

    <h2>Hash a Password</h2>

    <form method="get" action="hash_password.php">
        <label for="password">Password:</label>
        <input id="password" type="text" name="password" />

        <input type="submit" value="Hash Password" />
    </form>

</body>

</html>
