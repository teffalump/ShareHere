<?php
$con = mysql_connect(SERVER, S_USERNAME, S_PASSWORD) or die('Could not connect: ' . mysql_error());
mysql_select_db(DATABASE, $con);

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

