<?php
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
    return array("salt" => $salt, "hash" => hash("sha512", $salt . $plainText));
}

function cleanReturn($stmt, $link, $status)
{
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    echo $status;
}
?>
