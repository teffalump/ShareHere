<?php
/* Retrieves users from database when person is, for instance, hovering over network
        Required fields:
            $_POST['network_id']
        Returns:
            1 - required field unset
            join(":",$users) - list of users (colon-seperated) */
if (isset($_POST['network_id'])) //network_id or w/e identifying mark
{
    require_once "general.php";
    $network_id=$_POST['network_id'];
    $query=sprintf("SELECT name FROM users WHERE network_id='%s'", msyql_real_escape_string($network_id)); //add more fields if one wants
    $result=mysql_query($query);
    while( $row = mysql_fetch_array($result) )
    {
        $users[] = $row[0]; //Is this the best way...? I have no idea. =-/
    }
    echo join(":",$users);
    mysql_free_result($result);
} 
else 
{
    echo 1;
}    
?>
