<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/controller/connection.php';

$query = "SELECT * FROM User WHERE Username = ?";
$prepared_statement = $conn->prepare($query);
$prepared_statement->bind_param("s", $_SESSION['logged_user']);
$prepared_statement->execute();
$result = $prepared_statement->get_result();
$data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $searched = isset($_POST['foodname']) ? htmlspecialchars($_POST['foodname']) : ""; 

    if ($data['UserRoleID'] == 4 || $data['UserRoleID'] == 5) {
        $query = "SELECT * FROM food WHERE FoodName LIKE ?";
        $parameter = "%".$searched."%";
        $prepared_statement = $conn->prepare($query);
        $prepared_statement->bind_param("s", $parameter);
        $prepared_statement->execute();
        $datas = $prepared_statement->get_result();

        $foods = array();
        while($row = $datas->fetch_assoc()) {
            array_push($foods, $row);
        }

        echo json_encode($foods);
        die();
    } else {
        $query = "SELECT * FROM food WHERE FoodName LIKE ? AND CreatedBy = ?";
        $parameter = "%".$searched."%";
        $prepared_statement = $conn->prepare($query);
        $prepared_statement->bind_param("si", $parameter, $data['UserID']);
        $prepared_statement->execute();
        $datas = $prepared_statement->get_result();

        $foods = array();
        while($row = $datas->fetch_assoc()) {
            array_push($foods, $row);
        }

        echo json_encode($foods);
        die();
    }
}

?>