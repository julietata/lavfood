<?php

session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/controller/connection.php';

$query = "SELECT * FROM User WHERE Username = ?";
$prepared_statement = $conn->prepare($query);
$prepared_statement->bind_param("s", $_SESSION['logged_user']);
$prepared_statement->execute();
$result = $prepared_statement->get_result();

$data = $result->fetch_assoc();
$userroleid = $data['UserRoleID'];
$userid = $data['UserID'];
$last_id = isset($_POST['lastid']) ? $_POST['lastid'] : 0;
$limit = 6;

if ($userroleid == 4 || $userroleid == 5) {
    $query_trans = "SELECT * FROM transactionheader WHERE OrderType = 2 ORDER BY TransactionID ASC LIMIT ?, ?";
    $trans_statement = $conn->prepare($query_trans);
    $trans_statement->bind_param("ii", $last_id, $limit);
    $trans_statement->execute();
} else {
    $query_trans = "SELECT * FROM transactionheader WHERE (CustomerID = ? OR CookID = ? OR CourierID = ?) AND OrderType = 2 ORDER BY TransactionID ASC LIMIT ?, ?";
    $trans_statement = $conn->prepare($query_trans);
    $trans_statement->bind_param("iiiii", $userid, $userid, $userid, $last_id, $limit);
    $trans_statement->execute();
}

$trans_result = $trans_statement->get_result();

$order_history = array();
while ($row = $trans_result->fetch_assoc()) {

    $query_status = "SELECT * FROM transactionstatus WHERE TransactionID = ? ORDER BY `Date`";
    $status_statement = $conn->prepare($query_status);
    $status_statement->bind_param("i", $row['TransactionID']);
    $status_statement->execute();
    $status_result = $status_statement->get_result();

    $status_log = array();
    while ($status_row = $status_result->fetch_assoc()) {
        array_push($status_log, $status_row);
    }

    $query_detail = "SELECT p.PackageID, PackageName, Quantity FROM packagetransactiondetail AS ptd JOIN packageheader AS p ON p.PackageID = ptd.PackageID WHERE TransactionID = ?";
    $detail_statement = $conn->prepare($query_detail);
    $detail_statement->bind_param("i", $row['TransactionID']);
    $detail_statement->execute();
    $detail_result = $detail_statement->get_result();

    $details = array();
    while ($detail_row = $detail_result->fetch_assoc()) {
        array_push($details, $detail_row);
    }

    $order = [
        "transactionid" => $row['TransactionID'],
        "receivedate" => $row['ReceiveDate'],
        "price" => $row['TotalPrice'],
        "detail" => $details,
        "status" => $status_log,
        "currentstatus" => $status_log[sizeof($status_log) - 1]['Status'],
        "ordertype" => $row['OrderType']
    ];
    array_push($order_history, $order);
}

echo json_encode($order_history);

?>