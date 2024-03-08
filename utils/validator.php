<?php

require $_SERVER['DOCUMENT_ROOT'].'/controller/connection.php';

function check_empty($string): bool {
    return strlen($string) < 1;
}

function check_length($string, $min, $max): bool {
    return strlen($string) < $min || strlen($string) > $max;
}

function check_username($username, $conn): string {
    if (check_length($username, 5, 20)) {
        return "Username must be 5 - 20 characters!";
    }

    if (isset($_SESSION['logged_user'])) {
        if ($_SESSION['logged_user'] == $username) {
            return "";
        }
    }
    
    $query = "SELECT * FROM User WHERE Username = ?";
    $prepared_statement = $conn->prepare($query);
    $prepared_statement->bind_param("s", $username);
    $prepared_statement->execute();
    $result = $prepared_statement->get_result();

    if ($result->num_rows > 0) {
        return "Username must be unique!";
    }

    return "";
}

function check_email($email): string {
    if (substr($email, 0, 1) == '@' || substr($email, 0, 1) == '.' || substr_count($email, '@') > 1 || strpos($email, '.@') || strpos($email, '@.')) {
        return "Wrong Email format!";
    }
    return "";
}

function check_password($password, $conpass) {
    if (check_length($password, 9, 1000)) {
        return "Password must be more than 8 characters!";
    }
    if (!ctype_alnum($password)) {
        return "Password must be alphanumeric!";
    }
    if (strcmp($password, $conpass) != 0) {
        return "Confirm Password doesn't match!";
    }

    return "";
}

function check_address($address): string {
    if (substr(strrev($address), 0, 7) != strrev(" Street")) {
        return "Address must ends with 'Street'!";
    }
    return "";
}

function check_phone_number($phonenumber): string {
    if (check_length($phonenumber, 9, 14)) {
        return "Phone Number must be 10 - 15 characters!";
    }
    if (substr($phonenumber, 0, 1) != '8') {
        return "Wrong phone number format!";
    }
    return "";
}

function check_login($username, $password, $conn): string {
    $query = "SELECT * FROM User WHERE Username = ?";
    $prepared_statement = $conn->prepare($query);
    $prepared_statement->bind_param("s", $username);
    $prepared_statement->execute();
    $result = $prepared_statement->get_result();
    
    if ($result->num_rows <= 0) {
        return "Wrong Username or Password!";
    }

    $data = $result->fetch_assoc();
    $check_pass = password_verify($password, $data['UserPassword']);

    if ($check_pass == false) {
        return "Wrong Username or Password!";
    }

    return "";
}

function check_pass_validation($pass, $conn): string {
    $query = "SELECT * FROM User WHERE Username = ?";
    $prepared_statement = $conn->prepare($query);
    $prepared_statement->bind_param("s", $_SESSION['logged_user']);
    $prepared_statement->execute();
    $result = $prepared_statement->get_result();
    
    if ($result->num_rows <= 0) {
        return "Wrong Username or Password!";
    }

    $data = $result->fetch_assoc();
    $check_pass = password_verify($pass, $data['UserPassword']);

    if ($check_pass == false) {
        return "Current Password doesn't match!";
    }

    return "";
}