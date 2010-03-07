<?php
/* Authenticates a user
    Required fields
        $_POST['password']
        $_POST['email']
    Return values
        0 - password matches
        1 - password doesn't match
        2 - email not found
        3 - required fields were unset
*/
if (isset($_POST['password']) && isset($_POST['email'])) {
    require_once "general.php";
    require_once "connection.php";

    $email=$_POST['email'];
    
    //get password given certain email
    $query = array("email" => $email);
    $fields = array("password");
    $obj = $db->USERS->findOne($query, $fields);
    $db->close();
    
    //Check for invalid or unsuccessful query
    if (is_null($obj)) 
        {
        echo 2;
        exit;
        }
    else
        {
        $password = $obj["password"];
        }

    $test_hash = generateHash($_POST['password']);
    if ($test_hash == $password) 
        {
        echo 0;
        exit;
        }
    else 
        {
        echo 1;
        exit;
        }
} else { 
    echo 3;
    exit;
}
?>
