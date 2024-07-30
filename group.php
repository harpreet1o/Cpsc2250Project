<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include_once "./includes/database.inc.php";


$groupId=$_GET['groupId'];
$groupName=$_GET['groupName'];

$query = "SELECT u.username,u.userId
FROM user u
JOIN user_group ug ON u.userId = ug.userId
WHERE ug.groupId = :groupId;";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':groupId', $groupId);
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $sql = "SELECT listId, groupId, createdBy, completedBy, ItemName
                FROM group_list
                WHERE groupId = :groupId";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['groupId' => $groupId]);
        $listItems = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Handle form submissions to invite new users
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    // include_once "./includes/validationForGroup.php";
    // for_email();
    $Email = $_POST['email'];
    try {
        // Query to get the userId for the given email
        $query = "SELECT userId FROM user WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $Email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $userId = $user['userId'];
            // Query to insert into user_group table
            $sql = "INSERT INTO user_group (userId, groupId) VALUES (:userId, :groupId)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['userId' => $userId, 'groupId' => $groupId]);

            $message = "User invited successfully.";
        } else {
            $message = "No user found with the provided email address.";
        }

    } catch (PDOException $e) {
        $message = "Error: " . htmlspecialchars($e->getMessage());
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addItem'])) {
    $itemName = $_POST['itemName'];
    $createdBy=$_SESSION['username'];
    $sql = "INSERT INTO group_list (groupId, createdBy, ItemName) VALUES (:groupId, :createdBy, :itemName)";
    $stmt = $pdo->prepare($sql);

    // Execute the statement with bound parameters
    $stmt->execute([
        'groupId' => $groupId,
        'createdBy' => $createdBy,
        'itemName' => $itemName
    ]);
    // Handle form submission to mark an item as completed
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['listId'])) {
    $listId = $_POST['listId'];
    $completedBy = $_SESSION['username'];

    // Prepare the SQL statement
    $sql = "UPDATE group_list SET completedBy = :completedBy WHERE listId = :listId";
    $stmt = $pdo->prepare($sql);

    // Execute the statement with bound parameters
    $stmt->execute([
        'completedBy' => $completedBy,
        'listId' => $listId
    ]);

    // Redirect back to the list items page
    header("Location: " . $_SERVER['PHP_SELF'] . "?groupId=" . $groupId . "&groupName=" . $groupName);
    exit();
}


}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['listId']) && isset($_POST['markCompleted'])) {
    $listId = $_POST['listId'];
    $completedBy = $_SESSION['username'];

    // Prepare the SQL statement
    $sql = "UPDATE group_list SET completedBy = :completedBy WHERE listId = :listId";
    $stmt = $pdo->prepare($sql);

    // Execute the statement with bound parameters
    $stmt->execute([
        'completedBy' => $completedBy,
        'listId' => $listId
    ]);

    // Redirect back to the list items page
    header("Location: " . $_SERVER['PHP_SELF'] . "?groupId=" . $groupId . "&groupName=" . $groupName);
    exit();
}
// Handle form submissions to add expenses
if (isset($_POST['addExpense'])) {
    $payerUserId = $_POST['userId'];
    $userIds = $_POST['userIds'];
    $amount = $_POST['amount'];
    $descriptione = $_POST['description'];

$sql = "INSERT INTO expenses (groupId, userId, amount, descriptione) VALUES (:groupId, :userId, :amount, :descriptione)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'groupId' => $groupId, 
    'userId' => $payerUserId, 
    'amount' => $amount, 
    'descriptione' => $descriptione
]);
$expenseId = $pdo->lastInsertId();
$numUsers = count($userIds);
$shareAmount = $amount / $numUsers;
$sqlShare = "INSERT INTO expense_shares (expenseId, userId, amountOwed) VALUES (:expenseId, :userId, :amountOwed)";
$stmtShare = $pdo->prepare($sqlShare);
foreach ($userIds as $userId) {
    $amountOwed = ($userId == $payerUserId) ? 0 : $shareAmount;
    $stmtShare->execute([
        'expenseId' => $expenseId, 
        'userId' => $userId, 
        'amountOwed' => $amountOwed
    ]);
}
}
$sql = "SELECT e.expenseId, e.amount, e.descriptione, u.username AS payer,
             COALESCE(SUM(es.amountOwed), 0) AS amountDue
        FROM expenses e
        JOIN user u ON e.userId = u.userId
        LEFT JOIN expense_shares es ON e.expenseId = es.expenseId
        WHERE e.groupId = :groupId
        GROUP BY e.expenseId, e.amount, e.descriptione, u.username";

