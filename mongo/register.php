<?php
/* Stores a new user's details, sends an email, and confirms the link in the email.
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
if (isset($_POST['password']) && isset($_POST['username']) && isset($_POST['email'])) 
{
    require_once "general.php";
    require_once "connection.php";
    require_once "variables.php";
    
    $password_hash = generateHash($_POST['password']);
    $username=$_POST['username'];
    $email=$_POST['email'];
    
    //Make query
    $info = array("name"=>$username, "password"=>$password_hash, "email"=>$email, "confirmed" => false, "url_id" => '', "loc" => array( "lat" => '', "long" => ''));
    $db->USERS->save($info);
    if (is_null(getvalue($db->lastError(), "err")))
        {
        $subject="Email confirmation for Sharehere.net";
        
        $secret= $email . date("Ymd");
        $email_link_hash=generateHash($secret, EMAIL_SALT, EMAIL_LINK_HASH_LENGTH);
        
        $url=vsprintf("http://localhost/register.php?email=%s&cc=%s", array(urlencode($email), $email_link_hash));
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
    require_once "connection.php";
    require_once "variables.php";

    $email=urldecode($_GET['email']);
    $email_link_hash=$_GET['cc'];

    $cat = $email . date("Ymd");
    $email_hash=generateHash($cat, EMAIL_SALT, EMAIL_LINK_HASH_LENGTH);
    
    if ( $email_hash == $email_link_hash ) 
        {
        
        $filter = array( "email" => $email );
        $fields = array( '$set' => array( "confirmed" => true ) );
        $db->USERS->update($filter, $fields);

        if (getvalue($db->lastError(), "updateExisting")) 
            {
            echo 0;
            exit;
            }
        else
            {
            echo 1;
            exit;
            }
        }
    else 
        {
        echo 1;
        exit;
        }
} else {
    echo 2;
    exit;
} 
?>
