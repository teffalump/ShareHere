<?php
/* Reset a password through an email 
        Required fields
            Send email:
                $_POST['email']
            Reset:
                $_GET['email']
                $_GET['dd']
        Return values
            0 - mailed successfully
            1 - unsuccessful or invalid
            2 - required fields not set
*/
if (isset($_POST['email'])) {
    require_once "general.php";
    //Set variables
    $to=$_POST['email'];
    //Get password to use as text in hash
    $query=sprintf("SELECT name,password FROM users WHERE email='%s'", mysql_real_escape_string($to));
    $result=mysql_query($query);
    if (mysql_num_rows($result) == 0) {
        echo 1;
        exit;
    }
    $row=mysql_fetch_assoc($result);
    mysql_free_result($result);
    //Generate hash and send email
    $reset_hash=generateHash($row['password'], $to);
    $subject="Reset password for Sharehere.net";
    $url=vsprintf("http://localhost/reset.php?email=%s&dd=%s", array($to, $reset_hash));
    $message=vsprintf("Hiya %s!\n\nYou wanted to reset your password. To reset your password follow this link...\n%s\n\nAll the best,\n\nSharehere.net", array($row['name'], $url));
    $headers = 'From: share@sharehere.net'."\r\n"
            .'Content-type: text/plain; charset=utf-8' . "\r\n";
    mail($to,$subject,$message,$headers);
    echo 0;
    exit;
    
} elseif (isset($_GET['email']) && isset($_GET['dd'])) {
    require_once "general.php";
    //Set variables
    $email=$_GET['email'];
    $email_link_hash=$_GET['dd'];
    $query=vsprintf("SELECT name,password FROM users WHERE email='%s'", array(mysql_real_escape_string($email)));
    $result=mysql_query($query);
    if (mysql_num_rows($result) == 0) {
        echo 1;
        exit;
    }
    $row=mysql_fetch_assoc($result);
    mysql_free_result($result);
    $hash=generateHash($row['password'], $email);
    if ( $hash == $email_link_hash ) {
        //Generate a new random password and hash it
        $new_password=substr(md5(uniqid(rand(), true)), 0, 15);
        $new_password_hash = generateHash($new_password);
        //Submit query
        $query=vsprintf("UPDATE users SET password='%s' WHERE email='%s'", array(mysql_real_escape_string($new_password_hash), mysql_real_escape_string($email)));
        $result=mysql_query($query);
        //Check for successful query and mail password, or not, and return
        if (mysql_affected_rows($con) > 0) {
            $subject="New password for Sharehere";
            $message=vsprintf("Hiya %s!\n\nThis is your new password for Sharehere: %s\n\nYou can change it at your homepage.\n\nAll the best,\n\nSharehere.net", array($row['name'], $new_password));
            $headers = 'From: share@sharehere.net'."\r\n"
                .'Content-type: text/plain; charset=utf-8' . "\r\n";
            mail($email,$subject,$message,$headers);
            echo 0;
            exit;
        }
    } else {
        echo 1;
        }
} else {
    echo 2;
}
?>

