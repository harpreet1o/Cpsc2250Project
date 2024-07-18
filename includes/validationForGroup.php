<?php

function validate() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        session_start();
        
        // Ensure the correct path to the database connection file
        include_once "database.inc.php";

    

        $groupName = $_POST['groupName'];
        
        if (!empty($groupName)) {
            try {
                // Insert group name into the 'groups' table
                $query1 = "INSERT INTO groups (groupName) VALUES (:groupName)";
                $stmt = $pdo->prepare($query1);
                $stmt->bindParam(':groupName', $groupName);
                $stmt->execute();

                // Get the ID of the newly inserted group
                $group_id = $pdo->lastInsertId();

                // Ensure the session user is set
                if (!isset($_SESSION['user'])) {
                    throw new Exception('User ID not set in session');
                }

                // Insert into the user_group table
                $query2 = "INSERT INTO user_group (user_id, group_id) VALUES (:userId, :groupId)";
                $stmt2 = $pdo->prepare($query2);
                $stmt2->bindParam(':userId', $_SESSION['user']);
                $stmt2->bindParam(':groupId', $group_id);
                $stmt2->execute();

                echo 'New group_id: ' . $group_id;
            } catch (PDOException $e) {
                echo "Database error: " . $e->getMessage();
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            echo "Group name cannot be empty";
        }
    }
}
