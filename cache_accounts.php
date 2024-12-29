<?php
session_start();

function cacheAccount($user) {
    $_SESSION['user'] = $user;
    $_SESSION['last_login'] = time();
}

function isSessionValid() {
    return isset($_SESSION['last_login']) && (time() - $_SESSION['last_login'] < 86400);
}
?>