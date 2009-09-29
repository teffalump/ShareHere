<?php
class CookieManager
    /* This class implements a limited but effective secure cookie manager.
       
       I mostly took the code from BigOrNot_CookieManager written by Matthieu Huguet.
       If there are any problems with the similar code, talk to me.
    */
{
    //Server key
    protected   $_secret = '';

    //SSL support
    protected   $_ssl = True;

    public function         __construct($secret, $enable_ssl = True)
    {
        if (!$secret)
        {
            throw new Exception('You must provide a secret key');
        }
        if (!$enable_ssl)
        {
            $this->_ssl=False;
        }
    }
    public function         SSL_enabled()
    {
        return ($this->_ssl);
    }
    public function         set_SSL($status)
    {
        $this->$_ssl = $status;
        return ($this->_ssl);
    }
    public function         setCookie($cookiename, $data, $expiration = 0, $path = '', $domain = '', $secure = True, $httponly = null)
    {
        $secureCookieValue = $this->_secureCookieValue($username, $expiration, $data);
        setcookie($cookiename, $secureCookieValue, $expiration, $path, $domain, $secure, $httponly);
    }
    public function         deleteCookie($cookiename, $path='/', $domain='', $secure = True, $httponly = null)
    {
        setcookie($cookiename, '', $expire=1234567, $path, $domain, $secure, $httponly);
    }
    public function         cookieExists($cookiename)
    {
        return (isset($_COOKIE[$cookiename]));
    }
    public function         cookieData($cookiename)
    {
        if ($this->validCookie($cookiename))
        {
            $cookieFields = explode(':::', $_COOKIE[$cookiename]);
            return ($cookieFields[2]);
        }
        else
            return False;
    }
    public function          validCookie($cookiename)
    {
        //Checks the validity of a cookie. True if valid. False if not. 
        
        $cookieFields = explode(':::', $_COOKIE[$cookiename]);
        //Three checks to confirm validity
        //First: correct number of fields
        if (count($cookieFields) != 4)
        {
            return False;
        }
        //Second: expires after current time
        if (time() >= $cookieFields[1] )
        {
             return False;
        }
        //Third: our computed HMAC(...) matches cookie's HMAC(...)
        $test=array_slice(explode(':::', $this->_secureCookieValue($cookieFields[0], $cookieFields[1], $cookieFields[2])), -1);
        if ( $test != $cookieFields[3] )
        {
            return False;
        }
        //Passed checks, therefore is valid
        return True;
    }
    protected function         _secureCookieValue($username, $expiration = 0, $data='')
    {
        //Returns a secure cookie value
        //Structure of the cookie: username|expiration time|data|HMAC(username|expiration time|data|session_key, k)
        //k=HMAC(username|expiration time, sk)
        //sk = server key
        //If SSL isn't enabled, then bind the cookie to their IP address - a reasonable substitute, I think.
        $key=hash_hmac("sha512", $username.$expiration, $this->_secret);
        
        if ($this->_ssl && isset($_SERVER['SSL_SESSION_ID']))
        {
            $digest = hash_hmac("sha512", $username . $expiration . $data . $_SERVER['SSL_SESSION_ID'], $key);
        }
        else
            $digest = hash_hmac("sha512", $username . $expiration . $data . $_SERVER['REMOTE_ADDR'], $key);
        
        $fields = array($username, $expiration, $data, $digest);
        return (implode(':::', $fields));
    }
}
?> 
