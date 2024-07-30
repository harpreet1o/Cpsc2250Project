
<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$groupId=$_GET['groupId'];
$groupName=$_GET['groupName'];
include_once "./includes/database.inc.php";
$query = "SELECT u.username,u.userId
FROM user u
JOIN user_group ug ON u.userId = ug.userId
WHERE ug.groupId = :groupId;";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':groupId', $groupId);
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $pdo = null;
                $stmt = null;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php 
    echo $groupName;
    ?>
    <p>User names</p>
    <?php  foreach ($users as $user) {
                echo "<option value='" . htmlspecialchars($user['userId']) . "'>" . htmlspecialchars($user['username']) . "</option>";
            }
                            ?>

<form method="post" action="./includes/validationEmail.php">
        <label for="addUser">Email</label>
        <input type="email" name="email" id="email" required>
        <button type="submit">Submit</button>
    </form>
    
</body>
</html>