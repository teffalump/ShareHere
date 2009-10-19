<?php
    /* Validates email or password
        Returns:
            0       - Good password or email
            1       - Bad password or email
            2       - Argument not set
    */

if (isset($_GET['email'])) {
    /* Should we even validate emails? Sort of pointless, but here is an ok one */
    reg="/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i";
    if (preg_match($reg, $_GET['email'])) {
        echo 0;
    } else {
        echo 1;
    }
} elseif (isset($_GET['password'])) {
    /* Valid password - two checks
            -strlen(password) > 6 
            -password not in banned_passwords
    */

    $banned_passwords = file_get_contents("/banned_passwords");  //Change to whatever the list of banned passwords is     
    if (!strpos($banned_passwords, $_GET['password']) && strlen($_GET['password'] > 6))
    {
        echo 0;
    }
    else
    {
        echo 1;
    }
} else {
    echo 2;
    }
?>
