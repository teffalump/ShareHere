<?php
define("SERVER_KEY", 'testtesttest'); //Our key for secure cookie scheme
define("FIELD_SEP",":::");  //Cookie field separator
define("EMAIL_SALT",'testtesttesttest'); //email key
define("EXPIR", (3*60*60)); //Expiration time for cookie
function generateHash($plainText, $salt = null, $salt_length=10)
{
    if ($salt === null)
    {
        $salt = substr(md5(uniqid(mt_rand(), true)), 0, $salt_length);
    }
    else
    {
        $salt = substr($salt, 0, $salt_length);
    }
    return $salt . hash("sha512", $salt . $plainText);
}
?>
