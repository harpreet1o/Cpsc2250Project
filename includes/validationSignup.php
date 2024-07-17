<?php

// Global result of form validation
$val_messages = array(
    'email' => '',
    'username' => '',
    'password' => ''  
);

// Check each field to make sure submitted data is valid
function validate() {
    global $val_messages;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        include_once './validationForEmail.php';

        // Validate email
        $email_valid = for_email();
        if (!$email_valid) {
            $val_messages['email'] = 'Invalid email address';
            header("Location: ../signup.php?error=email");
            die();
        }

        // Validate username
        if (empty($_POST['username'])) {
            $val_messages['username'] = 'Username is required';
            header("Location: ../signup.php?error=username");
            die();
        }
        $username = $_POST['username'];

        // Validate password
        if (empty($_POST['password'])) {
            $val_messages['password'] = 'Password is required';
            header("Location: ../signup.php?error=password");
            die();
        }
        $password = $_POST['password'];

        // Insert into database if all validations pass
        try {
            include_once './database.inc.php';

            $query = "INSERT INTO user (email, username, user_password) VALUES (:email, :username, :user_password)";
            $stmt = $pdo->prepare($query);  
            $stmt->bindParam(':email', $email_valid);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':user_password', $password);

            $stmt->execute();

            // Clean up and redirect
            $pdo = null;
            $stmt = null;
            header("Location: ../login.php");
            die();
        } catch (PDOException $e) {
            // Log the error message
            error_log("Database query failed: " . $e->getMessage());
            header("Location: ../signup.php?error=db");
            die("Query failed: " . $e->getMessage());
        }
    } else {
        // If the request is not a POST request
        header("Location: ../signup.php?error=invalid_request");
        die();
    }
}

validate();
?>
