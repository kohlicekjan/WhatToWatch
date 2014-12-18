<?php

class Session {

    private static $isStart = false;

    public static function start($name = DEFAULT_SESSION) {
        if (self::$isStart) {
            return;
        }

        ini_set("session.use_trans_sid", false);
        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set("session.gc_maxlifetime", 3600);
        ini_set("session.cache_expire", 180);
        if (!defined("SID")) {
            session_name($name);
            $params = array(0, URL, "", isset($_SERVER['HTTPS']));
            if (version_compare(PHP_VERSION, '5.2.0') >= 0) {
                $params[] = true;
            }
            call_user_func_array('session_set_cookie_params', $params);
            session_cache_limiter('private');

            session_start();
        }
        
        self::$isStart = true;
    }

    public static function put($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get($key) {
        self::start();
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return null;
    }
    
   // public static function pop($key){
   //  $value=$this->get($key);
   //        unset($_SESSION[$key]);
   //        return $value;
   // }

    public static function delete($key, $key2 = null) {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function destroy() {
        self::start();
        session_unset();
    }

    public static function refresh(){
        self::start();
        session_regenerate_id(true);
    }
    
    public static function exists(){
        self::start();
        return session_id();
    }
}
