<?php

require $_SERVER['DOCUMENT_ROOT'].'/controller/connection.php';
require $_SERVER['DOCUMENT_ROOT'].'/utils/validator.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login']) && isset($_SESSION['login_token']) && $_SESSION['login_token'] == $_POST['token']) {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    $remember_me = isset($_POST['remember_me']) ? true : false;

    $login_val = check_login($username, $password, $conn);

    if (check_empty($username) || check_empty($password)) {
        $_SESSION['error'] = "All fields must be filled!";
    } else if (strcmp($login_val, "") != 0) {
        $_SESSION['error'] = $login_val;
    } else {
        if ($remember_me) {
            setcookie("remember_user", $username, time() + (86400 * 30), "/");
        }
        $_SESSION['logged_user'] = $username;
        header('Location: /pages/home.php');
        die();
    }
} else {
    $_SESSION['error'] = "Invalid Request";
}

header("Location: " . $_SERVER["HTTP_REFERER"]);

