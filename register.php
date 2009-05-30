<?php
/* Store a new user's details, sends an email, and confirms the link in the email.
        Required fields:
            Register:
                $_POST['password']
                $_POST['username']
                $_POST['email']
            Confirm registration:
                $_GET['email']
                $_GET['cc]
        Return values
            0 - stored succesffully and emailed or successfully confirmed email link
            1 - unsuccessful
            2 - required fields were unset
*/
if (isset($_POST['password']) && isset($_POST['username']) && isset($_POST['email'])) {
    require_once "general.php";
    //Set variables
    $password_hash = generateHash($_POST['password']);
    $username=$_POST['username'];
    $email=$_POST['email'];
    //Make query
    $query=vsprintf("INSERT INTO users (name, password, email, date_joined) VALUES ('%s', '%s', '%s', Now())", array(mysql_real_escape_string($username), mysql_real_escape_string($password_hash), mysql_real_escape_string($email)));
    $result=mysql_query($query);
    //Check for successful query
    if (mysql_affected_rows($con) > 0) {
        $to=$email;
        $subject="Email confirmation for Sharehere.net";
        $email_link_hash=substr(generateHash($email, EMAIL_SALT, 125), 125);
        $url=vsprintf("http://localhost/register.php?email=%s&cc=%s", array($email, $email_link_hash));
        $message = vsprintf("Dear %s,\n\nPlease confirm with this link...\n%s\n\nAll the best,\n\nSharehere.net", array($username, $url));
        $headers = 'From: share@sharehere.net'."\r\n"
                .'Content-type: text/plain; charset=utf-8' . "\r\n";
        mail($to, $subject, $message, $headers);
        echo 0;
        exit;
    }
    else {
        echo 1;
        exit;
    }
} elseif (isset($_GET['email']) && isset($_GET['cc'])) {
    require_once "general.php";
    //Set variables
    $email=$_GET['email'];
    $email_link_hash=$_GET['cc'];
    $email_hash=substr(generateHash($email, EMAIL_SALT, 125), 125);
    if ( $email_hash == $email_link_hash ) {
        $query=vsprintf("UPDATE users SET authenticated=0 WHERE email='%s'", array(mysql_real_escape_string($email)));
        $result=mysql_query($query);
        if (mysql_affected_rows($con) > 0) {
            echo 0;
            exit;
            }
        }
    else {
        echo 1;
        exit;
        }
} else {
    echo 2;
    exit;
} 
?>
