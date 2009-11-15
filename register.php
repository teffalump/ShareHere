<?php
/* Stores a new user's details, sends a confirmation email, and confirms the link in the email.
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
    require_once "connection.php";
    require_once "variables.php";
    
    $status = 1;        //our return value
    
    //Make query
    $stmt=mysqli_stmt_init($link);
    if (mysqli_stmt_prepare($stmt,"INSERT INTO users (name, password, email, date_joined) VALUES (?,?,?,Now())")) 
    {
        mysqli_stmt_bind_param($stmt, "sss", $username, $password_hash, $email);
        
        $password_hash = implode(generateHash($_POST['password']));
        $username=$_POST['username'];
        $email=$_POST['email'];
        
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_affected_rows($stmt) > 0) 
        {
            $subject="Email confirmation for Sharehere.net";

            //Returns the salt + hash, we only want hash so...
            $tmp=generateHash($email, EMAIL_SALT);
            $email_link_hash = $tmp["hash"]; 

            $url=vsprintf(REGISTER_LINK . "?email=%s&cc=%s", array($email, $email_link_hash));
            $message = vsprintf("Dear %s,\n\nPlease confirm with this link...\n%s\n\nAll the best,\n\nSharehere.net", array($username, $url));
            $headers = 'From: share@sharehere.net'."\r\n"
                    .'Content-type: text/plain; charset=utf-8' . "\r\n";
            mail($to, $subject, $message, $headers);
            
            $status = 0;
        }
    }
    
    mysqli_stmt_close($stmt);
    echo $status;
    exit;

} elseif (isset($_GET['email']) && isset($_GET['cc'])) {
    require_once "general.php";
    require_once "connection.php";
    require_once "variables.php";
    
    $status=1;          //our return value

    $stmt=mysqli_stmt_init($link);              

    if (mysqli_stmt_prepare($stmt, "UPDATE users SET authenticated=1 WHERE email=?")) 
    {

        mysqli_stmt_bind_param($stmt, "s", $email);
        
        $email=$_GET['email'];
        $email_link_hash=$_GET['cc'];
        
        $tmp=generateHash($email, EMAIL_SALT);
        $email_link_hash = $tmp["hash"];
        
        if ( $email_hash == $email_link_hash ) 
        {
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) > 0) 
            {
                $status=0;
            }
        }
    }
    mysqli_stmt_close($stmt);
    echo $status;
    exit;
} 
else 
{
    echo 2;
    exit;
} 
?>
