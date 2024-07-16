<?php
// Global result of form validation

// Global array of validation messages. For valid fields, message is ""
$val_messages = array(
    'email' => '',
    'username' => '',
    'password' => ''  
);


// Check each field to make sure submitted data is valid. If no boxes are checked, isset() will return false
function validate() {
    global $valid;
    global $val_messages;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email_valid = for_email();
        $username=$_POST['username'];
        $password=$_POST['password'];

        if ($email_valid) {
            include_once './database.inc.php';

        }
        else 
        $valid= false;
    }
}
function for_email() {
    global $val_messages;
    $email_pattern = '#^(.+)@([^\.].*)\.([a-z]{2,})$#';
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (preg_match($email_pattern, $email) === 1) {
        $val_messages['email'] = "";
        return true;
    } else {
        $val_messages['email'] = "Please enter a valid email.";
        return false;
    }
}
validate();