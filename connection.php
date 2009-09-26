<?php
//Connect to server and open database object
$link = @mysqli_connect(SERVER, S_USERNAME, S_PASSWORD, DATABASE);
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
?>
