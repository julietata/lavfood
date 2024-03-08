<?php

    session_start();
    require $_SERVER['DOCUMENT_ROOT'].'/controller/connection.php';

    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['notify_payment'])) {
        $transactionid = $_POST['transactionid'];
        $query = "INSERT INTO notification VALUES (null, ?, ?, ?)";
        $receiverid = 10;
        $status = "unseen";
        $message = $_SESSION['logged_user']." has sent you payment notification on order ".$transactionid;
        $prepared_statement = $conn->prepare($query);
        $prepared_statement->bind_param("iss", $receiverid, $message, $status);
        $prepared_statement->execute();
    }

    if ($_POST['ordertype'] == 1) {
        $_SESSION['transaction_active'] = "buffet";
    } else if ($_POST['ordertype'] == 2) {
        $_SESSION['transaction_active'] = "package";
    }

    header("Location: " . $_SERVER["HTTP_REFERER"]);

?>