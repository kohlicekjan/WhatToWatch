<?php

class Redirect {

    public static function to($url = null, $isRelative = true) {
        header('Version: HTTP/1.1');
        header('Cache-Control: private, no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: -1');
        header('X-Frame-Options: Deny');
        header('X-XSS-Protection: 0');
        $url= ($url==NULL ? self::currentUrl(!$isRelative): ($isRelative ? self::currentServer() : '').$url); 
        header('Location: ' . $url, true, 302);
        exit;
    }

    public static function refresh() {
        header('Location: ' . self::currentUrl(true), true, 302);
        exit;
    }

    public static function currentUrl($isAbsoluteUrl = false) {
        $url = '';
        if ($isAbsoluteUrl) {
            $url.=self::currentServer(false);
        }
        $url .= $_SERVER['REQUEST_URI'];
        return $url;
    }

    public static function currentServer($isRoot = true) {
        $url = (!empty($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
        $url.=$_SERVER['SERVER_NAME'];
        $url.=( $_SERVER['SERVER_PORT'] !== 80 ) ? ':' . $_SERVER['SERVER_PORT'] : '';
        $url.= $isRoot ? URL : '';
        return $url;
    }

}
