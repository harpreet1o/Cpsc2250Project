<?php
include_once "./includes/database.inc.php";
session_start();
$loggedInUserId =$_SESSION['username'];
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

<h2>Expense Details</h2>
<p>Amount: <?php echo htmlspecialchars($expense['amount']); ?></p>
<p>Description: <?php echo htmlspecialchars($expense['descriptione']); ?></p>
<p>Payer: <?php echo htmlspecialchars($expense['payer']); ?></p>

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

<button>
    <?php
        echo "<a href='group.php?groupId=" . urlencode($groupId) . "&groupName=" . urlencode($groupName) . "'>Go Back</a>";
    ?>
</button>