$stmt = $pdo->prepare($sql);
$stmt->execute(['groupId' => $groupId]);

$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                        <!-- <select name="userId" id="userId" required>
                            <?php
                            foreach ($users as $user) {
                                echo "<option value='" . htmlspecialchars($user['userId']) . "'>" . htmlspecialchars($user['username']) . "</option>";
                            }
                            ?>
                        </select> -->
                        <input type="email" id="email" name="email" placeholder="email for the user">
                        <button type="submit" name="inviteUser">Invite User</button>
                    </form>

                    <!-- Add Expense Form -->
                    <form method="post">
                        <h2>Add Expense</h2>
                        <label for="userId">Select User to pay</label>
                        <select name="userId" id="userId" required>
                            <?php
                            foreach ($users as $user) {
                                echo "<option value='" . htmlspecialchars($user['userId']) . "'>" . htmlspecialchars($user['username']) . "</option>";
                            }
                            ?>
                        </select>
                        <label for ="userIds[]">Select User who are going to pay</label>
                        <?php foreach ($users as $user): ?>
    <input type="checkbox" name="userIds[]" id="user_<?= htmlspecialchars($user['userId']) ?>" value="<?= htmlspecialchars($user['userId']) ?>">
    <label for="user_<?= htmlspecialchars($user['userId']) ?>"><?= htmlspecialchars($user['username']) ?></label><br>
<?php endforeach; ?>

                        <label for="amount">Amount</label>
                        <input type="number" name="amount" id="amount" step="0.01" required>
                        <label for="description">Description</label>
                        <input type="text" name="description" id="description" required>
                        <button type="submit" name="addExpense">Add Expense</button>
                    </form>
                    <form method="post" >
        <label for="Item">Enter the item needed</label>
        <input type="text" id="itemName" name="itemName" required>
        <br>

        <button type="submit" name="addItem">Add List Item</button>
                        </form>

                    <div class="back-link">
                        <a href="home.php">Back to Protected Page</a>
                    </div>
                </div>
                <div class="output-container">
                    <h1>Output</h1>
                    <table>
    <thead>
        <tr>
            <th>Amount</th>
            <th>Payer</th>
            <th>Description</th>
            <th>Amount Due</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($expenses as $expense): ?>
            <tr>
                <td><?php echo htmlspecialchars($expense['amount']); ?></td>
                <td><?php echo htmlspecialchars($expense['payer']); ?></td>
                <td><?php echo htmlspecialchars($expense['descriptione']); ?></td>
                <td><?php echo htmlspecialchars($expense['amountDue']); ?></td>
                <td>
                                     <?php 
echo "<a href='expense_details.php?groupId=" . urlencode($groupId) .
     "&groupName=" . urlencode($groupName) .
     "&expenseId=" . urlencode($expense['expenseId']) .
     "&payerId=" . urlencode($expense['payer']) . 
     "'>" .  "view details</a>";
?>

                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<h3>Existing Items</h3>
<table border="1">
        <thead>
            <tr>
                <th>Index</th>
                <th>Item Name</th>
                <th>Created By</th>
                <th>Completed By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listItems as $index => $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($index + 1); ?></td>
                    <td><?php echo htmlspecialchars($item['ItemName']); ?></td>
                    <td><?php echo htmlspecialchars($item['createdBy']); ?></td>
                    <td><?php echo htmlspecialchars($item['completedBy']); ?></td>
                    <td>
                        <?php if ($item['completedBy'] == NULL): ?>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?groupId=<?php echo htmlspecialchars($groupId); ?>&groupName=<?php echo htmlspecialchars($groupName); ?>" style="display:inline;">
                                <input type="hidden" name="listId" value="<?php echo htmlspecialchars($item['listId']); ?>">
                                <input type="hidden" name="markCompleted" value="1">
                                <button type="submit">Mark as Completed</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
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
