<?php

    session_start();
    require $_SERVER['DOCUMENT_ROOT'].'/controller/connection.php';
    require $_SERVER['DOCUMENT_ROOT'].'/utils/validator.php';

    $query = "SELECT * FROM User WHERE Username = ?";
    $prepared_statement = $conn->prepare($query);
    $prepared_statement->bind_param("s", $_SESSION['logged_user']);
    $prepared_statement->execute();
    $result = $prepared_statement->get_result();
    $data = $result->fetch_assoc();

    $cook_query = "SELECT * FROM User WHERE UserRoleID = ?";
    $cook_statement = $conn->prepare($cook_query);
    $role = 2;
    $cook_statement->bind_param("i", $role);
    $cook_statement->execute();
    $cook_result = $cook_statement->get_result();
    $cooks = array();

    while($row = $cook_result->fetch_assoc()) {
        array_push($cooks, $row);
    }

    $courier_query = "SELECT * FROM User WHERE UserRoleID = ?";
    $role = 3;
    $courier_statement = $conn->prepare($courier_query);
    $courier_statement->bind_param("i", $role);
    $courier_statement->execute();
    $courier_result = $courier_statement->get_result();
    $couriers = array();

    while($row = $courier_result->fetch_assoc()) {
        array_push($couriers, $row);
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $receivedate = htmlspecialchars($_POST['receivedate']);
        $delivery = isset($_POST['delivery']) ? htmlspecialchars((int)$_POST['delivery']) : 0;
        $menus = $_POST['chosen_menu_list'];
        $menus = json_decode($menus);
        $price = htmlspecialchars((int)$_POST['price']);

        if (check_empty($receivedate)) {
            $_SESSION['order_error'] = "Please choose receive date!";
        } else if ($delivery == 0) {
            $_SESSION['order_error'] = "Please choose delivery method!";
        } else if (sizeof($menus) == 0) {
            $_SESSION['order_error'] = "Please choose package you want to buy!";
        } else {
            if ($delivery == 1) {
                $header_query = "INSERT INTO transactionheader VALUES (null, ?, ?, ?, null, ?, ?, ?)";
                $header_statement = $conn->prepare($header_query);
                $cook = $cooks[rand(0, sizeof($cooks) - 1)];
                $courier = $couriers[rand(0, sizeof($couriers) - 1)];
                $order_type = 2;
                $header_statement->bind_param("iiisii", $data['UserID'], $cook['UserID'], $courier['UserID'], $receivedate, $order_type, $price);
                $header_statement->execute();
                $last_id = $conn->insert_id;

                $notif_cook_query = "INSERT INTO notification VALUES (null, ?, ?, ?)";
                $notif_cook_statement = $conn->prepare($notif_cook_query);
                $message = "You have been assign to handle a package order! Please check your history.";
                $status = "unseen";
                $notif_cook_statement->bind_param("iss", $cook['UserID'], $message, $status);
                $notif_cook_statement->execute();

                $notif_courier_query = "INSERT INTO notification VALUES (null, ?, ?, ?)";
                $notif_courier_statement = $conn->prepare($notif_courier_query);
                $message = "You have been assign to handle a package order! Please check your history.";
                $status = "unseen";
                $notif_courier_statement->bind_param("iss", $courier['UserID'], $message, $status);
                $notif_courier_statement->execute();

                $notif_admin_query = "INSERT INTO notification VALUES (null, ?, ?, ?)";
                $notif_admin_statement = $conn->prepare($notif_admin_query);
                $message = "There is a new order by ".$data['Username'];
                $status = "unseen";
                $id = 10;
                $notif_admin_statement->bind_param("iss", $id, $message, $status);
                $notif_admin_statement->execute();

            } else if ($delivery == 2) {
                $header_query = "INSERT INTO transactionheader VALUES (null, ?, ?, null, null, ?, ?, ?)";
                $header_statement = $conn->prepare($header_query);
                $cook = $cooks[rand(0, sizeof($cooks) - 1)];
                $order_type = 2;
                $header_statement->bind_param("iisii", $data['UserID'], $cook['UserID'], $receivedate, $order_type, $price);
                $header_statement->execute();
                $last_id = $conn->insert_id;

                $notif_cook_query = "INSERT INTO notification VALUES (null, ?, ?, ?)";
                $notif_cook_statement = $conn->prepare($notif_cook_query);
                $message = "You have been assign to handle a package order! Please check your history.";
                $status = "unseen";
                $notif_cook_statement->bind_param("iss", $cook['UserID'], $message, $status);

                $notif_admin_query = "INSERT INTO notification VALUES (null, ?, ?, ?)";
                $notif_admin_statement = $conn->prepare($notif_admin_query);
                $message = "There is a new order by ".$data['Username'];
                $status = "unseen";
                $id = 10;
                $notif_admin_statement->bind_param("iss", $id, $message, $status);
                $notif_admin_statement->execute();
            }
            $transaction_status = "Unpaid";

            $status_query = "INSERT INTO transactionstatus VALUES (null, ?, ?, null, ?)";
            $status_statement = $conn->prepare($status_query);
            $userid = (int)$data['UserID'];
            $status_statement->bind_param("isi", $last_id, $transaction_status, $userid);
            $status_statement->execute();

            foreach ($menus as $menu) {
                $menu = (array)$menu;
                $query_detail = "INSERT INTO packagetransactiondetail VALUES (?, ?, ?)";
                $detail_statement = $conn->prepare($query_detail);
                $packageid = (int)$menu['packageid'];
                $qty = (int)$menu['quantity'];
                $detail_statement->bind_param("iii", $last_id, $packageid, $qty);
                $detail_statement->execute();
            }
            header("Location: /pages/home.php");
            die();
        }
    } else {
        $_SESSION['order_error'] = "Invalid Request";
        die();
    }

    header("Location: " . $_SERVER["HTTP_REFERER"]);

?>