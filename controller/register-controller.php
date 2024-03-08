<?php

require $_SERVER['DOCUMENT_ROOT'] . '/controller/connection.php';
require $_SERVER['DOCUMENT_ROOT'] . '/utils/validator.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['register']) && isset($_SESSION['register_token']) && $_SESSION['register_token'] == $_POST['token']) {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $address = htmlspecialchars($_POST['address']);
    $phonenumber = htmlspecialchars($_POST['phonenumber']);
    $birthdate = htmlspecialchars($_POST['birthdate']);
    $crunch_date = str_replace("-", "", $birthdate);
    $gender = isset($_POST['gender']) ? htmlspecialchars($_POST['gender']) : "";
    $password = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : $username . $crunch_date;
    $conpass = isset($_POST['confirmpassword']) ? htmlspecialchars($_POST['confirmpassword']) : $password;
    $role = isset($_POST['roles']) ? (int)$_POST['roles'] : 1;

    $username_val = check_username($username, $conn);
    $email_val = check_email($email);
    $password_val = check_password($password, $conpass);
    $address_val = check_address($address);
    $phonenumber_val = check_phone_number($phonenumber);

    if (check_empty($username) || check_empty($email) || check_empty($password) || check_empty($conpass) || check_empty($address) || check_empty($phonenumber) || check_empty($birthdate) || check_empty($gender) || $role == 0) {
        $_SESSION['error'] = "All fields must be filled!";
    } else if (strcmp($username_val, "") != 0) {
        $_SESSION['error'] = $username_val;
    } else if (strcmp($email_val, "") != 0) {
        $_SESSION['error'] = $email_val;
    } else if (strcmp($password_val, "") != 0) {
        $_SESSION['error'] = $password_val;
    } else if (strcmp($address_val, "") != 0) {
        $_SESSION['error'] = $address_val;
    } else if (strcmp($phonenumber_val, "") != 0) {
        $_SESSION['error'] = $phonenumber_val;
    } else {
        $password = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO User VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?)";
        $prepared_statement = $conn->prepare($query);
        $prepared_statement->bind_param("sssssssi", $username, $email, $password, $address, $phonenumber, $birthdate, $gender, $role);
        $prepared_statement->execute();
        if (!isset($_SESSION['logged_user'])) {
            header('Location: /pages/auth/login.php');
            die();
        }
    }
} else {
    $_SESSION['error'] = "Invalid Request";
}

header("Location: " . $_SERVER["HTTP_REFERER"]);
