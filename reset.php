<?php
/* Resets a password through an email (sends it, etc)
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
    require_once "connection.php";
    $stmt=mysqli_stmt_init($link);
    if (mysqli_stmt_prepare($stmt, "SELECT name,password FROM users WHERE email=?")) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        //Set variables
        $to=$_POST['email'];
        //Get password to use as text in hash
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 0) {
            echo 1;
            mysqli_stmt_close($stmt);
            exit;
        }
        mysqli_stmt_bind_result($stmt, $name, $password);
        mysqli_stmt_fetch($stmt);
        //Generate hash and send email
        $reset_hash=generateHash($password, $to);
        $subject="Reset password for Sharehere.net";
        $url=vsprintf("http://localhost/reset.php?email=%s&dd=%s", array($to, $reset_hash));
        $message=vsprintf("Hiya %s!\n\nYou wanted to reset your password. To reset your password follow this link...\n%s\n\nAll the best,\n\nSharehere.net", array($row['name'], $url));
        $headers = 'From: share@sharehere.net'."\r\n"
                .'Content-type: text/plain; charset=utf-8' . "\r\n";
        mail($to,$subject,$message,$headers);
        echo 0;
        mysqli_stmt_free_result($stmt);
        mysqli_stmt_close($close);
        exit;
    }    
} elseif (isset($_GET['email']) && isset($_GET['dd'])) {
    require_once "general.php";
    require_once "connection.php";
    $stmt=mysqli_stmt_init($link);    
    if (mysqli_stmt_prepare($stmt, "SELECT name,password FROM users WHERE email=?")) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        //Set variables
        $email=$_GET['email'];
        $email_link_hash=$_GET['dd'];
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 0) {
            echo 1;
            exit;
        }
        mysqli_stmt_bind_result($stmt, $name, $password);
        $hash=generateHash($password, $email);
        mysqli_stmt_free_result($stmt);
        mysqli_stmt_close($stmt);
        if ( $hash == $email_link_hash ) {
            //Submit query
            $stmt=mysqli_stmt_init($link);
            if (mysqli_stmt_prepare($stmt, "UPDATE users SET password=? WHERE email=?")) {
                mysqli_stmt_bind_param($stmt, "ss", $new_password_hash, $email)
                
                //Generate a new random password and hash it
                $new_password=substr(md5(uniqid(mt_rand(), true)), 0, 15);
                $new_password_hash = generateHash($new_password);
                
                mysqli_stmt_execute($stmt);
                //Check for successful query and mail password, or not, and return
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $subject="New password for Sharehere";
                    $message=vsprintf("Hiya %s!\n\nThis is your new password for Sharehere: %s\n\nYou can change it at your homepage.\n\nAll the best,\n\nSharehere.net", array($row['name'], $new_password));
                    $headers = 'From: share@sharehere.net'."\r\n"
                        .'Content-type: text/plain; charset=utf-8' . "\r\n";
                    mail($email,$subject,$message,$headers);
                    echo 0;
                    mysqli_stmt_close($stmt);
                    exit;
                }
        } else {
            echo 1;
            }
} else {
    echo 2;
}
?>
