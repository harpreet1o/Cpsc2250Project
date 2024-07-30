<?php
include_once "./includes/database.inc.php";

// Retrieve data from the form
$expenseId = $_POST['expenseId'];
$groupId = $_GET['groupId'];
$groupName = $_GET['groupName'];
$payerId = $_GET['payerId'];
$paidUserIds = isset($_POST['paidUsers']) ? $_POST['paidUsers'] : [];

// Prepare the placeholders for userId in the IN clause
$placeholders = implode(',', array_fill(0, count($paidUserIds), '?'));

// Set all amounts owed to 0 for users who have paid
$sql = "UPDATE expense_shares
        SET amountOwed = 0
        WHERE expenseId = ? AND userId IN ($placeholders)";
$stmt = $pdo->prepare($sql);

// Execute the query with expenseId followed by paidUserIds
$params = array_merge([$expenseId], $paidUserIds);
$stmt->execute($params);

// Redirect back to the expense details page
header("Location: expense_details.php?expenseId=$expenseId&groupId=$groupId&groupName=$groupName&payerId=$payerId");
exit();
