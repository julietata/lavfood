<?php
    session_start();
    require $_SERVER['DOCUMENT_ROOT'].'/controller/connection.php';
    require $_SERVER['DOCUMENT_ROOT'].'/utils/validator.php';

    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['change_pass'])&& isset($_SESSION['profile_token']) && $_SESSION['profile_token'] == $_POST['token']) {
        $oldpass = htmlspecialchars($_POST['oldpass']);
        $newpass = htmlspecialchars($_POST['newpass']);
        $connewpass = htmlspecialchars($_POST['connewpass']);

        $password_val = check_password($newpass, $connewpass);
        $change_val = check_pass_validation($oldpass, $conn);
        if (check_empty($oldpass) || check_empty($newpass) || check_empty($connewpass)) {
            $_SESSION['changepass_error'] = "All fields must be filled!";
        } else if (strcmp($password_val, "") != 0) {
            $_SESSION['changepass_error'] = $password_val;
        } else if (strcmp($change_val, "") != 0) {
            $_SESSION['changepass_error'] = $change_val;
        } else {
            $newpass = password_hash($newpass, PASSWORD_BCRYPT);
            $query = "UPDATE User SET UserPassword = ? WHERE Username = ?";
            $prepared_statement = $conn->prepare($query);
            $prepared_statement->bind_param("ss", $newpass, $_SESSION['logged_user']);
            $prepared_statement->execute();
            $_SESSION['success'] = "Successfully Updated!";
        }
    } else {
        $_SESSION['changepass_error'] = "Invalid Request";
    }
    
    header("Location: " . $_SERVER["HTTP_REFERER"]);
?>