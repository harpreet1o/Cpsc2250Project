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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
<form method="post" action="./includes/validationForGroup.php">
        <label for="groupName">Group Name</label>
        <input type="text" name="groupName" id="groupName" required>
        <!-- <label for="groupName">invite the users</label>
        <input type="text"> -->
        
        <button type="submit">Submit</button>
</body>
</html>