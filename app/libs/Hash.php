<?php

class Hash {

    public static function make($value, $sold = null) {
        if (empty($sold)) {
            return hash('sha256', $value, false);
        } else {
            return hash_hmac("sha256", $value, $sold);
        }
    }

    public static function encodeUrl($value) {
        return base64_encode($value);
    }

    public static function decodeUrl($value) {
        return base64_decode($value);
    }

    public static function encrypt($value, $key = CRYPT_KEY) {
        return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $value, MCRYPT_MODE_ECB);
    }

    public static function decrypt($value, $key = CRYPT_KEY) {
        return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $value, MCRYPT_MODE_ECB);
    }

}
