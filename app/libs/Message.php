<?php

class Message {

    public static function getMessages($url = null) {
        $mg = [];
 
        if (!empty(Session::get('messages')['default'])) {
            $mg = Session::get('messages')['default'];
            Session::delete('messages', 'default');
        }
        if (!empty(Session::get('messages')[$url])) {
            $mg = array_merge($mg, Session::get('messages')[$url]);
            Session::delete('messages', $url);
        }

        return $mg;
    }

    public static function addError($text, $url = 'default') {
        $_SESSION['messages'][$url][] = ['state' => 'error', 'text' => $text];
    }

    public static function addInfo($text, $url = 'default') {
        $_SESSION['messages'][$url][] = ['state' => 'info', 'text' => $text];
    }

    public static function addSuccess($text, $url = 'default') {
        $_SESSION['messages'][$url][] = ['state' => 'success', 'text' => $text];
    }

    public static function addWarning($text, $url = 'default') {
        $_SESSION['messages'][$url][] = ['state' => 'warning', 'text' => $text];
    }

}
