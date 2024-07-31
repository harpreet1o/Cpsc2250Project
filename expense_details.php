<?php
include_once "./includes/database.inc.php";
session_start();
$loggedInUserId = $_SESSION['username'];
$expenseId = $_GET['expenseId'];
$groupId = $_GET['groupId'];
$groupName = $_GET['groupName'];
$payerId = $_GET['payerId'];

// Fetch expense details
$sql = "SELECT e.amount, e.descriptione, u.username AS payer
        FROM expenses e
        JOIN user u ON e.userId = u.userId
        WHERE e.expenseId = :expenseId";
$stmt = $pdo->prepare($sql);
$stmt->execute(['expenseId' => $expenseId]);
$expense = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch who has paid and who hasn't
$sqlShares = "SELECT u.userId, u.username, es.amountOwed
              FROM expense_shares es
              JOIN user u ON es.userId = u.userId
              WHERE es.expenseId = :expenseId";
$stmtShares = $pdo->prepare($sqlShares);
$stmtShares->execute(['expenseId' => $expenseId]);
$shares = $stmtShares->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Details</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Platypi:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@48,400,0,0" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <style>

body {
    font-family: 'Inter', sans-serif;
    background-color: #1E1E1E; 
    color: #f5f5f5; 
    margin: 0;
    padding: 0;
}

.container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 1rem;
    background-color: #2E2E2E; 
    border-radius: 0.8rem;
    box-shadow: 0 0.4rem 0.8rem rgba(0, 0, 0, 0.2);
    color: #f5f5f5; 
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

thead th {
    background-color: #7C90DB; 
    color: #fff;
    padding: 0.8rem;
    text-align: left;
}

tbody td {
    padding: 0.8rem;
    border-bottom: 1px solid #444; 
}

tbody tr:nth-child(even) {
    background-color: #2E2E2E; 
}

form {
    margin-top: 1rem;
}

form table {
    margin-top: 1rem;
}

form button {
    background-color: #7C90DB; /* Primary color */
    color: #fff;
    padding: 0.8rem 1.6rem;
    border: none;
    border-radius: 0.4rem;
    font-size: 1.2rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

form button:hover {
    background-color: #4a5683; /* Darker shade for hover */
}

.back-button a {
    text-decoration: none;
    color: #fff;
}

.back-button {
    background-color: #7C90DB; /* Primary color */
    color: #fff;
    padding: 0.8rem 1.6rem;
    border: none;
    border-radius: 0.4rem;
    font-size: 1.2rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-top: 1rem;
    display: inline-block;
}

.back-button:hover {
    background-color: #4a5683; /* Darker shade for hover */
}

    </style>
</head>
<body>
    <?php include_once "./navbar.php"; ?>

    <div class="container">
        <h2>Expense Details</h2>
        <p><strong>Amount:</strong> <?php echo htmlspecialchars($expense['amount']); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($expense['descriptione']); ?></p>
        <p><strong>Payer:</strong> <?php echo htmlspecialchars($expense['payer']); ?></p>

        <?php if ($loggedInUserId == $payerId): ?>
            <form method="post" action="update_payments.php?groupId=<?php echo urlencode($groupId); ?>&groupName=<?php echo urlencode($groupName); ?>&payerId=<?php echo urlencode($payerId); ?>">
                <input type="hidden" name="expenseId" value="<?php echo htmlspecialchars($expenseId); ?>">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Amount Owed</th>
                            <th>Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($shares as $share): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($share['username']); ?></td>
                                <td><?php echo htmlspecialchars($share['amountOwed']); ?></td>
                                <td>
                                    <input type="checkbox" name="paidUsers[]" value="<?php echo htmlspecialchars($share['userId']); ?>"
                                    <?php if ($share['amountOwed'] == 0) echo 'checked'; ?>>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit">Update Payments</button>
            </form>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Amount Owed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($shares as $share): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($share['username']); ?></td>
                            <td><?php echo htmlspecialchars($share['amountOwed']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p>You do not have permission to make changes.</p>
        <?php endif; ?>

        <button class="back-button">
            <a href="group.php?groupId=<?php echo urlencode($groupId); ?>&groupName=<?php echo urlencode($groupName); ?>">Go Back</a>
        </button>
    </div>
</body>
</html>
