<?php
//This is directly taken from the php website, I checked it against the RFC and couldn't find anything wrong with its implementation. However, as always, never do your own crypto, so I'm wary still. Hehe.
/**
 * Implementation of the PBKDF2 key derivation function as described in RFC 2898.
 *
 * PBKDF2 was published as part of PKCS #5 v2.0 by RSA Security. The standard is
 * also documented in IETF RFC 2898.
 *
 * The first four function arguments are as the standard describes:
 *
 *     PBKDF2(P, S, c, dkLen)
 *
 * The fifth function argument specifies the hash function to be used. This should
 * be provided in the same format as used for the hash() function. The default
 * hash algorithm is SHA-1, but this is not recommended for new applications.
 *
 * The function returns false if dk_len is too large. Otherwise it returns the
 * derived key as a binary (correction: hexadecimal) string. --- I like hex characters more, eh? =-) ---
 *
 * @author Henry Merriam <php@henrymerriam.com>
 *
 * @param    string    p        password
 * @param    string    s        salt
 * @param    int        c        iteration count
 * @param    int        dk_len    derived key length (octets)
 * @param    string    algo    hash algorithm
 *
 * @return    string            derived key
 */
function generateHash($p, $s, $dk_len = 512, $c = 2000, $algo = 'sha512') {

    // experimentally determine h_len for the algorithm in question
    static $lengths;
    if (!isset($lengths[$algo])) { $lengths[$algo] = strlen(hash($algo, null, true)); }   
    $h_len = $lengths[$algo];
   
    if ($dk_len > (pow(2, 32) - 1) * $h_len) {
        return false; // derived key is too long
    } else {
        $l = ceil($dk_len / $h_len); // number of derived key blocks to compute
        $t = null;
        for ($i = 1; $i <= $l; $i++) {
            $f = $u = hash_hmac($algo, $s . pack('N', $i), $p, true); // first iterate
            for ($j = 1; $j < $c; $j++) {
                $f ^= ($u = hash_hmac($algo, $u, $p, true)); // xor each iterate
            }
            $t .= $f; // concatenate blocks of the derived key
        }
        return bin2hex(substr($t, 0, $dk_len)); // return the derived key of correct length
    }

}

//Yeah...php sucks because one can't do someFunc(x,y)["key"] --- so this is a way around that
function getvalue($array, $key) 
    {
    return $array[$key];
    }
?>
