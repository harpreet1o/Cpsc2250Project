<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include_once "./includes/database.inc.php";

// Check if $pdo is set
if ($pdo === null) {
    die("Connection failed.");
}

// Updated SQL query to reflect the new database schema

try {
    $sql = "SELECT g.groupName, g.groupId
        FROM user_group ug
        JOIN groups g ON ug.groupId = g.groupId
        WHERE ug.userId=:userId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $_SESSION['userId']);
    $stmt->execute();

    $Groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $Groups = [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Protected Page</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <button><a href="./createGroup.php">Create a new Group</a></button>

    <?php
    if (!empty($Groups)) {
        echo "<ul>";
        foreach ($Groups as $group) {
            echo " - Group Name: <a href='group.php?groupId=$group[groupId]&groupName=$group[groupName]'>" . htmlspecialchars($group["groupName"]) . "</a></li>";
        }
        echo "</ul>";
    } else {
        echo "0 results";
    }

    $pdo = null; // Close the connection
    ?>
</body>
</html>
