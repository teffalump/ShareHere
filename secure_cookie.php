<?php
function secureCookie($username, $session_key, $data='')
{
    //Returns a secure cookie -- scheme follows. $session_key needs to be SSL session key or something similar
    
    //Structure of the cookie: username|expiration time|data|HMAC(username|expiration time|data|session_key, k)
    //k=HMAC(username|expiration time, sk)
    //sk = server key
    require_once "general.php"; //Needs SERVER_KEY and FIELD_SEP defined
    $expiration=time() + (3 * 60 * 60) //Change to whatever you want the expiration time to be
 
    $k=hash_hmac("sha512", $username.FIELD_SEP.$expiration, SERVER_KEY);
    $end=hash_hmac("sha512", $username.FIELD_SEP.$expiration.FIELD_SEP.$data.FIELD_SEP.$session_key, $k);
    $beg=$username.FIELD_SEP.$expiration.FIELD_SEP.$data;
    return $beg.FIELD_SEP.$end;
}
function validCookie($cookie, $session_key)
{
    //Checks the validity of a cookie. True if valid. False if not. $session_key needs to be SSL session key or something similar
    
    //Structure of the cookie: username|expiration time|data|HMAC(username|expiration time|data|session_key, k)
    //k=HMAC(username|expiration time, sk)
    //sk = server key
    require_once "general.php"; //Requires SERVER_KEY and FIELD_SEP defined
    $fields = explode(FIELD_SEP, $cookie);

    //Two checks to confirm validity 
    //First: expiration and current time
    if ( $fields[1] >= time() ) 
    {
         return False;
    }
    //Second: compute our own HMAC(...) and compare with cookie's HMAC(...)
    $k = hash_hmac("sha512", $fields[0].FIELD_SEP.$fields[1], SERVER_KEY);
    $test=hash_hmac("sha512", implode(FIELD_SEP, array_slice($fields, 0,3)).FIELD_SEP.$session_key, $k);
    if ( $test != $fields[3] )
    {
        return False;
    }
    //Passed checks, therefore is valid
    return True;
}
?> 
