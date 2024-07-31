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
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Platypi:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@48,400,0,0" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <link rel="stylesheet" href="css/style.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(function () {
        function openForm(formClass) {
            $(formClass).fadeIn();
            $(formClass).find("input").first().focus();
        }

        function closeForm() {
            $(" .inviteUserForm, .addExpensesForm,.addItemsForm").fadeOut();
        }

       
        $(".userInvite").on("click", () => openForm(".inviteUserForm"));
        $(".expenseaddition").on("click", () => openForm(".addExpensesForm"));
        $(".itemaddition").on("click", () => openForm(".addItemsForm"));
        
        $(".close-button").on("click", closeForm);

        $(document).on("keydown", (e) => {
            if (e.key === "Escape") {
                closeForm();
            }
        });
    });
</script>
<style>
.inviteUserForm, .addExpensesForm, .addItemsForm {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    background-color: black;
    width: 100vw;
    text-align: center;
    font-size: 1.6rem;
    color: white;
    z-index: 10;
    overflow: auto;
}
.logoa{
    font-family: "Platypi", serif;
        color:#6373af;
        margin-bottom:3.2rem;
        font-size:1.6rem;
}

.centering {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.formUser, .expense-form, .item-form {
    background-color: #1E1E1E;
    padding: 3.2rem;
    border-radius: 0.8rem;
    box-shadow: 0 0.4rem 0.8rem rgba(3, 3, 3, 0.3);
    max-width: 40rem;
    width: 100%;
    max-height: 80vh;
    overflow-y: auto;
}

.inputContainer {
    padding-right: 2rem;
}

.formUser label, .expense-form label, .item-form label {
    display: block;
    margin-bottom: 0.8rem;
    color: #fff;
}

.formUser input, .expense-form input, .expense-form select, .item-form input {
    width: 100%;
    padding: 1rem;
    margin-bottom: 2rem;
    border-radius: 0.4rem;
    background-color: #333;
    color: #fff;
    font-family: 'Inter', sans-serif;
}

.formUser .inviteSubmit, .expense-form .groupSubmit, .item-form .addItem {
    width: 100%;
    padding: 1rem;
    border: none;
    border-radius: 0.4rem;
    background-color: #7C90DB;
    color: #fff;
    font-size: 1.6rem;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
}

.formUser .inviteSubmit:hover, .expense-form .groupSubmit:hover, .item-form .addItem:hover {
    background-color: #4a5683;
}

.formUser .close-button, .expense-form .close-button, .item-form .close-button {
    position: fixed;
    top: 2rem;
    right: 2rem;
    z-index: 10;
    padding: 1rem;
    border: none;
    border-radius: 0.4rem;
    background-color: #7C90DB;
    color: #fff;
    font-size: 1.6rem;
    cursor: pointer;
}

.formUser .close-button:hover, .expense-form .close-button:hover, .item-form .close-button:hover {
    background-color: #4a5683;
}

.logo {
    font-family: "Platypi", serif;
    color: #7C90DB;
    margin-bottom: 3.2rem;
    font-size:2.4rem;
}

.userInvite, .expenseaddition, .itemaddition {
    font-size: 1.6rem;
    background-color: #7C90DB;
    color: white;
    border: none;
    border-radius: 0.4rem;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    padding:1rem;
}

.userInvite:hover, .expenseaddition:hover, .itemaddition:hover {
    background-color: #4a5683;
}

.alignment {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.2rem 1.6rem;
    background-color:#1E1E1E;
    margin-bottom:2rem;

}

        /* Table styling */


/* Table styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin: 2rem 0;
    font-family: 'Inter', sans-serif;
    margin-bottom:3.2rem;
}

thead {
    background-color: #f4f4f4;
}

th, td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #ddd;
}
tr:hover{
    background-color: #7C90DB;
}

th {
    background-color: #1E1E1E;
    color: #fff;
    font-size: 1.6rem;
}


/* Action button styling */
table button {
    background-color: #bec8ed;
    color: #fff;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.4rem;
    cursor: pointer;
    font-size: 1.6rem;
    transition: background-color 0.3s ease;
}
.view-details-btn a {
    text-decoration: none; 
}

.view-details-btn a:hover {
    text-decoration: none; 
}

table button:hover {
    background-color: #4a5683;
}

td:nth-child(4) {
    color: #4CAF50; 
}

td:nth-child(4):empty::after {
    content: 'Not Completed';
    color: #d9534f; 
}
/* Container styling */
.back-link {
    padding:3.2rem 3.2rem;
    text-align: center; 
}

/* Link styling */
.back-link a {
    text-decoration: none; 
    color: #7C90DB; 
    font-size: 1.6rem; 
    font-family: 'Inter', sans-serif; 
    padding: 1rem 1rem;
    border: 2px solid #7C90DB;
    border-radius: 0.4rem; 
    background-color: #1E1E1E; 
    transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
}

.back-link a:hover {
    background-color: #7C90DB; 
    color: #fff;
    border-color: #7C90DB; 
}
.containerz{
    padding:0.8rem 3.2rem;
}
.invitespace{
    margin-bottom:5rem;
}
</style>
</head>

<body>
<?php
include_once "./navbar.php";
?>
<div class="containerz">
<div class="alignment invitespace">
    <h1 class="logo">Welcome to <?php echo htmlspecialchars($groupName); ?></h1>
    
    <!-- Invite User Form -->
    <div class="inviteUserForm">
        <div class="centering">
            <form method="post" class="formUser">
                <h1 class="logoa">Invite User</h1>
                <div class="inputContainer">
                    <label for="email">Select User (by email)</label>
                    <input type="email" id="email" name="email" placeholder="Email for the user" required>
                </div>
                <button class="inviteSubmit" type="submit" name="inviteUser">Invite User</button>
                <button type="button" class="close-button">Close</button>
            </form>
        </div>
    </div>
    <button class="userInvite">Invite a User</button>
</div>

<div class="alignment">
    <h1 class="logoa">Expenses</h1>
    
    <!-- Add Expense Form -->
    <div class="addExpensesForm">
        <div class="centering">
            <form method="post" class="expense-form">
                <h2>Add Expense</h2>
                <div class="inputContainer">
                    <label for="userId">Select User to pay</label>
                    <select name="userId" id="userId" required>
                        <?php
                        foreach ($users as $user) {
                            echo "<option value='" . htmlspecialchars($user['userId']) . "'>" . htmlspecialchars($user['username']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="inputContainer">
                    <label for="userIds[]">Select Users who are going to pay</label>
                    <?php foreach ($users as $user): ?>
                        <input type="checkbox" name="userIds[]" id="user_<?= htmlspecialchars($user['userId']) ?>" value="<?= htmlspecialchars($user['userId']) ?>">
                        <label for="user_<?= htmlspecialchars($user['userId']) ?>"><?= htmlspecialchars($user['username']) ?></label><br>
                    <?php endforeach; ?>
                </div>
                <div class="inputContainer">
                    <label for="amount">Amount</label>
                    <input type="number" name="amount" id="amount" step="0.01" required>
                </div>
                <div class="inputContainer">
                    <label for="description">Description</label>
                    <input type="text" name="description" id="description" required>
                </div>
                <button type="submit" name="addExpense" class="groupSubmit">Add Expense</button>
                <button type="button" class="close-button">Close</button>
            </form>
        </div>
    </div>
    <button class="expenseaddition">Add an expense</button>
</div>
<table class="expense-table">
    <thead>
        <tr>
            <th>#</th> <!-- Index column header -->
            <th>Amount</th>
            <th>Payer</th>
            <th>Description</th>
            <th>Amount Due</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $index = 1; 
        foreach ($expenses as $expense): ?>
            <tr>
                <td><?php echo $index++; ?></td>
                <td><?php echo htmlspecialchars($expense['amount']); ?></td>
                <td><?php echo htmlspecialchars($expense['payer']); ?></td>
                <td><?php echo htmlspecialchars($expense['descriptione']); ?></td> <!-- Fixed typo here from 'descriptione' to 'description' -->
                <td><?php echo htmlspecialchars($expense['amountDue']); ?></td>
                <td>
                <?php 
                echo "<button class='view-details-btn'><a href='expense_details.php?groupId=" . urlencode($groupId) .
                    "&groupName=" . urlencode($groupName) .
                    "&expenseId=" . urlencode($expense['expenseId']) .
                    "&payerId=" . urlencode($expense['payer']) . 
                    "'>" .  "view details</a></button>";
                ?>                
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<div class="alignment">
    <h1 class="logoa">Items</h1>
    <div class="addItemsForm">
        <div class="centering">
            <form method="post" class="item-form">
                <h2>Add Item</h2>
                <div class="inputContainer">
                    <label for="itemName">Enter the item needed</label>
                    <input type="text" id="itemName" name="itemName" required>
                </div>
                <button type="submit" name="addItem" class="addItem">Add List Item</button>
                <button type="button" class="close-button">Close</button>
            </form>
        </div>
    </div>
    <button class="itemaddition">Add an item</button>
</div>                
<table>
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
                <div class="back-link">
                        <a href="Home.php">Home page</a>
                    </div>
                        </div>
</body>
</html>
