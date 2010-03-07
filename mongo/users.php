<?php
    /* 
    Retrieves users from database when person is, for instance, hovering over network
        Required fields:
            $_POST['location']
            $_POST['distance']
        Returns:
            1 - required field unset
            json($users) - json encoded $users 
    */

if (isset($_POST['distance']) && isset($_POST['location']))
{
    require_once "general.php";
    require_once "connection.php";

    $location=$_POST['location'];
    $distance=$_POST['distance'];
    
    
    while(mysqli_stmt_fetch($stmt))
        {
        $users[] = $name; //Is this the best way...? I have no idea. =-/
        }
    echo json_encode($users);
    mysqli_stmt_close($stmt);
    exit;
} 
else 
{
    echo 1;
    exit;
}    
?>
