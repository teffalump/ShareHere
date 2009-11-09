<?php
define ("SERVER", "localhost");
define ("S_USERNAME", "root");
define ("S_PASSWORD", "yummy");
define ("DATABASE", "goodies");

//Connect to server and open database object
$link = @mysqli_connect(SERVER, S_USERNAME, S_PASSWORD, DATABASE);
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
?>
