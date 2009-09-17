<?php
/* Retrieves users from database when person is, for instance, hovering over network
        Required fields:
            $_POST['network_id']
        Returns:
            1 - required field unset
            json($users) - json encoded $users */
if (isset($_POST['network_id'])) //network_id or w/e identifying mark
{
    require_once "general.php";
    $network_id=$_POST['network_id'];
    $query=sprintf("SELECT name FROM users WHERE network_id='%s'", msyql_real_escape_string($network_id)); //add more fields if one wants and also modify to include child networks, but we need to decide what schema to use first
    $result=mysql_query($query);
    while( $row = mysql_fetch_array($result) )
    {
        $users[] = $row[0]; //Is this the best way...? I have no idea. =-/
    }
    echo json_encode($users);
    mysql_free_result($result);
    exit;
} 
else 
{
    echo 1;
    exit;
}    
?>
