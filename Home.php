<?php
session_start();
if (!isset($_SESSION['username'])) {
    // If not, redirect to the login page
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Protected Page</title>
</head>
<body>
    <h1>Welcome, <?php echo ($_SESSION['username']); ?>!</h1>
    <button><a href="./group.php">Create a new Group</a></button>

</body>
</html>
