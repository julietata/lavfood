<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/controller/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_package']) && isset($_SESSION['manage_token']) && $_SESSION['manage_token'] == $_POST['token']) {
    $foodid = htmlspecialchars((int)$_POST['tobedeleted_packageid']);
    $query = "DELETE FROM packageheader WHERE PackageID = ?";
    $prepared_statement = $conn->prepare($query);
    $prepared_statement->bind_param("i", $foodid);
    $prepared_statement->execute();

    $query = "DELETE FROM packagedetail WHERE PackageID = ?";
    $prepared_statement = $conn->prepare($query);
    $prepared_statement->bind_param("i", $foodid);
    $prepared_statement->execute();
}

$_SESSION['active'] = "package";
header("Location: " . $_SERVER["HTTP_REFERER"]);

?>