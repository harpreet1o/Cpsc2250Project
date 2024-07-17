<?php
include_once './config.php';

function validate() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        include_once './validationForEmail.php';
        $email_valid = for_email();
        $password = $_POST['password'];
        $email_valid = for_email();
        if (!$email_valid) {
            $val_messages['email'] = 'Invalid email address';
            header("Location: ../signup.php?error=email");
            die();
        }
        try {
                include_once './database.inc.php';
                $query = "SELECT * FROM user WHERE email = :email";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':email', $email_valid);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch single result

                $pdo = null;
                $stmt = null;
                // var_dump($result); useful in checking the result
                // Check if a user was found and the password matches
                if ( $result['email'] == $email_valid && $result['user_password'] == $password) {
                    $_SESSION['username']=$result['username'];
                    $_SESSION['userId']=$result['userId'];
                    header("Location: ../home.php");
                    exit();
                } else {
                    header("Location: ../login.php?error=invalid_credentials");
                    exit();
                }
            } catch (PDOException $e) {
                // Log the error message
                error_log("Database query failed: " . $e->getMessage());
                header("Location: ../login.php?error=db");
                die("Query failed: " . $e->getMessage());
            }
        }
         else {
        header("Location: ../login.php?error=invalid_request");
        exit();
    }
}
validate();
