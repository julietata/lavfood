<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/controller/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_food']) && isset($_SESSION['manage_token']) && $_SESSION['manage_token'] == $_POST['token']) {
    $foodid = htmlspecialchars((int)$_POST['tobedeleted_foodid']);
    $query = "DELETE FROM Food WHERE FoodID = ?";
    $prepared_statement = $conn->prepare($query);
    $prepared_statement->bind_param("i", $foodid);
    $prepared_statement->execute();
}

$_SESSION['active'] = "food";
header("Location: " . $_SERVER["HTTP_REFERER"]);

?>