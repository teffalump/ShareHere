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
    //Set variable
    $email=$_POST['email'];
    //Get password hash
    $query=sprintf("SELECT password FROM users WHERE email='%s'", mysql_real_escape_string($email));
    $result=mysql_query($query);
    //Check for invalid or unsuccessful query
    if (mysql_num_rows($result) == 0) {
        echo 2;
        exit;
    }
    //Free result, hash new password with same salt and check equality
    $row=mysql_fetch_assoc($result);
    mysql_free_result($result);
    $password_hash = generateHash($_POST['password'], $row["password"]);
    if ($password_hash == $row["password"]) {
        echo 0;
        exit;
    }
    else {
        echo 1;
        exit;
    }
} else { 
    echo 3;
    exit;
}
?>
