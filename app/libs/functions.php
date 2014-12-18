<?php
function h($value) {
    if (is_array($value)) {
        return array_map("h", $value);
    } else {
        return htmlspecialchars(trim($value), ENT_QUOTES);
    }
}

function nl_br($string) {
    return str_replace("\n", "<br>", $string);
}

function is_ajax() {
    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest");
}

function cookie_on() {
    return (!SID && $_COOKIE);
}


function cookie($name, $value, $expire = 2592000) {

    $params = array(
        $name,
        $value,
        time() + $expire,
        str_replace("index.php", "", $_SERVER['PHP_SELF']),
        "",
        is_HTTPS()
    );
    if (version_compare(PHP_VERSION, '5.2.0') >= 0) {
        $params[] = true;
    }
    return call_user_func_array('setcookie', $params);
}


function is_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function is_url($url) {
    return filter_var($url, FILTER_VALIDATE_URL);
}

function is_ip($ip) {
    return filter_var($ip, FILTER_VALIDATE_IP);
}







