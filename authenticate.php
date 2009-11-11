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
    $stmt = mysqli_stmt_init($link);
    if (mysqli_stmt_prepare($stmt, "SELECT password FROM users WHERE email=?")) {

        /* bind parameters for markers */
        mysqli_stmt_bind_param($stmt,"s", $email);
         
        //Set email to one passed to script 
        $email=$_POST['email'];
        
        /* execute query */
        mysqli_stmt_execute($stmt);
 
        //Store result
        mysqli_stmt_store_result($stmt);
        
        //Check for invalid or unsuccessful query
        if (mysqli_stmt_num_rows($stmt)== 0) {
            echo 2;
            exit;
        }
        /* bind result variables */
        mysqli_stmt_bind_result($stmt, $password);

        /* fetch value */
        mysqli_stmt_fetch($stmt);
        //Check password against db password hash
        $password_hash = generateHash($_POST['password'], $password);
        if ($password_hash == $password) {
            echo 0;
        }
        else {
            echo 1;
        }

        //Free result
        mysqli_stmt_free_result($stmt);

        /* close statement */
        mysqli_stmt_close($stmt);

        exit;
    } 
} else { 
    echo 3;
    exit;
}
?>
