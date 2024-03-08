<?php

if (isset($_COOKIE['remember_user'])) {
    unset($_COOKIE['remember_user']);
    setcookie('remember_user', null, -1, '/');
}

session_start();
unset($_SESSION['logged_user']);
session_destroy();

header("Location: /pages/auth/login.php");