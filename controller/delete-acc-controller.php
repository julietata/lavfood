<?php
    session_start();
    require $_SERVER['DOCUMENT_ROOT'].'/controller/connection.php';
    require $_SERVER['DOCUMENT_ROOT'].'/utils/validator.php';

    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['delete'])&& isset($_SESSION['profile_token']) && $_SESSION['profile_token'] == $_POST['token']) {
        $password = htmlspecialchars($_POST['pass']);

        $pass_val = check_pass_validation($password, $conn);
        if (check_empty($password)) {
            $_SESSION['delete_error'] = "Please enter your current password!";
        } else if (strcmp($pass_val, "") != 0) {
            $_SESSION['delete_error'] = $pass_val;
        } else {

            $query = "SELECT * FROM User WHERE Username = ?";
            $prepared_statement = $conn->prepare($query);
            $prepared_statement->bind_param("s", $_SESSION['logged_user']);
            $prepared_statement->execute();
            $result = $prepared_statement->get_result();

            $data = $result->fetch_assoc();
            $userid = $data['UserID'];
            $username = '%'.$data['Username'].'%';

            $query_check_transaction = "SELECT * FROM transactionheader WHERE CustomerID = ? OR CookID = ? OR CourierID = ?";
            $check_transaction_statement = $conn->prepare($query_check_transaction);
            $check_transaction_statement->bind_param("iii", $userid, $userid, $userid);
            $check_transaction_statement->execute();
            $trans_result = $check_transaction_statement->get_result();

            while ($trans_row = $trans_result->fetch_assoc()) {
                $query_status = "SELECT * FROM transactionstatus WHERE TransactionID = ? AND Status != 'Finish' ORDER BY Date DESC LIMIT 1";
                $status_statement = $conn->prepare($query_status);
                $status_statement->bind_param("i", $trans_row['TransactionID']);
                $status_statement->execute();
                $status_result = $status_statement->get_result();
                if ($status_result->num_rows > 0) {
                    $_SESSION['delete_error'] = "You have an ongoing transaction! Finish it before deleting your account!";
                }
            }

            $query_delete_user = "DELETE FROM User WHERE Username = ?";
            $prepared_statement = $conn->prepare($query_delete_user);
            $prepared_statement->bind_param("s", $_SESSION['logged_user']);
            $prepared_statement->execute();

            $query_delete_notif = "DELETE FROM Notification WHERE ReceiverID = ? OR NotificationMessage LIKE ?";
            $prepared_statement = $conn->prepare($query_delete_notif);
            $prepared_statement->bind_param("is", $userid, $username);
            $prepared_statement->execute();

            unset($_SESSION['logged_user']);
            session_destroy();

            if (isset($_COOKIE['remember_user'])) {
                unset($_COOKIE['remember_user']);
                setcookie('remember_user', null, -1, '/');
            }

            header("Location: /pages/auth/login.php");
            die();
        }
    } else {
        $_SESSION['delete_error'] = "Invalid Request";
    }
    
    header("Location: " . $_SERVER["HTTP_REFERER"]);

?>