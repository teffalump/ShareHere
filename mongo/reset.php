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
if (isset($_POST['email'])) 
{
    require_once "general.php";
    require_once "connection.php";
    require_once "variables.php";

    $email = $_POST['email'];

    $filter = array( "email" => $email );
    $fields = array( "name" => true);
    $info = $db->USERS->findOne($filter, $fields);
    $db->close();
        
    if (is_null($info))
    {
        echo 1;
        exit;
    }
      
    //Generate hash and send email
    $secret = $info["_id"] . date("Ymd");
    $reset_hash=generateHash($secret, EMAIL_SALT, EMAIL_LINK_HASH_LENGTH);

    $subject="Reset password for Sharehere.net";
    $url=vsprintf("http://localhost/~chris/reset.php?email=%s&dd=%s", array(urlencode($email), $reset_hash));
    $message=vsprintf("Hiya %s!\n\nYou wanted to reset your password. To reset your password follow this link...\n%s\n\nAll the best,\n\nSharehere.net", array($info["name"], $url));
    $headers = 'From: share@sharehere.net'."\r\n"
                .'Content-type: text/plain; charset=utf-8' . "\r\n";
    mail($to,$subject,$message,$headers);
        
    echo 0;
    exit;
        
} 
elseif (isset($_GET['email']) && isset($_GET['dd'])) 
{
    require_once "general.php";
    require_once "connection.php";
    require_once "variables.php";

    $email = $_GET['email'];
    $filter = array ("email" => $email );
    $fields = array ("_id" => 1 );
    
    $id = getvalue($db->findOne($filter, $fields), "_id");

    if (isset($id))
        {
        $email_link_hash=$_GET['dd'];
        
        $secret = $id . date("Ymd");

        $hash=generateHash($secret, EMAIL_SALT, EMAIL_LINK_HASH_LENGTH)
        
        if ( $hash === $email_link_hash ) 
            {
            
            $new_password=substr(md5(uniqid(mt_rand(), true)), 0, 15);
            $new_hash = generateHash($new_password, USER_SALT);
            
            $fields = array ('$set' => array( "password" => $new_hash ) );
             
            $db->USERS->update($filter, $fields);
            if ( getvalue($db->lastError(), "updateExisting") )
                {
                //$subject="New password for Sharehere";
                //$message=vsprintf("Hiya!\n\nThis is your new password for Sharehere: %s\n\nYou can change it at your homepage.\n\nAll the best,\n\nSharehere.net\n\nPS: Upon logging in, you'll be required to change the password.", array($new_password));
                //$headers = 'From: share@sharehere.net'."\r\n"
                //    .'Content-type: text/plain; charset=utf-8' . "\r\n";
                //mail($email,$subject,$message,$headers);
                echo 0;
                unset($new_password);
                exit;
                }
            }
        }
            
    echo 1;
    exit;
} 
else 
{
    echo 2;
    exit;
}
?>
