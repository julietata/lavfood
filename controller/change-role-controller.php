<?php

    require $_SERVER['DOCUMENT_ROOT'].'/controller/connection.php';

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $newroleid = htmlspecialchars((int)$_POST['roles']);
        $userid = htmlspecialchars((int)$_POST['userid']);

        $query = "UPDATE User SET UserRoleID = ? WHERE UserID = ?";
        $prepared_statement = $conn->prepare($query);
        $prepared_statement->bind_param("ii", $newroleid, $userid);
        $prepared_statement->execute();
    } else {
        $_SESSION['change_role_error'] = "Invalid Request";
    }
    
    header("Location: " . $_SERVER["HTTP_REFERER"]);
