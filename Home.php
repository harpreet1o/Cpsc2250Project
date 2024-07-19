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
$sql = "SELECT u.userId, u.username, g.groupId, g.groupName 
        FROM user u
        JOIN user_group ug ON u.userId = ug.userId
        JOIN groups g ON ug.groupId = g.groupId";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $rows = [];
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
    <button><a href="./group.php">Create a new Group</a></button>

    <?php
    if (!empty($rows)) {
        echo "<ul>";
        foreach ($rows as $row) {
            echo "<li>UserID: " . htmlspecialchars($row["userId"]) . " - Username: " . htmlspecialchars($row["username"]) . " - Group Name: <a href='group.php?id=" . htmlspecialchars($row["groupId"]) . "'>" . htmlspecialchars($row["groupName"]) . "</a></li>";
        }
        echo "</ul>";
    } else {
        echo "0 results";
    }

    $pdo = null; // Close the connection
    ?>
</body>
</html>
