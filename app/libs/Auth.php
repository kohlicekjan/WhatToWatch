<?php

class Auth {

    public static function isLogin($role = null) {
        if (empty(Session::get('user'))) {
            return false;
        }
        if (!empty($role) and ! in_array($role, Session::get('user')['role'])) {
            return false;
        }
        return true;
    }

    public static function withLogin($role = null) {

        if (empty(Session::get('user'))) {
            Message::addInfo('Musíte se přihlásit.');
            Redirect::to('account/login/' . Hash::encodeUrl(Redirect::currentUrl(true)));
        }

        if (!empty($role) and ! in_array($role, Session::get('user')['role'])) {
            Message::addInfo('Nemáte práva.');
            Redirect::to('account/');
        }
    }

    public static function checkLogin() {

        if (empty(Session::get('user'))) {
            return;
        }
        $user = Session::get('user');
        if ($user['address'] != Hash::make(self::getIpAddress() . self::getBrowser())) {
            Session::delete('user');
            Session::destroy();
            Message::addWarning('Změnila se Ip adressa nebo prohlížeč.');
            Redirect::to('account/login/' . Hash::encodeUrl(Redirect::currentUrl(true)));
        }
        if ($user['expires'] < (time() - 3600)) {
            Session::delete('user');
            Session::destroy();
            Message::addInfo('Vypršel čas přihlášení.');
            Redirect::to('account/login/' . Hash::encodeUrl(Redirect::currentUrl(true)));
        } else {
            $_SESSION['user']['expires'] = time();
        }
    }

    public static function withoutLogin() {

        if (!empty(Session::get('user'))) {
            Redirect::to('account/');
        }
    }

    public static function login($data, $url = null) {

        $user['id'] = $data['id'];
        $user['name'] = $data['username'];
        $user['lastlogin'] = $data['last_login'];
        $user['role'] = explode(',', $data['role']);

        $user['address'] = Hash::make(self::getIpAddress() . self::getBrowser());
        $user['expires'] = time();
        Session::refresh();
        Session::delete('token');
        Session::put('user', $user);

        Message::addSuccess('Přihlášení proběhlo v pořádku.');
        Redirect::to(empty($url) ? 'account/' : Hash::decodeUrl($url), empty($url));
    }

    public static function logout($url = null) {
        Session::delete('user');
        Session::destroy();

        Message::addSuccess('Odhlášení proběhlo v pořádku.');
        Redirect::to(empty($url) ? 'account/login/' : Hash::decodeUrl($url), empty($url));
    }

    public static function generateToken() {
        if (Session::get('token') == null) {
            Session::put('token', self::randomKey());
        }
        return Session::get('token');
    }

    public static function checkToken($token) {
        if (Session::exists()) {
            return ($token == Session::get('token'));
        }
        return true;
    }

    public static function randomKey() {
        return md5(uniqid(time() . mt_rand(1, 1e9), true));
    }

    public static function randomPassword($size = 6) {
        $char = 'abcdefghijklmnopqrstuvwxyz123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password = '';
        for ($i = 0; $i < $size; $i++) {
            $password .= $char[mt_rand(0, strlen($char) - 1)];
        }
        return $password;
    }

    public static function getIpAddress() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            return $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            return $_SERVER['HTTP_FORWARDED'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        } else
            return 'UNKNOWN';
    }

    public static function getBrowser() {

        return $_SERVER['HTTP_USER_AGENT'];
    }

}
