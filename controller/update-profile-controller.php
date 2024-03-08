<?php
    session_start();
    require $_SERVER['DOCUMENT_ROOT'].'/controller/connection.php';
    require $_SERVER['DOCUMENT_ROOT'].'/utils/validator.php';

    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['update_profile']) && isset($_SESSION['profile_token']) && $_SESSION['profile_token'] == $_POST['token']) {
        
        $username = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : $_SESSION['logged_user'];
        $email = htmlspecialchars($_POST['email']);
        $address = htmlspecialchars($_POST['address']);
        $phonenumber = htmlspecialchars($_POST['phonenumber']);

        $username_val = check_username($username, $conn);
        $email_val = check_email($email);
        $address_val = check_address($address);
        $phonenumber_val = check_phone_number($phonenumber);

        if (check_empty($username) || check_empty($email) || check_empty($address) || check_empty($phonenumber)) {
            $_SESSION['update_error'] = "All fields must be filled!";
        } else if (strcmp($username_val, "") != 0) {
            $_SESSION['update_error'] = $username_val;
        } else if (strcmp($email_val, "") != 0) {
            $_SESSION['update_error'] = $email_val;
        } else if (strcmp($address_val, "") != 0) {
            $_SESSION['update_error'] = $address_val;
        } else if (strcmp($phonenumber_val, "") != 0) {
            $_SESSION['update_error'] = $phonenumber_val;
        } else {
            $query = "UPDATE User SET Username = ?, UserEmail = ?, UserAddress = ?, UserPhoneNumber = ? WHERE Username = ?";
            $prepared_statement = $conn->prepare($query);
            $prepared_statement->bind_param("sssss", $username, $email, $address, $phonenumber, $_SESSION['logged_user']);
            $prepared_statement->execute();
            $_SESSION['logged_user'] = $username;
            $_SESSION['success'] = "Successfully Updated!";
        }
    } else {
        $_SESSION['update_error'] = "Invalid Request";
    }
    
    header("Location: " . $_SERVER["HTTP_REFERER"]);

?>