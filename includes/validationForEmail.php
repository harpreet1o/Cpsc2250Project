<?php
function for_email() {
    global $val_messages;
    $email_pattern = '#^(.+)@([^\.].*)\.([a-z]{2,})$#';
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (preg_match($email_pattern, $email) === 1) {
        $val_messages['email'] = "";
        return $email;
    } else {
        $val_messages['email'] = "Please enter a valid email.";
        return false ;
    }
}