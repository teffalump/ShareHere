<?php
    /* 
    Retrieves users from database when person is, for instance, hovering over network
        Required fields:
            $_POST['network_id']
        Returns:
            1 - required field unset
            json($users) - json encoded $users 
    */

if (isset($_POST['network_id'])) //network_id or w/e identifying mark
{
    require_once "general.php";
    require_once "connection.php";
    $stmt=mysqli_stmt_init($link)
    if (mysqli_stmt_prepare($stmt, "SELECT name FROM users WHERE network_id=?")) {
        mysqli_stmt_bind_param($stmt,"i",$network_id);    
        $network_id=$_POST['network_id'];
        mysqli_stmt_execute($stmt); //add more fields if one wants and also modify to include child networks, but we need to decide what schema to use first
        mysqli_stmt_bind_result($stmt, $name);
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
