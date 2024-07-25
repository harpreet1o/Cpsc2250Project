<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include_once "./includes/database.inc.php";

// Check if $pdo is set and connected
if ($pdo === null) {
    die("Connection failed.");
}

$groupId = isset($_GET['groupId']) ? $_GET['groupId'] : null;
$groupName = '';

// Fetch group details
if ($groupId) {
    $sql = "SELECT groupName FROM groups WHERE groupId = :groupId";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['groupId' => $groupId]);
        $group = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($group) {
            $groupName = $group['groupName'];
        } else {
            echo "Group not found.";
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

// Handle form submissions to invite new users
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['inviteUser'])) {
    $newUserId = $_POST['userId'];
    $sql = "INSERT INTO user_group (userId, groupId) VALUES (:userId, :groupId)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['userId' => $newUserId, 'groupId' => $groupId]);
        echo "User invited successfully.";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Handle form submissions to add expenses
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addExpense'])) {
    $userId = $_POST['userId'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $sql = "INSERT INTO expenses (userId, groupId, amount, description) VALUES (:userId, :groupId, :amount, :description)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['userId' => $userId, 'groupId' => $groupId, 'amount' => $amount, 'description' => $description]);
        echo "Expense added successfully.";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetch users for the invite and expense forms
$sql = "SELECT userId, username FROM user";
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $users = [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group: <?php echo htmlspecialchars($groupName); ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fjalla+One&display=swap');

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            display: flex;
            height: 100vh;
            position: relative;
        }

        .background {
            width: 100%;
            height: 25%;
            background-color: #555555;
            position: absolute;
            top: 0;
            left: 0;
        }

        .letters {
            position: absolute;
            top: 0;
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            font-family: 'Fjalla One', sans-serif;
        }

        .letters .letter {
            font-size: 6em;
            font-weight: bold;
            color: black;
            padding: 20px;
            width: 120px;
            height: 120px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #333333; /* Dark grey background */
        }

        .letters .letter:nth-child(even) {
            margin-left: 120px;
        }

        .main-content {
            width: 70%;
            background-color: #3a3a3a;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            margin-top: 25%;
            position: absolute;
            top: 25%;
            left: 15%;
        }

        .form-output-container {
            display: flex;
            width: 100%;
            justify-content: space-between;
        }

        .form-container,
        .output-container {
            background-color: #555555;
            padding: 30px;
            border-radius: 8px;
            width: 45%;
        }

        .form-container h1,
        .output-container h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-container form,
        .output-container {
            display: flex;
            flex-direction: column;
        }

        .form-container label,
        .form-container input,
        .form-container select,
        .form-container button,
        .output-container label,
        .output-container input,
        .output-container select,
        .output-container button {
            margin: 15px 0;
        }

        .form-container button,
        .output-container button {
            background-color: #ff5722;
            color: white;
            border: none;
            padding: 12px;
            cursor: pointer;
        }

        .form-container button:hover,
        .output-container button:hover {
            background-color: #e64a19;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: white;
            text-decoration: none;
        }

        .right-letters {
            position: absolute;
            top: 50%;
            right: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            transform: translateY(-50%);
            font-family: 'Fjalla One', sans-serif;
        }

        .right-letters .letter {
            font-size: 4em;
            font-weight: bold;
            color: orange;
            padding: 20px;
            background-color: #333333;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="background"></div>
        <div class="letters">
            <div class="letter">S</div>
            <div class="letter">P</div>
            <div class="letter">L</div>
            <div class="letter">I</div>
            <div class="letter">T</div>
        </div>
        <div class="main-content">
            <div class="form-output-container">
                <div class="form-container">
                    <h1>Group: <?php echo htmlspecialchars($groupName); ?></h1>

                    <!-- Invite User Form -->
                    <form method="post">
                        <h2>Invite User</h2>
                        <label for="userId">Select User</label>
                        <select name="userId" id="userId" required>
                            <?php
                            foreach ($users as $user) {
                                echo "<option value='" . htmlspecialchars($user['userId']) . "'>" . htmlspecialchars($user['username']) . "</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" name="inviteUser">Invite User</button>
                    </form>

                    <!-- Add Expense Form -->
                    <form method="post">
                        <h2>Add Expense</h2>
                        <label for="userId">Select User</label>
                        <select name="userId" id="userId" required>
                            <?php
                            foreach ($users as $user) {
                                echo "<option value='" . htmlspecialchars($user['userId']) . "'>" . htmlspecialchars($user['username']) . "</option>";
                            }
                            ?>
                        </select>
                        <label for="amount">Amount</label>
                        <input type="number" name="amount" id="amount" step="0.01" required>
                        <label for="description">Description</label>
                        <input type="text" name="description" id="description" required>
                        <button type="submit" name="addExpense">Add Expense</button>
                    </form>

                    <div class="back-link">
                        <a href="protected_page.php">Back to Protected Page</a>
                    </div>
                </div>
                <div class="output-container">
                    <h1>Output</h1>
                    <!-- This section will display the outputs such as success messages, errors, etc. -->
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        if (isset($_POST['inviteUser'])) {
                            echo "User invited successfully.";
                        }
                        if (isset($_POST['addExpense'])) {
                            echo "Expense added successfully.";
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="right-letters">
            <div class="letter">G</div>
            <div class="letter">R</div>
            <div class="letter">O</div>
            <div class="letter">U</div>
            <div class="letter">P</div>
        </div>
    </div>
</body>
</html>
