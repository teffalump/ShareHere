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
    public function         setCookie($cookiename, $username, $data, $expiration = 0, $path = '/', $domain = '', $secure = False, $httponly = null)
    {
        $secureCookieValue = $this->_secureCookieValue($username, $expiration, $data);
        setcookie($cookiename, $secureCookieValue, $expiration, $path, $domain, $secure, $httponly);
    }
    public function         deleteCookie($cookiename, $path='/', $domain='', $secure = False, $httponly = null)
    {
        setcookie($cookiename, '', $expire=1234567, $path, $domain, $secure, $httponly);
        unset($_COOKIE[$cookiename]);
    }
    public function         cookieExists($cookiename)
    {
        return (isset($_COOKIE[$cookiename]));
    }
    public function         cookieData($cookiename)
    {
        if ($this->validCookie($cookiename))
        {
            $cookieFields = explode('|', base64_decode($_COOKIE[$cookiename]));
            return ($cookieFields[2]);
        }
        else
            return False;
    }
    public function          validCookie($cookiename)
    {
        //Checks the validity of a cookie. True if valid. False if not. 
       
        $cookieFields = explode('|', base64_decode($_COOKIE[$cookiename]));
        //Three checks to confirm validity
        //First: correct number of fields
        if (count($cookieFields) !== 4)
        {   
            return False;
        }
        //Second: expires after current time
        if (time() >= $cookieFields[1] && !($cookieFields[1]==0))
        {
            return False;
        }
        //Third: our computed HMAC(...) matches cookie's HMAC(...)
        $test=explode('|', base64_decode($this->_secureCookieValue($cookieFields[0], $cookieFields[1], $cookieFields[2])));
        if ( $test[3] != $cookieFields[3] )
        {
            return False;
        }
        //Passed checks, therefore is valid
        return True;
    }
    protected function         _secureCookieValue($username, $expiration = 0, $data='')
    {
        //Returns a secure cookie value - base64
        //Structure of the cookie: username|expiration time|data|HMAC(username|expiration time|data|session_key, k)
        //k=HMAC(username|expiration time, sk)
        //sk = server key
        //If SSL isn't enabled, then bind the cookie to their IP address - a reasonable substitute, I think.
        //Binding to IP address doesn't have the security the SSL certificate does, but oh well.
        $key=hash_hmac("sha512", $username.$expiration, $this->_secret);
        
        if ($this->_ssl && isset($_SERVER['SSL_SESSION_ID']))
        {
            $digest = hash_hmac("sha512", $username . $expiration . $data . $_SERVER['SSL_SESSION_ID'], $key);
        }
        else
            $digest = hash_hmac("sha512", $username . $expiration . $data . $_SERVER['REMOTE_ADDR'], $key);
        
        $fields = array($username, $expiration, $data, $digest);
        return (base64_encode(implode('|', $fields)));
    }
}
?>
